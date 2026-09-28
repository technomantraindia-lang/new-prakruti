@extends('admin.layouts.app')

@section('title', 'Import Products')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
    <div>
        <h2 class="mb-1">Import Products (CSV)</h2>
        <p class="text-muted mb-0">Import the same core product, package, pricing, stock, and detail information used by the Add Product form.</p>
    </div>
    <a href="{{ route('admin.products.importTemplate') }}" class="btn btn-outline-primary">
        <i class="fas fa-download"></i> Download CSV Template
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Upload CSV</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    Required columns are <strong>name</strong>, <strong>sku</strong>, and <strong>category</strong>.
                    Existing products are updated by SKU. Categories and brands may be entered by name or slug.
                </div>

                <form action="{{ route('admin.products.importStore') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">CSV File *</label>
                        <input type="file" name="csv_file" class="form-control @error('csv_file') is-invalid @enderror" accept=".csv,.txt" required>
                        @error('csv_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Maximum file size: 2 MB. Use UTF-8 CSV and keep the first row as the column header.</div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-file-import"></i> Import Products</button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h5 class="mb-0">Important notes</h5>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Use one row per product. Add package options with <code>package_1_...</code>, <code>package_2_...</code>, up to 10 packages.</li>
                    <li>If package columns are provided, their stock is synced to both the package variation and product stock.</li>
                    <li>If no package columns are provided, the legacy product-level <code>price</code> and <code>stock_qty</code> values are used.</li>
                    <li>CSV import does not upload image files. Add the main image and 1–7 detail photos from the product edit page after import.</li>
                    <li>If a row cannot be imported, the valid rows are saved and the skipped row numbers are shown after redirect.</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">CSV columns</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2"><strong>Required:</strong> name, sku, category</p>
                <p class="small text-muted mb-2"><strong>Product:</strong> slug, brand, hsn_code, price, sale_price, cost_price, gst_percentage, stock_qty, low_stock_qty, unit, min_order_qty, weight, status, featured</p>
                <p class="small text-muted mb-0"><strong>Frontend details:</strong> short_desc, description, nutritional_info, product_type, shelf_life, ingredient, packaging_type, storage, seo_title, seo_desc, seo_keywords</p>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <h5 class="mb-0">Package columns</h5>
            </div>
            <div class="card-body">
                <code class="d-block small">package_1_label</code>
                <code class="d-block small">package_1_price</code>
                <code class="d-block small">package_1_sale_price</code>
                <code class="d-block small">package_1_stock_qty</code>
                <code class="d-block small">package_1_status</code>
                <p class="small text-muted mt-2 mb-0">Repeat the same five columns for package_2, package_3, and so on.</p>
            </div>
        </div>
    </div>
</div>
@endsection
