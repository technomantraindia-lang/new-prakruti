<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductImage;
use App\Models\ProductVariation;
use App\Services\ImageUploadService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Services\InventoryService;

class ProductController extends Controller
{
    private const PRODUCT_DETAIL_IMAGE_MIN = 1;
    private const PRODUCT_DETAIL_IMAGE_MAX = 7;

    public function __construct(
        private ImageUploadService $uploader,
        private InventoryService $inventoryService
    ) {}

    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand'])->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"));
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->paginate(15)->withQueryString();
        $categories = Category::where('status', 'active')->orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('admin.products.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);
        $packages = $this->normalizePackageRows($data['packages'] ?? []);
        unset($data['packages']);
        $data['product_information'] = $this->normalizeProductInformation($data['product_information'] ?? []);

        $this->applyDefaultPackageToProductData($data, $packages);

        $data['featured'] = $request->boolean('featured');
        $initialStock = (int) ($data['stock_qty'] ?? 0);
        $data['stock_qty'] = 0;

        if (empty($data['slug'])) {
            $data['slug'] = $this->uniqueSlug($data['name'], 'products', 'slug');
        }

        if (empty($data['sku'])) {
            $data['sku'] = $this->generateSku($data['name']);
        }

        if (empty($data['brand_id'])) {
            $data['brand_id'] = Brand::where('slug', 'prakruti-organic')->value('id') ?: Brand::where('status', 'active')->value('id');
        }

        if ($request->hasFile('image')) {
            $data['image'] = $this->uploader->upload($request->file('image'), 'products');
        }

        $product = Product::create($data);
        $this->syncPackageVariants($product, $packages);
        $this->saveGallery($product, $request);

        if ($initialStock > 0) {
            $this->inventoryService->adjustStock($product, $initialStock, 'opening_stock');
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'subCategory', 'subSubCategory', 'brand', 'images', 'inventoryLogs.user']);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['images', 'variations']);

        return view('admin.products.edit', array_merge(['product' => $product], $this->formData()));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, $product->id);
        $packages = $this->normalizePackageRows($data['packages'] ?? []);
        unset($data['packages']);
        $data['product_information'] = $this->normalizeProductInformation($data['product_information'] ?? []);

        $this->applyDefaultPackageToProductData($data, $packages);

        $data['featured'] = $request->boolean('featured');
        $targetStock = (int) ($data['stock_qty'] ?? 0);
        unset($data['stock_qty']);

        if (empty($data['slug'])) {
            $data['slug'] = $this->uniqueSlug($data['name'], 'products', 'slug', $product->id);
        }

        if ($request->hasFile('image')) {
            $this->uploader->delete($product->image);
            $data['image'] = $this->uploader->upload($request->file('image'), 'products');
        }

        $product->update($data);
        $this->syncPackageVariants($product, $packages);
        $this->removeGalleryImages($product, $request->input('remove_gallery', []));
        $this->saveGallery($product, $request);

        if ($targetStock !== (int) $product->stock_qty) {
            $this->inventoryService->adjustStock($product, $targetStock, 'admin_product_update');
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        try {
            $mainImage = $product->image;
            $galleryImages = $product->images()->pluck('image')->filter()->all();

            DB::transaction(function () use ($product) {
                $variationIds = $product->variations()->pluck('id');

                if (Schema::hasTable('order_items')) {
                    DB::table('order_items')
                        ->where('product_id', $product->id)
                        ->update(['product_id' => null]);

                    if ($variationIds->isNotEmpty() && Schema::hasColumn('order_items', 'var_id')) {
                        DB::table('order_items')
                            ->whereIn('var_id', $variationIds)
                            ->update(['var_id' => null]);
                    }
                }

                if (Schema::hasTable('return_items') && Schema::hasColumn('return_items', 'product_id')) {
                    DB::table('return_items')
                        ->where('product_id', $product->id)
                        ->update(['product_id' => null]);
                }

                if (Schema::hasTable('inquiries') && Schema::hasColumn('inquiries', 'product_id')) {
                    DB::table('inquiries')
                        ->where('product_id', $product->id)
                        ->update(['product_id' => null]);
                }

                $product->delete();
            });

            $this->uploader->delete($mainImage);
            foreach ($galleryImages as $image) {
                $this->uploader->delete($image);
            }
        } catch (QueryException) {
            return redirect()
                ->route('admin.products.index')
                ->with('error', 'This product could not be deleted because it is linked to protected records. Please remove linked records and try again.');
        }

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function bulkCreate()
    {
        return view('admin.products.bulk-create', $this->formData());
    }

    public function bulkStore(Request $request)
    {
        $request->validate(['products' => 'required|array|min:1']);

        $count = 0;
        DB::transaction(function () use ($request, &$count) {
            foreach ($request->products as $i => $row) {
                if (empty($row['name']) || empty($row['sku'])) {
                    continue;
                }

                $initialStock = max((int) ($row['stock_qty'] ?? 0), 0);
                $slug = $row['slug'] ?? Str::slug($row['name']);
                $data = [
                    'name' => $row['name'],
                    'slug' => $slug,
                    'sku' => $row['sku'],
                    'category_id' => $row['category_id'],
                    'price' => $row['price'] ?? 0,
                    'sale_price' => $row['sale_price'] ?? null,
                    'stock_qty' => 0,
                    'unit' => $row['unit'] ?? 'pcs',
                    'status' => $row['status'] ?? 'active',
                    'featured' => ! empty($row['featured']),
                ];

                $product = Product::create($data);
                if ($initialStock > 0) {
                    $this->inventoryService->adjustStock($product, $initialStock, 'opening_stock');
                }
                $count++;
            }
        });

        return redirect()->route('admin.products.index')->with('success', "{$count} products added successfully.");
    }

    public function importForm()
    {
        return view('admin.products.import');
    }

    public function downloadImportTemplate()
    {
        return response()->streamDownload(
            function () {
                $output = fopen('php://output', 'w');
                fputcsv($output, [
                    'name', 'slug', 'sku', 'category', 'brand', 'hsn_code', 'price', 'sale_price', 'cost_price',
                    'gst_percentage', 'stock_qty', 'low_stock_qty', 'unit', 'min_order_qty', 'weight', 'short_desc',
                    'description', 'nutritional_info', 'product_type', 'shelf_life', 'ingredient', 'packaging_type',
                    'storage', 'seo_title', 'seo_desc', 'seo_keywords', 'status', 'featured',
                    'package_1_label', 'package_1_price', 'package_1_sale_price', 'package_1_stock_qty', 'package_1_status',
                    'package_2_label', 'package_2_price', 'package_2_sale_price', 'package_2_stock_qty', 'package_2_status',
                ]);
                fputcsv($output, [
                    'Masoor Dal / Pink Lentil Split', 'masoor-dal-pink-lentil-split', 'PRK-CSV-001', 'Cereal & Pulses',
                    'Prakruti Organic', '', 120, 108, '', 5, 100, 5, 'packet', 1, 1,
                    'Soft, naturally processed dal', 'Clean split masoor dal for everyday cooking.', 'Energy: 343 kcal; Protein: 22g',
                    'Dal', '12 Months', 'Masoor Dal', 'Food Grade Standing Pouch', 'Store in a dry airtight container',
                    '', '', '', 'active', 1, '500g', 120, 108, 50, 'active', '1kg', 220, 198, 50, 'active',
                ]);
                fclose($output);
            },
            'prakruti-products-import-template.csv',
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    public function importStore(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:2048']);

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');
        if (! $file) {
            return back()->with('error', 'The CSV file could not be opened.');
        }

        $rawHeader = fgetcsv($file);
        $header = collect($rawHeader ?: [])->map(function ($column) {
            $column = preg_replace('/^\\xEF\\xBB\\xBF/', '', (string) $column);
            return Str::lower(trim($column));
        })->all();

        $requiredColumns = ['name', 'sku', 'category'];
        $missingColumns = array_values(array_diff($requiredColumns, $header));
        if ($missingColumns || count($header) !== count(array_unique($header))) {
            fclose($file);
            $message = $missingColumns
                ? 'CSV is missing required columns: ' . implode(', ', $missingColumns) . '.'
                : 'CSV contains duplicate column names. Please use the supplied template.';

            return back()->withInput()->with('error', $message);
        }

        $count = 0;
        $skipped = [];
        $rowNumber = 1;

        DB::transaction(function () use ($file, $header, &$count, &$skipped, &$rowNumber) {
            while (($row = fgetcsv($file)) !== false) {
                $rowNumber++;
                if (count($row) === 1 && trim((string) $row[0]) === '') {
                    continue;
                }

                if (count($row) !== count($header)) {
                    $skipped[] = "Row {$rowNumber}: column count does not match the header.";
                    continue;
                }

                $data = array_combine($header, $row);
                $name = trim((string) ($data['name'] ?? ''));
                $sku = trim((string) ($data['sku'] ?? ''));
                $category = $this->resolveImportCategory($data['category'] ?? '');

                if ($name === '' || $sku === '') {
                    $skipped[] = "Row {$rowNumber}: name and sku are required.";
                    continue;
                }

                if (! $category) {
                    $skipped[] = "Row {$rowNumber}: category '{$data['category']}' was not found.";
                    continue;
                }

                $price = $this->csvNumber($data['price'] ?? '', 0);
                $salePrice = $this->csvNumber($data['sale_price'] ?? '', null);
                $costPrice = $this->csvNumber($data['cost_price'] ?? '', null);
                $gst = $this->csvNumber($data['gst_percentage'] ?? '', $category->gst_percentage ?? 5);
                $stock = $this->csvInteger($data['stock_qty'] ?? '', 0);
                $lowStock = $this->csvInteger($data['low_stock_qty'] ?? '', 5);
                $minOrder = max($this->csvInteger($data['min_order_qty'] ?? '', 1), 1);
                $weight = $this->csvNumber($data['weight'] ?? '', null);
                $status = in_array(Str::lower(trim((string) ($data['status'] ?? 'active'))), ['active', 'inactive'], true)
                    ? Str::lower(trim((string) ($data['status'] ?? 'active')))
                    : 'active';
                $existing = Product::where('sku', $sku)->first();
                $packages = $this->packagesFromCsv($data, $price, $salePrice, $stock);
                $targetStock = $packages ? array_sum(array_column($packages, 'stock_qty')) : $stock;
                $slugInput = trim((string) ($data['slug'] ?? '')) ?: $name;
                $slug = $this->uniqueSlug($slugInput, 'products', 'slug', $existing?->id);
                $brand = $this->resolveImportBrand($data['brand'] ?? '');

                $payload = [
                    'name' => $name,
                    'slug' => $slug,
                    'sku' => $sku,
                    'category_id' => $category->id,
                    'brand_id' => $brand?->id,
                    'price' => $price,
                    'sale_price' => $salePrice,
                    'cost_price' => $costPrice,
                    'gst_percentage' => $gst,
                    'stock_qty' => 0,
                    'low_stock_qty' => $lowStock,
                    'unit' => trim((string) ($data['unit'] ?? 'packet')) ?: 'packet',
                    'min_order_qty' => $minOrder,
                    'weight' => $weight,
                    'hsn_code' => trim((string) ($data['hsn_code'] ?? '')) ?: null,
                    'short_desc' => $data['short_desc'] ?? null,
                    'description' => $data['description'] ?? null,
                    'nutritional_info' => $data['nutritional_info'] ?? null,
                    'product_information' => collect([
                        'product_type' => $data['product_type'] ?? null,
                        'shelf_life' => $data['shelf_life'] ?? null,
                        'ingredient' => $data['ingredient'] ?? null,
                        'packaging_type' => $data['packaging_type'] ?? null,
                        'storage' => $data['storage'] ?? null,
                    ])->map(fn ($value) => filled($value) ? trim((string) $value) : null)->filter()->all(),
                    'seo_title' => $data['seo_title'] ?? null,
                    'seo_desc' => $data['seo_desc'] ?? null,
                    'seo_keywords' => $data['seo_keywords'] ?? null,
                    'status' => $status,
                    'featured' => $this->csvBoolean($data['featured'] ?? false),
                ];

                if ($packages) {
                    $this->applyDefaultPackageToProductData($payload, $packages);
                    $payload['stock_qty'] = 0;
                }

                if ($existing) {
                    $existing->update($payload);
                    $product = $existing->fresh();
                } else {
                    $product = Product::create($payload);
                }

                if ($packages) {
                    $this->syncPackageVariants($product, $packages);
                }

                $this->inventoryService->adjustStock($product, $targetStock, 'csv_import_stock');
                $count++;
            }
        });
        fclose($file);

        $message = "{$count} product(s) imported successfully.";
        if ($skipped) {
            $message .= ' ' . count($skipped) . ' row(s) skipped.';
        }

        return redirect()->route('admin.products.index')
            ->with('success', $message)
            ->with('import_errors', array_slice($skipped, 0, 10));
    }

    private function resolveImportCategory(string $value): ?Category
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        return is_numeric($value)
            ? Category::find((int) $value)
            : Category::whereRaw('LOWER(name) = ?', [Str::lower($value)])
                ->orWhere('slug', Str::slug($value))
                ->first();
    }

    private function resolveImportBrand(string $value): ?Brand
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        return Brand::whereRaw('LOWER(name) = ?', [Str::lower($value)])
            ->orWhere('slug', Str::slug($value))
            ->first();
    }

    private function csvNumber($value, $default = null)
    {
        $value = trim((string) $value);
        return $value === '' ? $default : (is_numeric($value) ? (float) $value : $default);
    }

    private function csvInteger($value, int $default = 0): int
    {
        $value = trim((string) $value);
        return $value === '' || ! is_numeric($value) ? $default : max((int) $value, 0);
    }

    private function csvBoolean($value): bool
    {
        return in_array(Str::lower(trim((string) $value)), ['1', 'true', 'yes', 'y'], true);
    }

    private function packagesFromCsv(array $data, $defaultPrice, $defaultSalePrice, int $defaultStock): array
    {
        $packages = [];
        for ($index = 1; $index <= 10; $index++) {
            $label = trim((string) ($data["package_{$index}_label"] ?? ''));
            if ($label === '') {
                continue;
            }

            $packages[] = [
                'id' => null,
                'label' => $label,
                'price' => $this->csvNumber($data["package_{$index}_price"] ?? '', $defaultPrice),
                'sale_price' => $this->csvNumber($data["package_{$index}_sale_price"] ?? '', $defaultSalePrice),
                'stock_qty' => $this->csvInteger($data["package_{$index}_stock_qty"] ?? '', $defaultStock),
                'status' => in_array(Str::lower(trim((string) ($data["package_{$index}_status"] ?? 'active'))), ['active', 'inactive'], true)
                    ? Str::lower(trim((string) ($data["package_{$index}_status"] ?? 'active')))
                    : 'active',
            ];
        }

        return $packages;
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['status' => $product->status === 'active' ? 'inactive' : 'active']);

        return back()->with('success', 'Product status updated.');
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['featured' => ! $product->featured]);

        return back()->with('success', 'Best seller status updated.');
    }

    private function validateProduct(Request $request, ?int $id = null): array
    {
        $skuRule = 'nullable|string|max:100|unique:products,sku' . ($id ? ",{$id}" : '');
        $slugRule = 'nullable|string|max:255|unique:products,slug' . ($id ? ",{$id}" : '');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => $slugRule,
            'sku' => $skuRule,
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'hsn_code' => 'nullable|string|max:50',
            'stock_qty' => 'nullable|integer|min:0',
            'low_stock_qty' => 'nullable|integer|min:0',
            'unit' => 'nullable|string|max:20',
            'min_order_qty' => 'nullable|integer|min:1',
            'weight' => 'nullable|numeric|min:0',
            'packages' => 'required|array|min:1',
            'packages.*.id' => 'nullable|integer|exists:product_variations,id',
            'packages.*.label' => 'required|string|max:100|distinct',
            'packages.*.price' => 'required|numeric|min:0',
            'packages.*.sale_price' => 'nullable|numeric|min:0',
            'packages.*.stock_qty' => 'required|integer|min:0',
            'packages.*.status' => 'nullable|in:active,inactive',
            'short_desc' => 'nullable|string',
            'description' => 'nullable|string',
            'nutritional_info' => 'nullable|string',
            'product_information' => 'nullable|array',
            'product_information.product_type' => 'nullable|string|max:255',
            'product_information.shelf_life' => 'nullable|string|max:255',
            'product_information.ingredient' => 'nullable|string|max:255',
            'product_information.packaging_type' => 'nullable|string|max:255',
            'product_information.storage' => 'nullable|string|max:255',
            'seo_title' => 'nullable|string|max:255',
            'seo_desc' => 'nullable|string',
            'seo_keywords' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'featured' => 'nullable|boolean',
            'image' => 'nullable|image',
            'gallery' => 'nullable|array|max:' . self::PRODUCT_DETAIL_IMAGE_MAX,
            'gallery.*' => 'nullable|image',
            'remove_gallery' => 'nullable|array',
            'remove_gallery.*' => 'nullable|integer|exists:product_images,id',
        ]);

        $validator->after(function ($validator) use ($request, $id) {
            $newGalleryCount = collect($request->file('gallery', []))->filter()->count();
            $removeGalleryIds = collect($request->input('remove_gallery', []))
                ->filter()
                ->map(fn ($value) => (int) $value)
                ->unique()
                ->values();

            $existingGalleryCount = $id ? ProductImage::where('product_id', $id)->count() : 0;
            $validRemoveCount = ($id && $removeGalleryIds->isNotEmpty())
                ? ProductImage::where('product_id', $id)->whereIn('id', $removeGalleryIds)->count()
                : 0;

            $totalGalleryCount = $existingGalleryCount - $validRemoveCount + $newGalleryCount;

            if ($totalGalleryCount < self::PRODUCT_DETAIL_IMAGE_MIN) {
                $validator->errors()->add('gallery', 'Please add at least 1 product detail photo.');
            }

            if ($totalGalleryCount > self::PRODUCT_DETAIL_IMAGE_MAX) {
                $validator->errors()->add('gallery', 'You can keep a maximum of 7 product detail photos. Please remove extra photos before saving.');
            }
        });

        return $validator->validate();
    }

    private function saveGallery(Product $product, Request $request): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        $remainingSlots = max(0, self::PRODUCT_DETAIL_IMAGE_MAX - $product->images()->count());
        $files = array_slice($request->file('gallery'), 0, $remainingSlots);

        if (! $files) {
            return;
        }

        $sort = $product->images()->max('sort_order') ?? 0;
        foreach ($this->uploader->uploadMany($files, 'products/gallery') as $path) {
            $product->images()->create(['image' => $path, 'sort_order' => ++$sort]);
        }
    }

    private function removeGalleryImages(Product $product, array $ids): void
    {
        foreach ($product->images()->whereIn('id', $ids)->get() as $img) {
            $this->uploader->delete($img->image);
            $img->delete();
        }
    }

    private function formData(): array
    {
        return [
            'categories' => Category::where('status', 'active')->orderBy('name')->get(),
            'brands' => Brand::where('status', 'active')->orderBy('name')->get(),
            'units' => ['pcs', 'kg', 'g', 'box', 'packet', 'litre', 'ml'],
        ];
    }

    private function normalizePackageRows(array $rows): array
    {
        $packages = [];

        foreach ($rows as $row) {
            $label = trim((string) ($row['label'] ?? ''));

            if ($label === '') {
                continue;
            }

            $packages[] = [
                'id' => $row['id'] ?? null,
                'label' => $label,
                'price' => (float) ($row['price'] ?? 0),
                'sale_price' => isset($row['sale_price']) && $row['sale_price'] !== '' ? (float) $row['sale_price'] : null,
                'stock_qty' => max((int) ($row['stock_qty'] ?? 0), 0),
                'status' => $row['status'] ?? 'active',
            ];
        }

        return $packages;
    }

    private function normalizeProductInformation(array $info): array
    {
        return collect([
            'product_type' => $info['product_type'] ?? null,
            'shelf_life' => $info['shelf_life'] ?? null,
            'ingredient' => $info['ingredient'] ?? null,
            'packaging_type' => $info['packaging_type'] ?? null,
            'storage' => $info['storage'] ?? null,
        ])->map(fn ($value) => filled($value) ? trim((string) $value) : null)
            ->filter()
            ->all();
    }

    private function applyDefaultPackageToProductData(array &$data, array $packages): void
    {
        $defaultPackage = collect($packages)->firstWhere('status', 'active') ?? ($packages[0] ?? null);

        if (! $defaultPackage) {
            return;
        }

        $data['price'] = $defaultPackage['price'];
        $data['sale_price'] = $defaultPackage['sale_price'];
        $data['stock_qty'] = collect($packages)->sum('stock_qty');
        $data['unit'] = 'packet';
    }

    private function syncPackageVariants(Product $product, array $packages): void
    {
        $weightAttribute = ProductAttribute::firstOrCreate(
            ['name' => 'Weight'],
            ['status' => 'active']
        );

        $keptVariationIds = [];

        foreach ($packages as $package) {
            $variation = ! empty($package['id'])
                ? $product->variations()->whereKey($package['id'])->first()
                : null;

            if (! $variation) {
                $variation = $product->variations()
                    ->where('attr_id', $weightAttribute->id)
                    ->where('attr_val', $package['label'])
                    ->first();
            }

            $targetStock = $package['stock_qty'];
            $payload = [
                'product_id' => $product->id,
                'attr_id' => $weightAttribute->id,
                'attr_val' => $package['label'],
                'sku' => $this->generateVariationSku($product, $package['label'], $variation?->id),
                'price' => $package['price'],
                'sale_price' => $package['sale_price'],
                'status' => $package['status'],
            ];

            if ($variation) {
                $variation->update($payload);
            } else {
                $payload['stock_qty'] = 0;
                $variation = ProductVariation::create($payload);
            }

            if ($targetStock !== (int) $variation->stock_qty) {
                $this->inventoryService->adjustStock($variation, $targetStock, 'admin_package_update');
            }

            $keptVariationIds[] = $variation->id;
        }

        $product->variations()
            ->when($keptVariationIds, fn ($query) => $query->whereNotIn('id', $keptVariationIds))
            ->delete();
    }

    private function generateVariationSku(Product $product, string $label, ?int $ignoreId = null): string
    {
        $base = trim($product->sku . '-' . strtoupper(preg_replace('/[^A-Za-z0-9]+/', '', $label)), '-');
        $base = $base ?: 'PRK-PACKAGE';
        $sku = $base;
        $counter = 2;

        while (
            ProductVariation::where('sku', $sku)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $sku = $base . '-' . $counter++;
        }

        return $sku;
    }

    private function uniqueSlug(string $value, string $table, string $column, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'product';
        $slug = $base;
        $counter = 2;

        while (
            DB::table($table)
                ->where($column, $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    private function generateSku(string $name): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3)) ?: 'PRK';

        do {
            $sku = 'PRK-' . $prefix . '-' . random_int(1000, 9999);
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }
}
