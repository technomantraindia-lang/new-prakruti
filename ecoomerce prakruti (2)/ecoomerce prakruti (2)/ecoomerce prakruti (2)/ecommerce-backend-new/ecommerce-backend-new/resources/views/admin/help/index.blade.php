@extends('admin.layouts.app')

@section('title', 'Admin Help & Guide')

@section('content')
<div class="help-hero rounded-4 p-4 p-lg-5 mb-4 text-white">
    <div class="d-flex flex-wrap justify-content-between align-items-start gap-4">
        <div>
            <span class="badge bg-white text-success mb-3">Prakruti Organic Admin</span>
            <h1 class="display-6 fw-bold mb-2">Admin Help & Guide</h1>
            <p class="mb-0 opacity-75">Use this page to understand what each admin module does, where information appears, and the safest order for daily store operations.</p>
        </div>
        <div class="help-hero-icon"><i class="fas fa-circle-question"></i></div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row g-3 align-items-center">
            <div class="col-lg-8">
                <label for="helpSearch" class="form-label fw-semibold">Search this guide</label>
                <input id="helpSearch" type="search" class="form-control form-control-lg" placeholder="Try: CSV, banner, order, stock, review...">
            </div>
            <div class="col-lg-4">
                <div class="help-tip-box">
                    <i class="fas fa-lightbulb text-warning me-2"></i>
                    <span>Open Help anytime from the sidebar or the top-right Help button.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="helpNoResults" class="alert alert-warning d-none">No help topic matched your search. Try a shorter word such as <strong>product</strong>, <strong>order</strong>, or <strong>image</strong>.</div>

<div class="row g-4" id="helpSections">
    <div class="col-12 help-section" data-help-content="dashboard overview kpi alerts recent orders low stock">
        <section class="card shadow-sm help-card" id="dashboard">
            <div class="card-header help-card-header"><span class="help-number">1</span><div><h4 class="mb-1">Dashboard</h4><p class="mb-0 text-muted">The daily overview of store health and activity.</p></div><i class="fas fa-chart-line ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Review total sales, orders, customers, pending orders, completed orders, and today’s activity.</li>
                    <li>Check low-stock alerts before processing new orders.</li>
                    <li>Use Recent Orders to open an order quickly and use notification counts to find pending work.</li>
                    <li>This page is for monitoring; product, order, customer, and setting changes happen in their dedicated modules.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-12 help-section" data-help-content="search global search find product order customer inquiry">
        <section class="card shadow-sm help-card" id="search">
            <div class="card-header help-card-header"><span class="help-number">2</span><div><h4 class="mb-1">Global Search</h4><p class="mb-0 text-muted">Find records across the admin panel.</p></div><i class="fas fa-search ms-auto text-success"></i></div>
            <div class="card-body">
                <p>Use the search box in the admin header to locate products, orders, customers, or inquiries. Open the result and continue editing from the relevant module.</p>
                <div class="alert alert-light border mb-0"><strong>Tip:</strong> Search with an exact SKU, order number, customer phone/email, or a distinctive product name for the fastest result.</div>
            </div>
        </section>
    </div>

    <div class="col-12 help-section" data-help-content="products product add edit delete status featured best sellers package variation stock image gallery csv bulk import">
        <section class="card shadow-sm help-card" id="products">
            <div class="card-header help-card-header"><span class="help-number">3</span><div><h4 class="mb-1">Products</h4><p class="mb-0 text-muted">Create the products shown in categories, product cards, best sellers, cart, and product details.</p></div><i class="fas fa-boxes ms-auto text-success"></i></div>
            <div class="card-body">
                <h6>Product list</h6>
                <ul>
                    <li>Search by name or SKU and filter by category or active/inactive status.</li>
                    <li>Use View for the admin product summary, Edit for full product data, and the status button to hide/show a product.</li>
                    <li>Use Best Seller to control whether the product appears in the homepage Best Sellers section.</li>
                </ul>
                <h6>Add/Edit Product</h6>
                <ul>
                    <li>Enter the product name and category, then add one or more package sizes such as 500g or 1kg.</li>
                    <li>Each package can have its own regular price, sale price, stock quantity, and active/inactive status.</li>
                    <li>Product stock is synchronized from package stock. The first active package is used for the main card price.</li>
                    <li>Fill short description, full description, nutritional information, product information, status, and featured visibility for the storefront.</li>
                    <li>Upload one main product image and 1–7 product detail photos. Detail photos become the thumbnails on the product page.</li>
                </ul>
                <h6>Bulk Add and CSV Import</h6>
                <ul>
                    <li><strong>Bulk Add</strong> is for quickly entering basic products in the browser.</li>
                    <li><strong>CSV Import</strong> is for larger or more complete imports. Required columns are <code>name</code>, <code>sku</code>, and <code>category</code>.</li>
                    <li>Existing products are updated by SKU. Categories and brands can be entered by name or slug.</li>
                    <li>Use <strong>Download CSV Template</strong> on the CSV page. Package columns follow the pattern <code>package_1_label</code>, <code>package_1_price</code>, <code>package_1_stock_qty</code>, and can be repeated up to package 10.</li>
                    <li>CSV cannot upload local image files. Add images from Edit after importing.</li>
                </ul>
                <h6>Product variations</h6>
                <p class="mb-0">Variations represent package choices. Keep variation labels clear, keep prices and stock accurate, and deactivate a package instead of deleting it when it should no longer be sold.</p>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="categories category organize products gst seo image">
        <section class="card shadow-sm help-card h-100" id="categories">
            <div class="card-header help-card-header"><span class="help-number">4</span><div><h4 class="mb-1">Categories</h4><p class="mb-0 text-muted">Organize products and control category browsing.</p></div><i class="fas fa-sitemap ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Create or edit the category name and slug used by storefront navigation.</li>
                    <li>Set status to control whether customers can browse the category.</li>
                    <li>Add category image, description, GST, and SEO information where available.</li>
                    <li>Update a category before assigning new products so product filters remain consistent.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="banners home banner carousel drag drop mobile image link url preview delete">
        <section class="card shadow-sm help-card h-100" id="banners">
            <div class="card-header help-card-header"><span class="help-number">5</span><div><h4 class="mb-1">Home Banners</h4><p class="mb-0 text-muted">Manage the homepage banner carousel.</p></div><i class="fas fa-images ms-auto text-success"></i></div>
            <div class="card-body">
                <ul>
                    <li>Upload one or multiple banner images. The current image and selected upload previews are shown in Admin.</li>
                    <li>Use the optional Link URL for a hash such as <code>#categories</code> or a full URL.</li>
                    <li>The complete banner image is clickable when a link is configured. There is no text CTA button on the image.</li>
                    <li>Drag rows into the required order and click <strong>Save Banner Order</strong>.</li>
                    <li>For mobile, keep important text/product content near the center and use a wide banner composition. Recommended size is 1920 × 720 px.</li>
                    <li>If a stored image is missing, Admin shows an unavailable-image message and the storefront falls back instead of showing a broken banner.</li>
                </ul>
                <p class="mb-0"><strong>Safe delete:</strong> confirm the image is no longer needed before deleting; deletion removes the banner record and stored banner file.</p>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="farm gallery gallery category video image">
        <section class="card shadow-sm help-card h-100" id="farm-gallery">
            <div class="card-header help-card-header"><span class="help-number">6</span><div><h4 class="mb-1">Farm Gallery</h4><p class="mb-0 text-muted">Manage farm, process, and brand story media.</p></div><i class="fas fa-seedling ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Use Farm Gallery Categories to create the filters used by the gallery.</li>
                    <li>Use Farm Gallery to add or edit gallery items, assign categories, set captions/status, and manage photos or videos.</li>
                    <li>Keep gallery categories short and consistent so the storefront filter remains easy to use.</li>
                    <li>Deactivate media temporarily when it should be hidden without deleting the file.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="orders order status payment verify invoice bulk action shipment customer">
        <section class="card shadow-sm help-card h-100" id="orders">
            <div class="card-header help-card-header"><span class="help-number">7</span><div><h4 class="mb-1">Orders</h4><p class="mb-0 text-muted">Process customer orders from payment review through fulfillment.</p></div><i class="fas fa-shopping-cart ms-auto text-success"></i></div>
            <div class="card-body">
                <ol class="mb-3">
                    <li>Open the order and confirm customer, delivery address, items, quantity, totals, and payment method.</li>
                    <li>For manual/offline payment, verify the payment only after checking the proof and transaction details.</li>
                    <li>Update status through the normal fulfillment flow: pending → processing → packed → shipped → delivered.</li>
                    <li>Use the invoice action to view or print the order invoice.</li>
                </ol>
                <ul class="mb-0">
                    <li>Use filters and bulk actions for repeated status updates.</li>
                    <li>Do not mark an order delivered before dispatch and delivery confirmation.</li>
                    <li>Refunds, cancellations, and stock effects should be checked before changing a completed order.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="customers customer account orders address">
        <section class="card shadow-sm help-card h-100" id="customers">
            <div class="card-header help-card-header"><span class="help-number">8</span><div><h4 class="mb-1">Customers</h4><p class="mb-0 text-muted">Review customer accounts and order history.</p></div><i class="fas fa-users ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Search the customer list and open a customer to review contact information, addresses, and related orders.</li>
                    <li>Use customer information only for support and fulfillment.</li>
                    <li>Order changes must be made from Orders so inventory and status history remain consistent.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="shipping delivery charge free shipping method">
        <section class="card shadow-sm help-card h-100" id="shipping">
            <div class="card-header help-card-header"><span class="help-number">9</span><div><h4 class="mb-1">Shipping</h4><p class="mb-0 text-muted">Configure delivery methods and shipping charges.</p></div><i class="fas fa-truck ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Create or edit a shipping method, charge, availability, and minimum order value for free shipping where supported.</li>
                    <li>Keep only the methods currently offered to customers active.</li>
                    <li>Test checkout after changing shipping charges or thresholds.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="payments payment transaction method status">
        <section class="card shadow-sm help-card h-100" id="payments">
            <div class="card-header help-card-header"><span class="help-number">10</span><div><h4 class="mb-1">Payments</h4><p class="mb-0 text-muted">Review payment records linked to orders.</p></div><i class="fas fa-credit-card ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Open a payment to inspect method, amount, status, transaction reference, and linked order.</li>
                    <li>Payment verification should be performed from the order workflow when a manual verification action is required.</li>
                    <li>Never treat a payment screenshot alone as proof of settlement without checking the transaction reference or provider status.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="inquiries contact message status note">
        <section class="card shadow-sm help-card h-100" id="inquiries">
            <div class="card-header help-card-header"><span class="help-number">11</span><div><h4 class="mb-1">Inquiries</h4><p class="mb-0 text-muted">Handle contact and product questions from customers.</p></div><i class="fas fa-comments ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Open an inquiry to see the customer message, contact details, and related product if supplied.</li>
                    <li>Update status as the request moves from pending to contacted and closed.</li>
                    <li>Add an internal note so the next admin can understand what was promised or resolved.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="consultations consultation request status">
        <section class="card shadow-sm help-card h-100" id="consultations">
            <div class="card-header help-card-header"><span class="help-number">12</span><div><h4 class="mb-1">Consultations</h4><p class="mb-0 text-muted">Manage consultation requests submitted from the storefront.</p></div><i class="fas fa-user-md ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Review the request details and customer contact information.</li>
                    <li>Open the record for the full request and update it after follow-up.</li>
                    <li>Use the status filter to separate new requests from completed follow-ups.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="reviews review testimonial approve moderate product">
        <section class="card shadow-sm help-card h-100" id="reviews">
            <div class="card-header help-card-header"><span class="help-number">13</span><div><h4 class="mb-1">Reviews & Testimonials</h4><p class="mb-0 text-muted">Moderate customer feedback before it appears publicly.</p></div><i class="fas fa-star ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Review product reviews and testimonials separately.</li>
                    <li>Approve useful, genuine feedback and keep inappropriate or irrelevant content hidden.</li>
                    <li>Use the product review status and testimonial status actions provided on the page.</li>
                    <li>Approved product reviews contribute to the storefront product rating and review count.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="settings setting store configuration">
        <section class="card shadow-sm help-card h-100" id="settings">
            <div class="card-header help-card-header"><span class="help-number">14</span><div><h4 class="mb-1">Settings</h4><p class="mb-0 text-muted">Maintain store-wide configuration values.</p></div><i class="fas fa-cog ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Review each setting before saving; settings can affect storefront content, checkout, communication, and operations.</li>
                    <li>Change one group at a time and test the affected page afterward.</li>
                    <li>Do not paste passwords, payment secrets, or API keys into fields that are not explicitly intended for them.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="notifications notification alerts read delete">
        <section class="card shadow-sm help-card h-100" id="notifications">
            <div class="card-header help-card-header"><span class="help-number">15</span><div><h4 class="mb-1">Notifications</h4><p class="mb-0 text-muted">Track operational alerts and mark them handled.</p></div><i class="fas fa-bell ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Open notifications from the bell menu or notification page.</li>
                    <li>Use Read All after reviewing the alerts; delete only notifications that are no longer useful.</li>
                    <li>Pending orders, inquiries, reviews, consultations, and low-stock conditions may also appear as dashboard/sidebar indicators.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="system health server database storage queue">
        <section class="card shadow-sm help-card h-100" id="system-health">
            <div class="card-header help-card-header"><span class="help-number">16</span><div><h4 class="mb-1">System Health</h4><p class="mb-0 text-muted">Check the technical health of the backend.</p></div><i class="fas fa-heart-pulse ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Use this page to inspect application, database, storage, cache, and queue health indicators.</li>
                    <li>If a check fails, record the exact message and time before asking technical support to investigate.</li>
                    <li>Do not repeatedly retry a failing operation without checking the underlying error.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-md-6 help-section" data-help-content="failed jobs queue retry error">
        <section class="card shadow-sm help-card h-100" id="failed-jobs">
            <div class="card-header help-card-header"><span class="help-number">17</span><div><h4 class="mb-1">Failed Jobs</h4><p class="mb-0 text-muted">Review background tasks that did not complete.</p></div><i class="fas fa-triangle-exclamation ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Open a failed job to inspect its error context.</li>
                    <li>Retry only after confirming the cause is temporary or has been fixed.</li>
                    <li>Use Retry All carefully because it can re-run multiple background operations.</li>
                    <li>Delete a failed job only after its information is no longer required for troubleshooting.</li>
                </ul>
            </div>
        </section>
    </div>

    <div class="col-12 help-section" data-help-content="backups backup database download create delete restore">
        <section class="card shadow-sm help-card" id="backups">
            <div class="card-header help-card-header"><span class="help-number">18</span><div><h4 class="mb-1">Backups</h4><p class="mb-0 text-muted">Create and manage database backups.</p></div><i class="fas fa-database ms-auto text-success"></i></div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Create a backup before major product, settings, migration, or operational changes.</li>
                    <li>Download important backups to a secure location outside the server.</li>
                    <li>Delete old backups only after confirming another verified copy exists.</li>
                    <li>Backup creation does not replace testing; verify that the file was generated and has a sensible timestamp.</li>
                </ul>
            </div>
        </section>
    </div>
</div>

<section class="card shadow-sm mt-4">
    <div class="card-header help-card-header"><span class="help-number"><i class="fas fa-route"></i></span><div><h4 class="mb-1">Recommended daily workflow</h4><p class="mb-0 text-muted">A simple order for routine store administration.</p></div></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3"><div class="workflow-step"><strong>1. Check Dashboard</strong><span>Review alerts, new orders, inquiries, and low stock.</span></div></div>
            <div class="col-md-3"><div class="workflow-step"><strong>2. Process Orders</strong><span>Verify payment, update fulfillment status, and print invoices.</span></div></div>
            <div class="col-md-3"><div class="workflow-step"><strong>3. Maintain Catalog</strong><span>Update products, package stock, categories, banners, and gallery media.</span></div></div>
            <div class="col-md-3"><div class="workflow-step"><strong>4. Close the Loop</strong><span>Answer inquiries, consultations, and moderate reviews.</span></div></div>
        </div>
    </div>
</section>

<section class="card shadow-sm mt-4">
    <div class="card-header help-card-header"><span class="help-number"><i class="fas fa-shield-halved"></i></span><div><h4 class="mb-1">Before changing important data</h4><p class="mb-0 text-muted">Use these checks to prevent accidental store problems.</p></div></div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4"><div class="help-safety"><i class="fas fa-copy"></i><span>Create a backup before bulk imports or system changes.</span></div></div>
            <div class="col-md-4"><div class="help-safety"><i class="fas fa-eye"></i><span>Preview images and verify the storefront after content changes.</span></div></div>
            <div class="col-md-4"><div class="help-safety"><i class="fas fa-check-double"></i><span>Confirm prices, stock, status, and links before saving.</span></div></div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .help-hero {
        background: linear-gradient(135deg, #2d5a27 0%, #193a15 72%, #102a13 100%);
        box-shadow: 0 16px 35px rgba(45, 90, 39, .18);
    }
    .help-hero-icon {
        width: 76px;
        height: 76px;
        border: 1px solid rgba(255,255,255,.35);
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        color: #f3edd7;
        background: rgba(255,255,255,.1);
    }
    .help-tip-box {
        height: 100%;
        min-height: 66px;
        display: flex;
        align-items: center;
        padding: 14px 16px;
        border: 1px solid #e3ddcf;
        border-radius: 12px;
        background: #faf8f3;
        color: #4c5748;
    }
    .help-card {
        scroll-margin-top: 24px;
        overflow: hidden;
    }
    .help-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(180deg, #ffffff 0%, #faf8f3 100%);
    }
    .help-card-header h4 {
        color: #193a15;
        font-weight: 800;
    }
    .help-number {
        width: 34px;
        height: 34px;
        flex: 0 0 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        color: #fff;
        background: #2d5a27;
        font-size: .9rem;
        font-weight: 800;
    }
    .help-card h6 {
        color: #2d5a27;
        font-weight: 800;
        margin-top: 18px;
    }
    .help-card h6:first-child {
        margin-top: 0;
    }
    .help-card li {
        margin-bottom: 8px;
    }
    .help-card li:last-child {
        margin-bottom: 0;
    }
    .workflow-step {
        min-height: 112px;
        padding: 16px;
        border: 1px solid #e3ddcf;
        border-radius: 12px;
        background: #faf8f3;
    }
    .workflow-step strong,
    .workflow-step span {
        display: block;
    }
    .workflow-step strong {
        color: #2d5a27;
        margin-bottom: 8px;
    }
    .workflow-step span {
        color: #616b5e;
        font-size: .9rem;
    }
    .help-safety {
        display: flex;
        align-items: center;
        gap: 12px;
        height: 100%;
        padding: 14px;
        border: 1px solid #e3ddcf;
        border-radius: 12px;
        color: #4c5748;
    }
    .help-safety i {
        color: #2d5a27;
        font-size: 1.25rem;
    }
    @media (max-width: 575.98px) {
        .help-hero {
            padding: 22px !important;
        }
        .help-hero-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            font-size: 1.65rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    (() => {
        const search = document.getElementById('helpSearch');
        const sections = [...document.querySelectorAll('.help-section')];
        const emptyState = document.getElementById('helpNoResults');
        if (!search) return;

        search.addEventListener('input', () => {
            const term = search.value.trim().toLowerCase();
            let visibleCount = 0;

            sections.forEach((section) => {
                const matches = !term || section.dataset.helpContent.includes(term) || section.textContent.toLowerCase().includes(term);
                section.classList.toggle('d-none', !matches);
                if (matches) visibleCount += 1;
            });

            emptyState?.classList.toggle('d-none', visibleCount > 0);
        });
    })();
</script>
@endpush

