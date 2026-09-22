import React, { useState, useEffect, useMemo } from 'react';
import './ProductDetailPage.css';
import { productsData as fallbackProducts } from '../../data/products';
import { api } from '../../services/api';
import { reviewsApi } from '../../api/reviews';
import { useCart } from '../../context/CartContext';
import packgringImg from '../../assets/images/new packing .png';
import sedexLogo from '../../assets/images/sedex.png';
import usdaLogo from '../../assets/images/usda logo.png';
import kLogo from '../../assets/images/k.png';
import jaivikBharatLogo from '../../assets/images/jaivik bharat.png';
import gheeDetailImg from '../../assets/images/ghhee.png';

const formatPrice = (value) => `₹${Number(value || 0).toFixed(0)}`;

const getPackMultiplier = (packSize) => {
  if (packSize === '1 kg') return 1.8;
  if (packSize === '2 kg') return 3.4;
  if (packSize === '5 kg') return 8.0;
  return 1;
};

const getOriginalPrice = (product, currentPrice = Number(product?.price || 0)) => {
  const givenOriginal = Number(product?.originalPrice || product?.original_price || product?.mrp || product?.compare_at_price || 0);
  if (givenOriginal > Number(product?.price || 0)) {
    const multiplier = Number(product?.price || 0) ? currentPrice / Number(product.price || 1) : 1;
    return givenOriginal * multiplier;
  }
  return Math.max(currentPrice + 1, Math.round((currentPrice / 0.75) / 10) * 10);
};

const renderMultilineText = (value) => {
  const lines = String(value || '').trim().split(/\r?\n/).filter(Boolean);

  return lines.map((line, index) => (
    <React.Fragment key={`${line}-${index}`}>
      {line}
      {index < lines.length - 1 && <br />}
    </React.Fragment>
  ));
};

const ProductDetailPage = ({ productId, onBack, onNavigateProduct, onBuyNow }) => {
  const { addToCart } = useCart();
  const [product, setProduct] = useState(() => {
    const found = fallbackProducts.find(p => String(p.id) === String(productId));
    return found || fallbackProducts[0];
  });
  const [relatedProducts, setRelatedProducts] = useState([]);
  const [addingRelatedId, setAddingRelatedId] = useState(null);
  const [quantity, setQuantity] = useState(1);
  const [packSize, setPackSize] = useState('500g');
  const [activeTab, setActiveTab] = useState('Overview');
  const [addedToCart, setAddedToCart] = useState(false);
  const [adding, setAdding] = useState(false);
  const [customerReviews, setCustomerReviews] = useState([]);
  const [reviewForm, setReviewForm] = useState({ name: '', location: '', rating: 5, comment: '' });
  const [reviewSubmitting, setReviewSubmitting] = useState(false);

  useEffect(() => {
    let isMounted = true;
    if (productId) {
      api.getProductBySlugOrId(productId).then(res => {
        if (isMounted && res) setProduct(res);
      });
    }
    return () => { isMounted = false; };
  }, [productId]);

  useEffect(() => {
    let isMounted = true;
    if (!product?.category) return undefined;
    api.getProducts({ category: product.category, per_page: 12 }).then((items) => {
      if (!isMounted) return;
      const baseList = (items && items.length > 1) ? items : fallbackProducts;
      let matched = baseList.filter((item) => String(item.id) !== String(product.id) && item.category === product.category);
      if (matched.length < 4) {
        matched = baseList.filter((item) => String(item.id) !== String(product.id));
      }
      setRelatedProducts(matched.slice(0, 5));
    });
    return () => { isMounted = false; };
  }, [product?.category, product?.id]);

  // Handle alternate images for thumbnail grid
  const productImages = useMemo(() => {
    const list = Array.isArray(product?.images) && product.images.length
      ? product.images
      : [product?.image || packgringImg];

    return list.filter(Boolean).filter((value, index, self) => self.indexOf(value) === index);
  }, [product]);

  const [activeImage, setActiveImage] = useState(() => product?.images?.[0] || product?.image || packgringImg);

  useEffect(() => {
    setActiveImage(productImages[0] || packgringImg);
  }, [productImages]);

  useEffect(() => {
    let isMounted = true;
    const reviewProductKey = product?.slug || product?.id || productId;
    if (!reviewProductKey) return undefined;

    reviewsApi.getProductReviews(reviewProductKey).then((res) => {
      if (!isMounted || !res.success || !Array.isArray(res.data)) return;
      setCustomerReviews(res.data);
    });

    return () => { isMounted = false; };
  }, [product?.slug, product?.id, productId]);

  const packageOptions = useMemo(() => {
    const apiPackages = Array.isArray(product?.variations)
      ? product.variations.filter((variation) => variation && variation.stock_status !== 'inactive')
      : [];

    if (apiPackages.length) {
      return apiPackages.map((variation) => {
        const regularPrice = Number(variation.price || 0);
        const salePrice = variation.sale_price !== null && variation.sale_price !== undefined
          ? Number(variation.sale_price)
          : null;

        return {
          key: `variation-${variation.id}`,
          variantId: variation.id,
          label: variation.label || variation.package_size || variation.attributes?.Weight || 'Pack',
          price: salePrice ?? regularPrice,
          regularPrice,
          availableStock: Number(variation.available_stock || 0),
        };
      });
    }

    return ['500g', '1 kg', '2 kg', '5 kg'].map((size) => {
      const price = Number(product.price || 0) * getPackMultiplier(size);
      return {
        key: size,
        variantId: null,
        label: size,
        price,
        regularPrice: getOriginalPrice(product, price),
        availableStock: Number(product.available_stock || product.stock_qty || 0),
      };
    });
  }, [product]);

  const selectedPackage = useMemo(
    () => packageOptions.find((option) => option.key === packSize) || packageOptions[0],
    [packageOptions, packSize]
  );

  // Reset states when the product ID changes
  useEffect(() => {
    setQuantity(1);
    setPackSize(packageOptions[0]?.key || '500g');
    setActiveTab('Overview');
    setAddedToCart(false);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }, [product?.id, packageOptions]);

  const handleAddToCart = async () => {
    setAdding(true);
    const res = await addToCart(product.id, quantity, selectedPackage?.variantId || null);
    setAdding(false);
    if (res?.success) {
      setAddedToCart(true);
      setTimeout(() => setAddedToCart(false), 2000);
    }
  };

  const handleBuyNow = async () => {
    setAdding(true);
    const res = await addToCart(product.id, quantity, selectedPackage?.variantId || null);
    setAdding(false);
    if (res?.success) {
      onBuyNow?.();
    }
  };

  const handleReviewSubmit = async (event) => {
    event.preventDefault();
    if (!reviewForm.name.trim() || !reviewForm.comment.trim()) return;

    const payload = {
      name: reviewForm.name.trim(),
      location: reviewForm.location.trim() || 'Verified Customer',
      rating: Number(reviewForm.rating) || 5,
      comment: reviewForm.comment.trim()
    };

    setReviewSubmitting(true);
    const res = await reviewsApi.addProductReview(product.slug || product.id, payload);
    setReviewSubmitting(false);

    const savedReview = res.success && res.data
      ? res.data
      : { id: Date.now(), ...payload };

    setCustomerReviews((current) => [savedReview, ...current]);
    setReviewForm({ name: '', location: '', rating: 5, comment: '' });
  };

  const priceDisplay = Number(selectedPackage?.price ?? product.price ?? 0);
  const originalPriceDisplay = Number(selectedPackage?.regularPrice ?? getOriginalPrice(product, priceDisplay));
  const showGheeDetailImage = String(product?.name || '').toLowerCase().includes('a2 gir cow ghee bilona');
  const productShortDescription = product.short_description || product.short_desc || product.description || `Premium quality ${product.name} sourced from trusted organic farms.`;
  const productLongDescription = product.description || productShortDescription;
  const nutritionalInfo = product.nutritional_info || '';
  const productInformation = {
    product_type: product.product_information?.product_type || product.category,
    shelf_life: product.product_information?.shelf_life || '12 Months',
    ingredient: product.product_information?.ingredient || `Pure ${product.name}`,
    packaging_type: product.product_information?.packaging_type || 'Food Grade Standing Pouch',
    storage: product.product_information?.storage || 'Store in dry, airtight container',
  };

  return (
    <div className="product-detail-page">
      <div className="container">
        
        {/* Navigation Breadcrumbs */}
        <div className="detail-navigation-bar">
          <button className="back-link-btn" onClick={onBack}>
            ← Back to Categories
          </button>
          <div className="detail-breadcrumbs">
            Home &nbsp; <span>&gt;</span> &nbsp; {product.category} &nbsp; <span>&gt;</span> &nbsp; <span className="active">{product.name}</span>
          </div>
        </div>

        {/* Product Details Grid */}
        <div className="product-details-grid-rebuilt">
          
          {/* Left Column: Thumbnails + Main Showcase */}
          <div className="image-showcase-container">
            {/* Vertical thumbnails strip */}
            <div className="thumbnails-strip">
              {productImages.map((img, idx) => (
                <div 
                  key={idx} 
                  className={`thumb-box ${activeImage === img ? 'active' : ''}`}
                  onClick={() => setActiveImage(img)}
                >
                  <img src={img} alt={`Thumbnail view ${idx + 1}`} />
                </div>
              ))}
            </div>

            {/* Large Main Showcase Panel */}
            <div className="main-display-frame">
              <img src={activeImage} alt={product.name} className="active-showcase-img" />
              <span className="organic-badge-label">🌱 Pure &amp; Natural</span>
              <button className="expand-view-trigger" title="Expand View" aria-label="Expand image view">🔍</button>
            </div>
          </div>

          {/* Right Column: Detailed Specifications */}
          <div className="info-showcase-container">
            
            <span className="category-green-tag">{String(product.category || 'Organic').toUpperCase()}</span>
            
            <h1 className="showcase-title">{product.name}</h1>
            
            {/* Reviews line */}
            <div className="ratings-meta-row">
              <div className="stars-fill">★★★★★</div>
              <span className="rating-score">4.8</span>
              <span className="review-count">({Math.max(Number(product.reviews || 0), customerReviews.length, 125)} Reviews)</span>
            </div>

            {/* Brief description */}
            <p className="brief-desc">
              {productShortDescription}
            </p>

            {/* Pricing Section */}
            <div className="pricing-showcase-box">
              <div className="detail-price-row">
                <span className="detail-original-price">{formatPrice(originalPriceDisplay)}</span>
                <span className="price-tag-value">{formatPrice(priceDisplay)}</span>
              </div>
            </div>

            {/* Pack Size Selector */}
            <div className="pack-size-section">
              <span className="section-small-title">Pack Size</span>
              <div className="sizes-btn-row">
                {packageOptions.map(option => (
                  <button 
                    key={option.key}
                    type="button"
                    className={`size-select-btn ${packSize === option.key ? 'active' : ''}`}
                    onClick={() => setPackSize(option.key)}
                  >
                    <span>{option.label}</span>
                    <small>{formatPrice(option.price)}</small>
                  </button>
                ))}
              </div>
              {selectedPackage?.availableStock <= 0 && (
                <p className="pack-stock-warning">This package is currently out of stock.</p>
              )}
            </div>

            {/* Highlights features grid */}
            <div className="features-highlight-grid">
              <div className="feature-card-item">
                <span className="feature-icon">📝</span>
                <div className="feature-text">
                  <h4>High in Protein</h4>
                  <p>Builds muscle</p>
                </div>
              </div>
              <div className="feature-card-item">
                <span className="feature-icon">🌿</span>
                <div className="feature-text">
                  <h4>Pure &amp; Natural</h4>
                  <p>Simple, wholesome ingredients</p>
                </div>
              </div>
              <div className="feature-card-item">
                <span className="feature-icon">🚫</span>
                <div className="feature-text">
                  <h4>No Preservatives</h4>
                  <p>Chemical free</p>
                </div>
              </div>
              <div className="feature-card-item">
                <span className="feature-icon">🏡</span>
                <div className="feature-text">
                  <h4>Farm Fresh</h4>
                  <p>Direct harvest</p>
                </div>
              </div>
            </div>

            {/* Quantity Selector & Actions Buttons */}
            <div className="cart-buy-actions-wrapper">
              <div className="qty-selector-rebuilt">
                <button type="button" onClick={() => setQuantity(q => q > 1 ? q - 1 : 1)}>-</button>
                <span className="qty-show-val">{quantity}</span>
                <button type="button" onClick={() => setQuantity(q => q + 1)}>+</button>
              </div>

              <button 
                type="button" 
                className={`btn add-to-cart-green-btn ${addedToCart ? 'success' : ''}`}
                onClick={handleAddToCart}
                disabled={adding}
              >
                {addedToCart ? '✨ Added to Cart!' : (adding ? 'Adding...' : '🛒 Add to Cart')}
              </button>

              <button
                type="button"
                className="btn buy-now-white-btn"
                onClick={handleBuyNow}
                disabled={adding || selectedPackage?.availableStock <= 0}
              >
                {adding ? 'Processing...' : '⚡ Buy Now'}
              </button>

            </div>

          </div>

        </div>

        {/* Certificate Logos Banner */}
        <div className="details-trust-banner-row">
          <div className="trust-banner-item trust-certifications-item">
            <div className="trust-certification-logos" aria-label="Certification logos">
              <img src={sedexLogo} alt="Sedex" className="trust-certification-logo" />
              <img src={usdaLogo} alt="USDA Organic" className="trust-certification-logo" />
              <img src={kLogo} alt="Quality certification" className="trust-certification-logo" />
              <img src={jaivikBharatLogo} alt="Jaivik Bharat" className="trust-certification-logo" />
            </div>
          </div>
        </div>

        {showGheeDetailImage && (
          <div className="ghee-detail-feature-image">
            <img src={gheeDetailImg} alt="A2 Gir Cow Ghee Bilona details" />
          </div>
        )}

        {/* Tabs Detailed Specification Panel */}
        <div className="details-tabs-component">
          <div className="tabs-nav-bar">
            {['Overview', 'Nutritional Info', `Reviews (${Math.max(Number(product.reviews || 0), customerReviews.length, 125)})`].map(tab => (
              <button 
                key={tab}
                type="button" 
                className={`tab-nav-btn ${activeTab === tab ? 'active' : ''}`}
                onClick={() => setActiveTab(tab)}
              >
                {tab}
              </button>
            ))}
          </div>

          <div className="tab-details-container">
            {activeTab === 'Overview' && (
              <div className="overview-tab-content">
                
                {/* Left Bullet checklist */}
                <div className="overview-left-list">
                  <h3>Product Overview</h3>
                  <p>{renderMultilineText(productLongDescription)}</p>
                  <ul className="overview-checkmarks">
                    <li><span>✓</span> Pure &amp; Natural Ingredients</li>
                    <li><span>✓</span> Rich in Protein & Fiber</li>
                    <li><span>✓</span> No Artificial Colors or Preservatives</li>
                    <li><span>✓</span> Sourced from Trusted Farming Communities</li>
                  </ul>
                </div>

                {/* Center table specs */}
                <div className="overview-center-table">
                  <h3>Product Information</h3>
                  <table className="specs-table-data">
                    <tbody>
                      <tr>
                        <th>Product Type</th>
                        <td>{productInformation.product_type}</td>
                      </tr>
                      <tr>
                        <th>Shelf Life</th>
                        <td>{productInformation.shelf_life}</td>
                      </tr>
                      <tr>
                        <th>Ingredient</th>
                        <td>{productInformation.ingredient}</td>
                      </tr>
                      <tr>
                        <th>Packaging Type</th>
                        <td>{productInformation.packaging_type}</td>
                      </tr>
                      <tr>
                        <th>Storage</th>
                        <td>{productInformation.storage}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>

              </div>
            )}

            {activeTab === 'Nutritional Info' && (
              <div className="nutrition-tab-content">
                <h3>Nutritional Info</h3>
                {nutritionalInfo ? (
                  <div className="nutrition-backend-text">{renderMultilineText(nutritionalInfo)}</div>
                ) : (
                  <p className="text-muted">Nutritional information will be updated soon.</p>
                )}
              </div>
            )}

            {activeTab.startsWith('Reviews') && (
              <div className="reviews-tab-content">
                <h3>Customer Testimonials</h3>
                <form className="product-review-form" onSubmit={handleReviewSubmit}>
                  <div className="review-form-grid">
                    <input
                      type="text"
                      value={reviewForm.name}
                      onChange={(event) => setReviewForm((current) => ({ ...current, name: event.target.value }))}
                      placeholder="Your name"
                      required
                    />
                    <input
                      type="text"
                      value={reviewForm.location}
                      onChange={(event) => setReviewForm((current) => ({ ...current, location: event.target.value }))}
                      placeholder="City"
                    />
                    <select
                      value={reviewForm.rating}
                      onChange={(event) => setReviewForm((current) => ({ ...current, rating: event.target.value }))}
                    >
                      {[5, 4, 3, 2, 1].map((rating) => (
                        <option key={rating} value={rating}>{rating} Star{rating > 1 ? 's' : ''}</option>
                      ))}
                    </select>
                  </div>
                  <textarea
                    value={reviewForm.comment}
                    onChange={(event) => setReviewForm((current) => ({ ...current, comment: event.target.value }))}
                    placeholder={`Share your experience with ${product.name}`}
                    rows="4"
                    required
                  />
                  <button type="submit" disabled={reviewSubmitting}>{reviewSubmitting ? 'Saving...' : 'Add Review'}</button>
                </form>
                {customerReviews.map((review) => (
                  <div className="review-comment-item customer-added-review" key={review.id}>
                    <div className="rev-stars">{'★'.repeat(review.rating)}</div>
                    <p className="rev-comment">"{review.comment}"</p>
                    <span className="rev-meta">- {review.name}, {review.location}</span>
                  </div>
                ))}
                <div className="review-comment-item">
                  <div className="rev-stars">★★★★★</div>
                  <p className="rev-comment">"This {product.name} is extremely pure and cooks beautifully. Smells authentic, just like traditional harvests. Highly recommend!"</p>
                  <span className="rev-meta">- Rajesh Kumar, August 2026</span>
                </div>
                <div className="review-comment-item">
                  <div className="rev-stars">★★★★★</div>
                  <p className="rev-comment">"Best organic product we have tried. The packaging is premium and the grains are clean without any dirt."</p>
                  <span className="rev-meta">- Meera Sharma, July 2026</span>
                </div>
              </div>
            )}
          </div>
        </div>

        {/* Related Products Section */}
        <section className="related-products-section">
          <div className="related-section-header">
            <h2>You May Also Like</h2>
            <p>Explore other premium, wholesome options in the {product.category} category</p>
          </div>
          
          <div className="products-grid-feed">
            {relatedProducts.map(relProduct => {
              const origPrice = getOriginalPrice(relProduct);
              const discountPercent = origPrice > relProduct.price 
                ? Math.round(((origPrice - relProduct.price) / origPrice) * 100)
                : 0;

              return (
                <div 
                  key={relProduct.id} 
                  className="product-feed-card"
                  onClick={() => onNavigateProduct && onNavigateProduct(relProduct.id)}
                >
                  <div className="product-image-container">
                    <div className="card-badge-container">
                      {relProduct.types?.includes('Organic') && (
                        <span className="organic-tag">🌿 Organic</span>
                      )}
                      {discountPercent > 0 && (
                        <span className="discount-tag">-{discountPercent}%</span>
                      )}
                    </div>

                    <img 
                      src={relProduct.image} 
                      alt={relProduct.name} 
                      className="product-feed-img" 
                      loading="lazy"
                    />

                    <div className="product-card-overlay">
                      <button 
                        className="quick-view-btn" 
                        type="button"
                        onClick={(e) => {
                          e.stopPropagation();
                          if (onNavigateProduct) onNavigateProduct(relProduct.id);
                        }}
                        title="View Product Details"
                      >
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" className="svg-eye">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                          <circle cx="12" cy="12" r="3" />
                        </svg>
                      </button>
                    </div>
                  </div>
                  
                  <div className="product-info-container">
                    <div className="product-meta-row">
                      <span className="product-feed-cat">{relProduct.category || product.category}</span>
                      <div className="product-feed-rating">
                        <span className="rating-star">★</span>
                        <span className="rating-val">5.0</span>
                        <span className="review-count">({relProduct.reviews || 16})</span>
                      </div>
                    </div>

                    <h3 className="product-feed-name" title={relProduct.name}>{relProduct.name}</h3>

                    <div className="product-pricing-row">
                      <div className="pricing-left">
                        <span className="price-val">{formatPrice(relProduct.price)}</span>
                        {origPrice > relProduct.price && (
                          <span className="price-original">{formatPrice(origPrice)}</span>
                        )}
                      </div>
                      {discountPercent > 0 && (
                        <span className="price-save-badge">Save {formatPrice(origPrice - relProduct.price)}</span>
                      )}
                    </div>

                    <button 
                      type="button" 
                      className="feed-add-cart-btn"
                      disabled={addingRelatedId === relProduct.id}
                      onClick={async (e) => {
                        e.stopPropagation();
                        setAddingRelatedId(relProduct.id);
                        try {
                          await addToCart(relProduct.id, 1);
                        } catch (err) {
                          console.error(err);
                        } finally {
                          setAddingRelatedId(null);
                        }
                      }}
                    >
                      {addingRelatedId === relProduct.id ? (
                        <span className="btn-loading-text">Adding...</span>
                      ) : (
                        <>
                          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                          </svg>
                          <span>+ Add to Cart</span>
                        </>
                      )}
                    </button>
                  </div>
                </div>
              );
            })}
          </div>
        </section>

      </div>
    </div>
  );
};

export default ProductDetailPage;
