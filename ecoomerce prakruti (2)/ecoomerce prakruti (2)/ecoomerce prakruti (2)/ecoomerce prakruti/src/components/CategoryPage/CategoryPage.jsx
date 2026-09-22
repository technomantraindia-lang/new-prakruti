import React, { useState, useEffect, useMemo } from 'react';
import './CategoryPage.css';
import { categoriesData as fallbackCategories, productsData as fallbackProducts } from '../../data/products';
import { api } from '../../services/api';
import { useCart } from '../../context/CartContext';

import bottomCtaBg from '../../assets/images/new cta.png';

const formatPrice = (value) => `₹${Number(value || 0).toFixed(0)}`;

const getOriginalPrice = (product) => {
  const current = Number(product.price || 0);
  const givenOriginal = Number(product.originalPrice || product.original_price || product.mrp || product.compare_at_price || 0);
  if (givenOriginal > current) return givenOriginal;
  return Math.max(current + 1, Math.round((current / 0.75) / 10) * 10);
};

const CategoryPage = ({ initialCategory, clearInitialCategory, onShopClick, onViewProduct, initialSearchQuery, clearInitialSearchQuery }) => {
  const { addToCart } = useCart();
  const [addingId, setAddingId] = useState(null);

  // Dynamic API state
  const [categories, setCategories] = useState(fallbackCategories);
  const [products, setProducts] = useState(fallbackProducts);

  // Filters State
  const [selectedCategory, setSelectedCategory] = useState('All');
  const [sortBy, setSortBy] = useState('Popularity');
  const [searchQuery, setSearchQuery] = useState('');

  // Fetch categories and products on mount
  useEffect(() => {
    let isMounted = true;
    api.getCategories().then(res => {
      if (isMounted && res && res.length > 0) setCategories(res);
    });
    api.getProducts().then(res => {
      if (isMounted && res && res.length > 0) setProducts(res);
    });
    return () => { isMounted = false; };
  }, []);

  // Handle Initial Category redirect
  useEffect(() => {
    if (initialCategory) {
      setSelectedCategory(initialCategory);
      clearInitialCategory();
      window.scrollTo({ top: 400, behavior: 'smooth' });
    }
  }, [initialCategory, clearInitialCategory]);

  // Handle Initial Search redirect
  useEffect(() => {
    if (initialSearchQuery !== undefined && initialSearchQuery !== null) {
      setSearchQuery(initialSearchQuery);
      clearInitialSearchQuery();
      setSelectedCategory('All'); // Show all categories so search finds matching items globally
      window.scrollTo({ top: 400, behavior: 'smooth' });
    }
  }, [initialSearchQuery, clearInitialSearchQuery]);

  // Reset all filters
  const handleResetFilters = () => {
    setSelectedCategory('All');
    setSortBy('Popularity');
    setSearchQuery('');
  };

  // Filter & Sort Logic
  const filteredProducts = useMemo(() => {
    let result = [...products];

    // Search Query
    if (searchQuery.trim() !== '') {
      const query = searchQuery.toLowerCase();
      result = result.filter(p => p.name.toLowerCase().includes(query) || (p.category && p.category.toLowerCase().includes(query)));
    }

    // Category Filter
    if (selectedCategory !== 'All') {
      result = result.filter(p => p.category === selectedCategory);
    }

    // Sorting
    if (sortBy === 'Price: Low to High') {
      result.sort((a, b) => a.price - b.price);
    } else if (sortBy === 'Price: High to Low') {
      result.sort((a, b) => b.price - a.price);
    }
    // "Popularity" is default sorting

    return result;
  }, [products, selectedCategory, sortBy, searchQuery]);

  // Group products by category to match the layout
  const groupedProducts = useMemo(() => {
    const groups = {};
    
    // Determine which categories should be shown
    const activeCategories = selectedCategory === 'All' 
      ? categories.map(c => c.name) 
      : [selectedCategory];

    activeCategories.forEach(catName => {
      groups[catName] = filteredProducts.filter(p => p.category === catName);
    });

    return groups;
  }, [filteredProducts, selectedCategory, categories]);

  return (
    <div className="category-page">
      {/* 2. Top Category Quick Selection Grid */}
      <section className="category-quick-links">
        <div className="container">
          <div className="quick-grid">
            {categories.map((cat, index) => {
              const isActive = selectedCategory === cat.name;
              return (
                <button
                  key={cat.id}
                  className={`quick-card ${isActive ? 'active' : ''}`}
                  onClick={() => {
                    setSelectedCategory(cat.name);
                    const el = document.getElementById('products-explore');
                    if (el) el.scrollIntoView({ behavior: 'smooth' });
                  }}
                  style={{ '--slide-order': index }}
                >
                  <img 
                    src={cat.image} 
                    alt={cat.name} 
                    className="quick-bg-img" 
                    onError={(e) => {
                      e.target.onerror = null;
                      e.target.style.display = 'none';
                    }}
                  />
                  <div className="quick-overlay" />
                  <div className="quick-content">
                    <h3 className="quick-name">{cat.name}</h3>
                    {cat.count && <span className="quick-count">{cat.count} Items</span>}
                  </div>
                </button>
              );
            })}
          </div>
        </div>
      </section>

      {/* 3. Trust Badges Row */}
      <section className="trust-badges-row">
        <div className="container trust-container">
          <div className="badge-item">
            <span className="badge-icon">🌿</span>
            <div className="badge-text">
              <h4>Natural Goodness</h4>
              <p>Pure and Natural</p>
            </div>
          </div>
          <div className="badge-item">
            <span className="badge-icon">🤝</span>
            <div className="badge-text">
              <h4>Ethically Sourced</h4>
              <p>Responsible & Sustainable</p>
            </div>
          </div>
          <div className="badge-item">
            <span className="badge-icon">💧</span>
            <div className="badge-text">
              <h4>Cold Pressed & Pure</h4>
              <p>Unrefined & Chemical Free</p>
            </div>
          </div>
          <div className="badge-item">
            <span className="badge-icon">🏺</span>
            <div className="badge-text">
              <h4>Traditional Quality</h4>
              <p>Time Honored Goodness</p>
            </div>
          </div>
          <div className="badge-item">
            <span className="badge-icon">🚚</span>
            <div className="badge-text">
              <h4>Fresh Delivery</h4>
              <p>Packed with Care</p>
            </div>
          </div>
        </div>
      </section>

      {/* 4. Products Explore Section (Sidebar + Grid) */}
      <section className="products-explore" id="products-explore">
        <div className="container explore-container">
          
          {/* Sidebar Filters */}
          <aside className="explore-sidebar">
            
            {/* Categories Radio List */}
            <div className="filter-group">
              <h3 className="filter-title">Categories</h3>
              <div className="filter-options">
                <label className="radio-container">
                  <input
                    type="radio"
                    name="category-filter"
                    checked={selectedCategory === 'All'}
                    onChange={() => setSelectedCategory('All')}
                  />
                  <span className="radio-checkmark"></span>
                  All Categories
                </label>
                {categories.map(cat => (
                  <label key={cat.id} className="radio-container">
                    <input
                      type="radio"
                      name="category-filter"
                      checked={selectedCategory === cat.name}
                      onChange={() => setSelectedCategory(cat.name)}
                    />
                    <span className="radio-checkmark"></span>
                    {cat.name}
                  </label>
                ))}
              </div>
            </div>

            {/* Sort By Dropdown */}
            <div className="filter-group">
              <h3 className="filter-title">Sort By</h3>
              <select
                value={sortBy}
                onChange={(e) => setSortBy(e.target.value)}
                className="sort-select"
              >
                <option value="Popularity">Popularity</option>
                <option value="Price: Low to High">Price: Low to High</option>
                <option value="Price: High to Low">Price: High to Low</option>
              </select>
            </div>

            {/* Clear Filters Button */}
            {(selectedCategory !== 'All' || sortBy !== 'Popularity') && (
              <button className="clear-filters-btn" onClick={handleResetFilters}>
                🧹 Clear All Filters
              </button>
            )}
          </aside>

          {/* Main Feed Area */}
          <main className="explore-main">
            {selectedCategory !== 'All' && (
              <button 
                className="back-to-all-btn"
                onClick={() => setSelectedCategory('All')}
              >
                ← Back to All Categories
              </button>
            )}
            {Object.keys(groupedProducts).every(key => groupedProducts[key].length === 0) ? (
              <div className="no-products-found">
                <span className="no-products-icon">🌾</span>
                <h2>No Products Found</h2>
                <p>We couldn't find any products matching your selected filters. Try adjusting them or clear all filters to start over.</p>
                <button className="btn btn-primary" onClick={handleResetFilters}>Reset Filters</button>
              </div>
            ) : (
              categories.map(cat => {
                const products = groupedProducts[cat.name];
                if (!products || products.length === 0) return null;

                return (
                  <div key={cat.id} className="category-section-block">
                    {/* Group Header */}
                    <div className="category-block-header">
                      <div className="header-left">
                        <div className="cat-thumbnail">
                          <img 
                            src={cat.image} 
                            alt={cat.name} 
                            onError={(e) => {
                              e.target.onerror = null;
                              e.target.style.display = 'none';
                            }}
                          />
                        </div>
                        <div className="cat-heading-info">
                          <h2 className="cat-block-title">{cat.name}</h2>
                          <p className="cat-block-desc">{cat.description}</p>
                        </div>
                      </div>
                      {selectedCategory === 'All' && (
                        <button 
                          className="view-all-group-btn"
                          onClick={() => setSelectedCategory(cat.name)}
                        >
                          View All
                        </button>
                      )}
                    </div>

                    {/* Products Grid */}
                    <div className="products-grid-feed">
                      {products.map(product => {
                        const origPrice = getOriginalPrice(product);
                        const discountPercent = origPrice > product.price 
                          ? Math.round(((origPrice - product.price) / origPrice) * 100)
                          : 0;

                        return (
                          <div 
                            key={product.id} 
                            className="product-feed-card" 
                            onClick={() => onViewProduct && onViewProduct(product.id)}
                          >
                            <div className="product-image-container">
                              <div className="card-badge-container">
                                {product.types?.includes('Organic') && (
                                  <span className="organic-tag">🌿 Organic</span>
                                )}
                                {discountPercent > 0 && (
                                  <span className="discount-tag">-{discountPercent}%</span>
                                )}
                              </div>

                              <img 
                                src={product.image} 
                                alt={product.name} 
                                className="product-feed-img" 
                                loading="lazy"
                              />

                              <div className="product-card-overlay">
                                <button 
                                  className="quick-view-btn"
                                  onClick={(e) => {
                                    e.stopPropagation();
                                    if (onViewProduct) onViewProduct(product.id);
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
                                <span className="product-feed-cat">{product.category || cat.name}</span>
                                <div className="product-feed-rating">
                                  <span className="rating-star">★</span>
                                  <span className="rating-val">5.0</span>
                                  <span className="review-count">({product.reviews || 16})</span>
                                </div>
                              </div>

                              <h3 className="product-feed-name" title={product.name}>{product.name}</h3>

                              <div className="product-pricing-row">
                                <div className="pricing-left">
                                  <span className="price-val">{formatPrice(product.price)}</span>
                                  {origPrice > product.price && (
                                    <span className="price-original">{formatPrice(origPrice)}</span>
                                  )}
                                </div>
                                {discountPercent > 0 && (
                                  <span className="price-save-badge">Save {formatPrice(origPrice - product.price)}</span>
                                )}
                              </div>

                              <button 
                                className="feed-add-cart-btn"
                                disabled={addingId === product.id}
                                onClick={async (e) => {
                                  e.stopPropagation();
                                  setAddingId(product.id);
                                  try {
                                    await addToCart(product.id, 1);
                                  } catch (err) {
                                    console.error(err);
                                  } finally {
                                    setAddingId(null);
                                  }
                                }}
                              >
                                {addingId === product.id ? (
                                  <span className="btn-loading-text">Adding...</span>
                                ) : (
                                  <>
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round">
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
                  </div>
                );
              })
            )}
          </main>

        </div>
      </section>

      {/* 5. Bottom Promo Banner */}
      <section className="category-promo-section">
        <div className="container">
          <div className="category-promo-inner" style={{ backgroundImage: `linear-gradient(to right, rgba(19, 44, 21, 0.9), rgba(19, 44, 21, 0.45)), url(${bottomCtaBg})` }}>
            <div className="promo-text-wrap">
              <h2 className="bottom-promo-title">Bring home purity in every pantry</h2>
              <p className="bottom-promo-desc">
                Handpicked essentials for a healthier, happier you and your family.
              </p>
            </div>
            <div className="promo-buttons-wrap">
              <button 
                className="btn bottom-cta-btn-primary"
                onClick={() => {
                  handleResetFilters();
                  window.scrollTo({ top: 400, behavior: 'smooth' });
                }}
              >
                Shop All Categories
              </button>
              <button 
                className="btn bottom-cta-btn-outline"
                onClick={() => {
                  if (onShopClick) onShopClick();
                }}
              >
                View Best Sellers
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
};

export default CategoryPage;
