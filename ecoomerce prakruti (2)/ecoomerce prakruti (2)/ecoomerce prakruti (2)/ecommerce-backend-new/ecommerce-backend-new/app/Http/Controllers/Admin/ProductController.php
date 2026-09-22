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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use App\Services\InventoryService;

class ProductController extends Controller
{
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
        $this->removeGalleryImages($request->input('remove_gallery', []));
        $this->saveGallery($product, $request);

        if ($targetStock !== (int) $product->stock_qty) {
            $this->inventoryService->adjustStock($product, $targetStock, 'admin_product_update');
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->uploader->delete($product->image);
        foreach ($product->images as $img) {
            $this->uploader->delete($img->image);
        }
        $product->delete();

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

    public function importStore(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:2048']);

        $file = fopen($request->file('csv_file')->getRealPath(), 'r');
        $header = fgetcsv($file);
        $count = 0;

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);
            if (empty($data['name']) || empty($data['sku'])) {
                continue;
            }

            $category = Category::where('name', $data['category'] ?? '')->first();
            if (! $category) {
                continue;
            }

            $initialStock = max((int) ($data['stock_qty'] ?? 0), 0);

            $existing = Product::where('sku', $data['sku'])->first();
            if ($existing) {
                $existing->update([
                    'name' => $data['name'],
                    'slug' => $data['slug'] ?? Str::slug($data['name']),
                    'category_id' => $category->id,
                    'price' => $data['price'] ?? 0,
                    'sale_price' => $data['sale_price'] ?? null,
                    'unit' => $data['unit'] ?? 'pcs',
                    'status' => $data['status'] ?? 'active',
                ]);
                $this->inventoryService->adjustStock($existing, $initialStock, 'csv_import_stock');
            } else {
                $product = Product::create([
                    'sku' => $data['sku'],
                    'name' => $data['name'],
                    'slug' => $data['slug'] ?? Str::slug($data['name']),
                    'category_id' => $category->id,
                    'price' => $data['price'] ?? 0,
                    'sale_price' => $data['sale_price'] ?? null,
                    'stock_qty' => 0,
                    'unit' => $data['unit'] ?? 'pcs',
                    'status' => $data['status'] ?? 'active',
                ]);
                if ($initialStock > 0) {
                    $this->inventoryService->adjustStock($product, $initialStock, 'opening_stock');
                }
            }
            $count++;
        }
        fclose($file);

        return redirect()->route('admin.products.index')->with('success', "{$count} products imported successfully.");
    }

    public function toggleStatus(Product $product)
    {
        $product->update(['status' => $product->status === 'active' ? 'inactive' : 'active']);

        return back()->with('success', 'Product status updated.');
    }

    public function toggleFeatured(Product $product)
    {
        $product->update(['featured' => ! $product->featured]);

        return back()->with('success', 'Featured status updated.');
    }

    private function validateProduct(Request $request, ?int $id = null): array
    {
        $skuRule = 'nullable|string|max:100|unique:products,sku' . ($id ? ",{$id}" : '');
        $slugRule = 'nullable|string|max:255|unique:products,slug' . ($id ? ",{$id}" : '');

        return $request->validate([
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
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|image',
        ]);
    }

    private function saveGallery(Product $product, Request $request): void
    {
        if (! $request->hasFile('gallery')) {
            return;
        }

        $sort = $product->images()->max('sort_order') ?? 0;
        foreach ($this->uploader->uploadMany($request->file('gallery'), 'products/gallery') as $path) {
            $product->images()->create(['image' => $path, 'sort_order' => ++$sort]);
        }
    }

    private function removeGalleryImages(array $ids): void
    {
        foreach (ProductImage::whereIn('id', $ids)->get() as $img) {
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
