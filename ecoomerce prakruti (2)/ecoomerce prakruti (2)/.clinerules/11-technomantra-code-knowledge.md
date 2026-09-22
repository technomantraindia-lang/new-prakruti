# Technomantra Local Code Knowledge Graph (V4.8.14)

> Structural local index. Read current source before editing. Secrets are intentionally excluded.

- Indexed source files: 103
- Structural edges: 79
- Matched end-to-end flows: 0
- Updated: 2026-09-15T10:49:51.924Z

## Routes
- ROUTE GET /login -> LoginController@showLoginForm @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE POST /login -> LoginController@login @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE GET /dashboard -> DashboardController@index @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE GET /search -> \App\Http\Controllers\Admin\GlobalSearchController@search @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE POST /logout -> LoginController@logout @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE GET /products-bulk/create -> ProductController@bulkCreate @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE POST /products-bulk/store -> ProductController@bulkStore @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE GET /products-import -> ProductController@importForm @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE POST /products-import -> ProductController@importStore @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE PATCH /products/{product}/toggle-status -> ProductController@toggleStatus @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE PATCH /products/{product}/toggle-featured -> ProductController@toggleFeatured @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE GET /products/{product}/variations -> \App\Http\Controllers\Admin\ProductVariationController@index @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE POST /products/{product}/variations -> \App\Http\Controllers\Admin\ProductVariationController@store @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE PUT /products/{product}/variations/{variation} -> \App\Http\Controllers\Admin\ProductVariationController@update @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE DELETE /products/{product}/variations/{variation} -> \App\Http\Controllers\Admin\ProductVariationController@destroy @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE GET /orders -> OrderController@index @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE POST /orders/bulk-action -> OrderController@bulkAction @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE GET /orders/{order} -> OrderController@show @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/admin.php
- ROUTE GET /api/products -> ProductApiController@index @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE GET /api/products/{slug} -> ProductApiController@show @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE GET /api/products/{product}/variations -> ProductApiController@variations @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE GET /api/categories -> CategoryApiController@index @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE GET /api/categories/{slug} -> CategoryApiController@show @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE GET /api/shipping-methods -> ShippingTaxApiController@shippingMethods @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE GET /api/taxes -> ShippingTaxApiController@taxes @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE GET /api/cart -> CartApiController@index @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE POST /api/cart/items -> CartApiController@store @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE PUT /api/cart/items/{id} -> CartApiController@update @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE DELETE /api/cart/items/{id} -> CartApiController@destroy @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE DELETE /api/cart -> CartApiController@clear @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE POST /api/inquiries -> InquiryApiController@store @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE POST /api/consultation-requests -> ConsultationRequestApiController@store @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE POST /api/register -> CustomerAuthApiController@register @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE POST /api/login -> CustomerAuthApiController@login @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE POST /api/logout -> CustomerAuthApiController@logout @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php
- ROUTE GET /api/me -> CustomerAuthApiController@me @ ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/routes/api.php

## Dependency edges
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AboutPage/AboutPage.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AboutPage/AboutPage.css
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AboutSection/AboutSection.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AboutSection/AboutSection.css
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AccountPage/AccountPage.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AccountPage/AccountPage.css
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AnnouncementBar/AnnouncementBar.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AnnouncementBar/AnnouncementBar.css
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AuthModal/AuthModal.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AuthModal/AuthModal.css
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AuthPage/AuthPage.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AuthPage/AuthPage.css
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/Benefits/Benefits.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/Benefits/Benefits.css
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/BestSellers/BestSellers.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/BestSellers/BestSellers.css
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/CartPage/CartPage.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/CartPage/CartPage.css
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/CategoryPage/CategoryPage.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/CategoryPage/CategoryPage.css
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/ContactPage/ContactPage.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/ContactPage/ContactPage.css
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/EducationPage/EducationPage.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/EducationPage/EducationPage.css
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/FamilyPackPage/FamilyPackPage.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/FamilyPackPage/FamilyPackPage.css
- IMPORT ecoomerce prakruti (2)/ecoomerce prakruti/src/components/FaqSection/FaqSection.jsx -> ecoomerce prakruti (2)/ecoomerce prakruti/src/components/FaqSection/FaqSection.css

## Database references
- DB ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/vite.config.js -> vite, laravel
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AboutPage/AboutPage.jsx -> react, a, do, verified, traditional, Rajasthan, chemical, trusted, certified, Source, mills
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AboutSection/AboutSection.jsx -> react, framer, verified, farmer
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AccountPage/AccountPage.jsx -> react
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AnnouncementBar/AnnouncementBar.jsx -> react
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AuthModal/AuthModal.jsx -> react, framer
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AuthPage/AuthPage.jsx -> react, framer, our, us
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/Benefits/Benefits.jsx -> react, framer, trusted
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/BestSellers/BestSellers.jsx -> react
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/CartPage/CartPage.jsx -> react
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/CategoryPage/CategoryPage.jsx -> react
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/ContactPage/ContactPage.jsx -> react
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/EducationPage/EducationPage.jsx -> react, groundwater, a, harmful, free, curds
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/FamilyPackPage/FamilyPackPage.jsx -> react, previous, the
- DB ecoomerce prakruti (2)/ecoomerce prakruti/src/components/FaqSection/FaqSection.jsx -> react, framer, a, do, verified, traditional, Rajasthan

## Symbols
- SYMBOL ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/app/Http/Controllers/Frontend/PageController.php: PageController, show
- SYMBOL ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/app/Models/Page.php: Page
- SYMBOL ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/resources/views/admin/layouts/app.blade.php: setSidebarTheme
- SYMBOL ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/resources/views/admin/products/_form.blade.php: renderGalleryPreview
- SYMBOL ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/resources/views/admin/settings/index.blade.php: bindRemoveButtons
- SYMBOL ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/tests/Feature/WebhookRouteTest.php: WebhookRouteTest, test_single_woocommerce_webhook_route_exists
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AboutPage/AboutPage.jsx: AboutPage, toggleFaq
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AboutSection/AboutSection.jsx: AboutSection
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AccountPage/AccountPage.jsx: AccountPage, loadAccount
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AnnouncementBar/AnnouncementBar.jsx: AnnouncementBar
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AuthModal/AuthModal.jsx: AuthModal
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AuthPage/AuthPage.jsx: AuthPage, handleSubmit
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/Benefits/Benefits.jsx: Benefits
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/BestSellers/BestSellers.jsx: getOriginalPrice, BestSellers, formatPrice
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/CartPage/CartPage.jsx: CartPage, handleQuantityChange, handleRemoveItem, handleApplyCoupon, handleCarouselScroll, handleSubscribe
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/CategoryPage/CategoryPage.jsx: formatPrice, getOriginalPrice, CategoryPage, handleResetFilters
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/ContactPage/ContactPage.jsx: ContactPage, handleInputChange, handleFormSubmit, handleSubscribe
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/EducationPage/EducationPage.jsx: parseFiniteNumber, feetAndInchesToMeters, centimetresToMeters, calculateBmiValue, roundBmiForDisplay, formatHeightCm, convertFeetInchesToCm, convertCmToFeetInches, classifyAdultBmi, EducationPage
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/FamilyPackPage/FamilyPackPage.jsx: ageGroup, memberFactor, nutritionFor, findProduct, buildRecommendations, hydrateRecommendations, summarizeMembers, FamilyPackPage, updateMember, addMember
- SYMBOL ecoomerce prakruti (2)/ecoomerce prakruti/src/components/FaqSection/FaqSection.jsx: FaqSection, toggleFaq

## Safe configuration variable names
- CONFIG ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/config/cache.php: DB_CACHE_CONNECTION, DB_CACHE_TABLE, DB_CACHE_LOCK_CONNECTION, DB_CACHE_LOCK_TABLE, REDIS_CACHE_CONNECTION, REDIS_CACHE_LOCK_CONNECTION
- CONFIG ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/config/database.php: DB_CONNECTION, DB_URL, DB_DATABASE, DB_FOREIGN_KEYS, DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD, DB_SOCKET, DB_CHARSET, DB_COLLATION, MYSQL_ATTR_SSL_CA, DB_SSLMODE, DB_ENCRYPT
- CONFIG ecoomerce prakruti (2)/ecommerce-backend-new/ecommerce-backend-new/config/queue.php: DB_QUEUE_CONNECTION, DB_QUEUE_TABLE, DB_QUEUE, DB_QUEUE_RETRY_AFTER, REDIS_QUEUE_CONNECTION, REDIS_QUEUE, REDIS_QUEUE_RETRY_AFTER, DB_CONNECTION

## UI/style selectors
- UI ecoomerce prakruti (2)/ecoomerce prakruti/index.html: #root
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AboutPage/AboutPage.css: .about-page, .container, .about-hero, .about-hero-content, .about-breadcrumbs, .about-hero-title, .about-hero-subtitle, .about-hero-highlights, .hero-hl-item, .about-story-section, .story-grid, .story-image-panel, .story-image-frame, #faf9f6
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AboutSection/AboutSection.css: .about-section, #fffaf1, #f4ead9, #fffdf8, .about-grid-2col, .about-left, .story-badge, #b86b42, .about-title, .title-line, .title-highlight, .about-text, .certifications-box, .cert-box-title
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AccountPage/AccountPage.css: .account-page, .account-container, .account-breadcrumbs, .account-hero-card, .account-card, .account-login-card, .account-avatar, .account-kicker, .account-alert, #fff8e1, #ffe0a3, .account-grid, .account-card-heading, .account-detail-list
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AnnouncementBar/AnnouncementBar.css: .announcement-bar, .announcement-content, .announcement-text, #ffffff, .leaf-icon
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AuthModal/AuthModal.css: .auth-overlay, .auth-panel, #fffaf1, .auth-close, .auth-visual, #ffffff, .auth-badge, #f3d899, #dcefd4, .auth-form, .auth-tabs, #efe2ce, .auth-submit, .auth-switch
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/AuthPage/AuthPage.css: .auth-page-wrapper, .auth-page-container, .auth-editorial-panel, .editorial-inner, .editorial-badge, .editorial-title, .editorial-text, #dbeed4, .editorial-bullets, .bullet-point, .bullet-icon, .bullet-info, #c9e2bf, .editorial-footer
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/Benefits/Benefits.css: .benefits-strip, .benefits-row, .benefit-item, .benefit-icon-box, .benefit-info, .benefit-title, .benefit-desc
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/BestSellers/BestSellers.css: .best-sellers, .container, .view-all-container, .view-all-btn, .products-grid, .product-card, .product-image-wrap, #fbfcf8, #f2f6ed, .product-image, .best-seller-badge, .product-info, .product-name, .product-subtext
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/CartPage/CartPage.css: .cart-page-rebuilt, .container, .cart-breadcrumbs, .cart-page-title, .cart-page-subtitle, .empty-cart-state, .empty-cart-icon, .cart-main-grid-layout, .cart-items-column, .cart-items-table-header, .col-header-product, .col-header-price, .col-header-quantity, .col-header-total
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/CategoryPage/CategoryPage.css: .category-page, .container, .category-quick-links, .quick-grid, .quick-card, #f4faf2, .quick-img-wrap, .quick-img, .quick-name, .quick-count, .trust-badges-row, .trust-container, .badge-item, .badge-icon
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/ContactPage/ContactPage.css: .contact-page, .container, .contact-hero, .contact-hero-inner, .contact-hero-left, .contact-breadcrumbs, .contact-hero-title, .decor-line, .contact-hero-subtitle, .contact-hero-right, .hero-product-mockup, .contact-main-section, .grid-contact-box, .contact-form-card
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/EducationPage/EducationPage.css: .education-page, .container, .edu-hero, .edu-hero-content, .edu-tag, .edu-title, .edu-subtitle, .edu-section, #f5f1e6, .edu-row, .edu-col-text, .edu-benefits-list, .edu-benefit-item, .benefit-icon
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/FamilyPackPage/FamilyPackPage.css: .family-pack-page, #fbf8ef, #f6f0e3, .container, .family-pack-hero, .family-pack-hero-grid, .family-pack-eyebrow, .family-panel-heading, .family-results-header, .family-pack-actions, .btn, .family-pack-footer-actions, .family-pack-save-note, .family-pack-stats
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/FaqSection/FaqSection.css: .faq-section, #fffcf6, #ffffff, .faq-container, .faq-grid, .faq-info-col, .faq-badge, #b86b42, .faq-title, .faq-title-highlight, .faq-intro-text, .faq-contact-card, .contact-card-icon, .contact-card-content
- UI ecoomerce prakruti (2)/ecoomerce prakruti/src/components/FarmGallery/FarmGallery.css: .farm-gallery, .container, .gallery-hero, .gallery-hero-content, .gallery-tag, .gallery-title, .gallery-subtitle, .gallery-header, .section-subtitle, .gallery-filters, .filter-btn, .gallery-grid, .gallery-card, .gallery-img-wrapper
