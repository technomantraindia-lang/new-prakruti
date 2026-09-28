import React, { useEffect, useState } from 'react';
import './CartPage.css';
import { api } from '../../services/api';
import { accountApi } from '../../api/account';
import { productsApi } from '../../api/products';
import { useCart } from '../../context/CartContext';
import packgringImg from '../../assets/images/new packing .png';

const CartPage = ({ onShopClick, currentUser, onLoginRequired }) => {
  const {
    items: cartItems,
    updateQuantity,
    removeItem,
    addToCart,
    clearCart,
    loading,
    subtotal: cartSubtotal,
    discount,
    discountRule,
    gstAmt,
    shipCharge,
    freeShippingThreshold,
    freeShippingRemaining,
    freeShippingUnlocked,
    total,
  } = useCart();
  const [couponCode, setCouponCode] = useState('');
  const [discountValue, setDiscountValue] = useState(0);
  const [couponApplied, setCouponApplied] = useState(false);
  const [couponMessage, setCouponMessage] = useState('');
  const [recommendProducts, setRecommendProducts] = useState([]);
  const [newsletterEmail, setNewsletterEmail] = useState('');
  const [subscribed, setSubscribed] = useState(false);
  const [carouselIndex, setCarouselIndex] = useState(0);
  const [isSubmittingOrder, setIsSubmittingOrder] = useState(false);
  const [orderConfirmation, setOrderConfirmation] = useState(null);
  const [checkoutError, setCheckoutError] = useState('');
  const [savedAddresses, setSavedAddresses] = useState([]);
  const [selectedAddressId, setSelectedAddressId] = useState('new');
  const [saveAddress, setSaveAddress] = useState(true);
  const [shipping, setShipping] = useState({
    name: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    pincode: '',
  });

  useEffect(() => {
    let mounted = true;
    productsApi.getProducts({ per_page: 8 }).then((res) => {
      if (!mounted) return;
      const mapped = (res.data || []).map((prod) => ({
        id: prod.id,
        name: prod.name,
        category: prod.category || 'Organic',
        desc: prod.short_description || '100% Natural',
        weight: prod.weight || '1 pack',
        price: Number(prod.price || 0),
        rating: 4.6,
        reviews: '120+',
        image: prod.image || packgringImg,
        badge: prod.featured ? 'bestseller' : null,
      }));
      setRecommendProducts(mapped);
    });
    return () => { mounted = false; };
  }, []);

  useEffect(() => {
    if (!currentUser) return;

    const name = currentUser.name || '';
    const phone = currentUser.phone || '';
    setShipping((prev) => ({
      ...prev,
      name: prev.name || name,
      phone: prev.phone || phone,
    }));

    let mounted = true;
    accountApi.getAddresses().then((res) => {
      if (!mounted || !res.success) return;
      const addresses = Array.isArray(res.data) ? res.data : [];
      setSavedAddresses(addresses);
      const defaultAddress = addresses.find((address) => address.is_default) || addresses[0];
      if (defaultAddress) {
        setSelectedAddressId(String(defaultAddress.id));
        setShipping({
          name: `${defaultAddress.first_name || name || ''} ${defaultAddress.last_name || ''}`.trim(),
          phone: defaultAddress.phone || phone || '',
          address: [defaultAddress.address_line_1, defaultAddress.address_line_2].filter(Boolean).join(', '),
          city: defaultAddress.city || '',
          state: defaultAddress.state || '',
          pincode: defaultAddress.pincode || '',
        });
      }
    });

    return () => {
      mounted = false;
    };
  }, [currentUser]);

  const handleQuantityChange = (id, delta) => {
    const item = cartItems.find((entry) => entry.id === id);
    if (!item) return;
    updateQuantity(id, item.quantity + delta);
  };

  const handleRemoveItem = (id) => {
    removeItem(id);
  };

  const handleApplyCoupon = (e) => {
    e.preventDefault();
    setCouponMessage('Coupons are no longer available.');
  };

  const handleAddressSelect = (value) => {
    setSelectedAddressId(value);
    if (value === 'new') {
      setShipping((prev) => ({
        ...prev,
        name: currentUser?.name || prev.name || '',
        phone: currentUser?.phone || prev.phone || '',
        address: '',
        city: '',
        state: '',
        pincode: '',
      }));
      setSaveAddress(true);
      return;
    }

    const selected = savedAddresses.find((address) => String(address.id) === String(value));
    if (!selected) return;

    setShipping({
      name: `${selected.first_name || currentUser?.name || ''} ${selected.last_name || ''}`.trim(),
      phone: selected.phone || currentUser?.phone || '',
      address: [selected.address_line_1, selected.address_line_2].filter(Boolean).join(', '),
      city: selected.city || '',
      state: selected.state || '',
      pincode: selected.pincode || '',
    });
    setSaveAddress(false);
  };

  const buildShippingAddress = () => (
    [shipping.name, shipping.phone, shipping.address, shipping.city, shipping.state, shipping.pincode]
      .filter(Boolean)
      .join(', ')
  );


  // Carousel slider navigations
  const handleCarouselScroll = (direction) => {
    if (direction === 'left') {
      setCarouselIndex(prev => Math.max(prev - 1, 0));
    } else {
      setCarouselIndex(prev => Math.min(prev + 1, Math.max(recommendProducts.length - 4, 0)));
    }
  };

  // Calculations
  const localSubtotal = cartItems.reduce((acc, item) => acc + (item.price * item.quantity), 0);
  const subtotal = Number(cartSubtotal || localSubtotal || 0);
  const shippingThreshold = Number(freeShippingThreshold || 0);
  const shippingFee = Number.isFinite(shipCharge) ? Number(shipCharge) : 0;
  const totalDiscount = Number(discount || 0);
  const estimatedGst = Number(gstAmt || 0);
  const estimatedTotal = Number(total || Math.max(0, subtotal - totalDiscount) + estimatedGst + shippingFee);
  const originalEstimatedTotal = estimatedTotal + totalDiscount;
  const nextDiscountRule = discountRule?.next_rule;
  const nextDiscountNeeded = Number(discountRule?.amount_needed || 0);
  const discountRules = Array.isArray(discountRule?.rules)
    ? [...discountRule.rules].sort((a, b) => Number(a.min_amount || 0) - Number(b.min_amount || 0))
    : [];
  const hasDiscountOffer = Boolean(discountRule?.enabled && (discountRules.length > 0 || totalDiscount > 0 || nextDiscountRule));

  // Free shipping progress bar calculations
  const progressPercent = shippingThreshold > 0 ? Math.min((subtotal / shippingThreshold) * 100, 100) : 100;
  const neededForFree = Number(freeShippingRemaining ?? Math.max(shippingThreshold - subtotal, 0));

  const handleSubscribe = (e) => {
    e.preventDefault();
    if (!newsletterEmail) return;
    setSubscribed(true);
    setTimeout(() => {
      setSubscribed(false);
      setNewsletterEmail('');
      alert("Thank you for subscribing to Prakruti newsletter! 🌿");
    }, 1500);
  };

  return (
    <div className="cart-page-rebuilt">
      <div className="container">
        
        {/* Breadcrumbs */}
        <div className="cart-breadcrumbs">Home &nbsp; &gt; &nbsp; <span className="active">Cart</span></div>
        
        <h1 className="cart-page-title">Your Cart</h1>
        <p className="cart-page-subtitle">Review your items, check savings, and proceed to secure checkout.</p>

        {loading ? (
          <div className="empty-cart-state">
            <h2>Loading your cart...</h2>
          </div>
        ) : cartItems.length === 0 ? (
          <div className="empty-cart-state">
            {orderConfirmation ? (
              <>
                <span className="empty-cart-icon">🎉</span>
                <h2>Order placed successfully!</h2>
                <p>Order Number: <strong>{orderConfirmation.order_num || orderConfirmation.order_number}</strong></p>
                <p>Status: {orderConfirmation.status || 'pending'}</p>
                <button className="btn btn-primary" onClick={onShopClick}>Continue Shopping</button>
              </>
            ) : (
              <>
                <span className="empty-cart-icon">🛒</span>
                <h2>Your cart is currently empty.</h2>
                <p>Wholesome staples, cold-pressed oils, and spices are waiting to nourish your family.</p>
                <button className="btn btn-primary" onClick={onShopClick}>Explore Categories</button>
              </>
            )}
          </div>
        ) : (
          <div className="cart-main-grid-layout">
            
            {/* Left Column: Product List */}
            <div className="cart-items-column">
              <div className="cart-items-table-header">
                <span className="col-header-product">Product</span>
                <span className="col-header-price">Price</span>
                <span className="col-header-quantity">Quantity</span>
                <span className="col-header-total">Total</span>
                <span className="col-header-action"></span>
              </div>

              <div className="cart-items-list-rows">
                {cartItems.map((item) => (
                  <div key={item.id} className="cart-item-row">
                    
                    {/* Product Cell */}
                    <div className="cart-item-product-cell">
                      <div className="item-img-frame">
                        <img src={item.image} alt={item.name} />
                      </div>
                      <div className="item-details-box">
                        <h3>{item.name}</h3>
                        <p className="item-sub-desc">{item.subtitle}</p>
                        <p className={`item-weight-tag ${item.package || item.desc ? '' : 'missing'}`}>
                          {item.package || item.desc || 'Package size not selected'}
                        </p>
                      </div>
                    </div>

                    {/* Price Cell */}
                    <div className="cart-item-price-cell">
                      <span className="item-price-value">₹{Number(item.price).toFixed(2)}</span>
                      <span className="item-price-per-kg">₹{Number(item.price).toFixed(2)}{item.desc ? ` / ${item.desc}` : ''}</span>
                    </div>

                    {/* Quantity Cell */}
                    <div className="cart-item-qty-cell">
                      <div className="cart-qty-selector">
                        <button type="button" onClick={() => handleQuantityChange(item.id, -1)}>-</button>
                        <span className="qty-value-display">{item.quantity}</span>
                        <button type="button" onClick={() => handleQuantityChange(item.id, 1)}>+</button>
                      </div>
                    </div>

                    {/* Total Cell */}
                    <div className="cart-item-total-cell">
                      <span className="item-total-value">₹{(item.price * item.quantity).toFixed(2)}</span>
                    </div>

                    {/* Action Cell */}
                    <div className="cart-item-delete-cell">
                      <button 
                        type="button" 
                        className="delete-item-trash-btn" 
                        onClick={() => handleRemoveItem(item.id)}
                        title="Remove product"
                        aria-label={`Remove ${item.name}`}
                      >
                        🗑️
                      </button>
                    </div>

                  </div>
                ))}
              </div>

              {/* Continue Shopping & Shipping Progress footer bar */}
              <div className="cart-list-footer-row">
                <button type="button" className="continue-shopping-outline-btn" onClick={onShopClick}>
                  ← Continue Shopping
                </button>
                
                {shippingThreshold > 0 && !freeShippingUnlocked && subtotal < shippingThreshold && (
                  <div className="shipping-progress-banner">
                    <p>Add <strong>₹{neededForFree.toFixed(2)}</strong> more to get <strong>FREE shipping!</strong></p>
                    <div className="progress-bar-container">
                      <div className="progress-bar-fill" style={{ width: `${progressPercent}%` }}></div>
                    </div>
                    <span className="progress-bar-text-val">₹{subtotal.toFixed(0)} / ₹{shippingThreshold}</span>
                  </div>
                )}

                {(freeShippingUnlocked || (shippingThreshold > 0 && subtotal >= shippingThreshold)) && (
                  <div className="shipping-progress-banner success-shipping">
                    <p>🎉 You have unlocked <strong>FREE shipping!</strong></p>
                    <div className="progress-bar-container">
                      <div className="progress-bar-fill unlocked" style={{ width: '100%' }}></div>
                    </div>
                  </div>
                )}
              </div>

            </div>

            {/* Right Column: Order Summary */}
            <div className="cart-summary-column">
              <div className="order-summary-card">
                <h2>Order Summary</h2>

                {hasDiscountOffer && (
                  <div className={`cart-offer-box ${totalDiscount > 0 ? 'applied' : ''}`}>
                    <span className="cart-offer-label">{totalDiscount > 0 ? 'Discount Applied' : 'Bill Discount Offer'}</span>
                    <strong>
                      {totalDiscount > 0
                        ? `You saved ₹${totalDiscount.toFixed(2)}${discountRule?.percent ? ` (${discountRule.percent}% off)` : ''}`
                        : `Add ₹${nextDiscountNeeded.toFixed(2)} more to get ${nextDiscountRule?.percent}% off`}
                    </strong>
                    {discountRules.length > 0 && (
                      <div className="cart-offer-tiers" aria-label="Available bill discount offers">
                        {discountRules.map((rule) => {
                          const minimum = Number(rule.min_amount || 0);
                          const percent = Number(rule.percent || 0);
                          const unlocked = subtotal >= minimum;
                          const current = unlocked && totalDiscount > 0 && Number(discountRule?.min_amount || 0) === minimum;
                          const amountNeeded = Math.max(minimum - subtotal, 0);

                          return (
                            <div key={`${minimum}-${percent}`} className={`cart-offer-tier ${unlocked ? 'unlocked' : ''} ${current ? 'current' : ''}`}>
                              <div>
                                <strong>₹{minimum.toFixed(0)}+ bill</strong>
                                <span>{percent}% off</span>
                              </div>
                              <small>{current ? 'Best offer applied' : unlocked ? 'Offer available' : `Add ₹${amountNeeded.toFixed(0)} more`}</small>
                            </div>
                          );
                        })}
                      </div>
                    )}
                    {discountRule?.min_amount > 0 && totalDiscount > 0 && (
                      <small>Offer unlocked on bills above ₹{Number(discountRule.min_amount).toFixed(0)}.</small>
                    )}
                  </div>
                )}
                
                <div className="summary-details-rows">
                  <div className="summary-row">
                    <span>Subtotal ({cartItems.length} items)</span>
                    <span className="summary-val-dark">₹{subtotal.toFixed(2)}</span>
                  </div>
                  {totalDiscount > 0 && (
                    <div className="summary-row discount-row">
                      <span>{discountRule?.percent ? `Bill Discount (${discountRule.percent}%)` : 'Bill Discount'}</span>
                      <span className="discount-value-green">- ₹{totalDiscount.toFixed(2)}</span>
                    </div>
                  )}
                  <div className="summary-row">
                    <span>GST</span>
                    <span className="summary-val-dark">₹{estimatedGst.toFixed(2)}</span>
                  </div>
                  <div className="summary-row">
                    <span>Shipping</span>
                    <span className="summary-val-dark">{shippingFee > 0 ? `₹${shippingFee.toFixed(2)}` : 'FREE'}</span>
                  </div>
                  {false && couponApplied && subtotal > 0 && (
                    <div className="summary-row discount-row">
                      <span>Discount</span>
                      <span className="discount-value-green">- ₹{totalDiscount.toFixed(2)}</span>
                    </div>
                  )}
                </div>

                <div className="estimated-total-row">
                  <div className="est-flex">
                    <span>Estimated Total</span>
                    {totalDiscount > 0 && <span className="est-total-cut">₹{originalEstimatedTotal.toFixed(2)}</span>}
                    <span className="est-total-amount">₹{estimatedTotal.toFixed(2)}</span>
                  </div>
                  <p className="est-taxes-label">(Inclusive of all taxes)</p>
                </div>

                {/* Promo Code Box */}
                <form onSubmit={handleApplyCoupon} className="coupon-code-form" style={{ display: 'none' }}>
                  <span className="coupon-small-title">🏷️ Apply Coupon Code</span>
                  <div className="coupon-input-group">
                    <input 
                      type="text" 
                      placeholder="Enter coupon code" 
                      value={couponCode}
                      onChange={(e) => setCouponCode(e.target.value)}
                    />
                    <button type="submit">Apply</button>
                  </div>
                  {couponApplied && <p className="coupon-code-indicator">{couponMessage}</p>}
                  {!couponApplied && couponMessage && <p className="coupon-code-indicator" style={{ color: '#c62828' }}>{couponMessage}</p>}
                </form>

                <div className="checkout-address-box">
                  <span className="coupon-small-title">Delivery details</span>
                  {savedAddresses.length > 0 && (
                    <div className="saved-address-picker">
                      <label htmlFor="savedAddress">Select saved address</label>
                      <select
                        id="savedAddress"
                        value={selectedAddressId}
                        onChange={(e) => handleAddressSelect(e.target.value)}
                      >
                        {savedAddresses.map((address) => (
                          <option key={address.id} value={address.id}>
                            {[address.address_line_1, address.city, address.state, address.pincode].filter(Boolean).join(', ')}
                            {address.is_default ? ' (Default)' : ''}
                          </option>
                        ))}
                        <option value="new">+ Add new address</option>
                      </select>
                    </div>
                  )}
                  <input
                    type="text"
                    placeholder="Full name"
                    value={shipping.name}
                    onChange={(e) => setShipping((prev) => ({ ...prev, name: e.target.value }))}
                  />
                  <input
                    type="tel"
                    placeholder="Phone number"
                    value={shipping.phone}
                    onChange={(e) => setShipping((prev) => ({ ...prev, phone: e.target.value }))}
                  />
                  <textarea
                    placeholder="Shipping address"
                    rows="3"
                    value={shipping.address}
                    onChange={(e) => setShipping((prev) => ({ ...prev, address: e.target.value }))}
                  />
                  <div className="checkout-address-grid">
                    <input
                      type="text"
                      placeholder="City"
                      value={shipping.city}
                      onChange={(e) => setShipping((prev) => ({ ...prev, city: e.target.value }))}
                    />
                    <input
                      type="text"
                      placeholder="State"
                      value={shipping.state}
                      onChange={(e) => setShipping((prev) => ({ ...prev, state: e.target.value }))}
                    />
                  </div>
                  <input
                    type="text"
                    placeholder="Pincode"
                    value={shipping.pincode}
                    onChange={(e) => setShipping((prev) => ({ ...prev, pincode: e.target.value }))}
                  />
                  {selectedAddressId === 'new' && (
                    <label className="save-address-check">
                      <input
                        type="checkbox"
                        checked={saveAddress}
                        onChange={(e) => setSaveAddress(e.target.checked)}
                      />
                      Save this address for next time
                    </label>
                  )}
                </div>

                {checkoutError && <p className="checkout-error-text">{checkoutError}</p>}

                {/* Checkout Trigger */}
                <button 
                  type="button" 
                  className="btn btn-primary proceed-checkout-btn"
                  disabled={isSubmittingOrder}
                  onClick={async () => {
                    setCheckoutError('');
                    if (!currentUser) {
                      setCheckoutError('Please login first to purchase your products.');
                      if (onLoginRequired) onLoginRequired();
                      return;
                    }
                    if (!shipping.name || !shipping.phone || !shipping.address || !shipping.city || !shipping.state) {
                      setCheckoutError('Please add your name, phone, address, city, and state.');
                      return;
                    }
                    setIsSubmittingOrder(true);
                    let finalShippingAddress = buildShippingAddress();
                    if (selectedAddressId === 'new' && saveAddress) {
                      const nameParts = String(shipping.name || '').trim().split(/\s+/);
                      const firstName = nameParts.shift() || currentUser?.name || '';
                      const lastName = nameParts.join(' ');
                      const addressRes = await accountApi.addAddress({
                        type: 'shipping',
                        first_name: firstName,
                        last_name: lastName,
                        phone: shipping.phone,
                        address_line_1: shipping.address,
                        city: shipping.city,
                        state: shipping.state,
                        pincode: shipping.pincode,
                        country: 'India',
                        is_default: savedAddresses.length === 0,
                      });

                      if (addressRes.success && addressRes.data) {
                        setSavedAddresses((current) => [addressRes.data, ...current]);
                        setSelectedAddressId(String(addressRes.data.id));
                      } else {
                        setIsSubmittingOrder(false);
                        setCheckoutError(addressRes.message || 'Could not save this address. Please check the details.');
                        return;
                      }
                    }

                    const payload = {
                      items: cartItems.map(item => ({
                        product_id: item.product_id || item.id,
                        var_id: item.var_id || null,
                        qty: item.quantity,
                      })),
                      order_type: localStorage.getItem('prakruti_pending_order_type') === 'family_pack' ? 'family_pack' : 'standard',
                      payment_method: 'cod',
                      shipping_address: finalShippingAddress,
                      billing_address: finalShippingAddress,
                      coupon_code: couponApplied ? couponCode : undefined,
                    };
                    const res = await api.createOrder(payload);
                    setIsSubmittingOrder(false);
                    if (res && res.success) {
                      setOrderConfirmation(res.order);
                      await clearCart();
                      setCouponApplied(false);
                      setDiscountValue(0);
                      setCouponCode('');
                      localStorage.removeItem('prakruti_pending_order_type');
                    } else {
                      if (res?.status === 401) {
                        setCheckoutError('Please login first to purchase your products.');
                        if (onLoginRequired) onLoginRequired();
                      } else {
                        setCheckoutError(res?.message || 'Could not place the order.');
                      }
                    }
                  }}
                >
                  {isSubmittingOrder ? 'Processing Order...' : 'Proceed to Checkout ➔'}
                </button>

                {orderConfirmation && (
                  <div className="order-success-modal" style={{ marginTop: '16px', padding: '16px', backgroundColor: '#e8f5e9', borderRadius: '8px', border: '1px solid #c8e6c9', color: '#2e7d32' }}>
                    <h3>🎉 Order Placed Successfully!</h3>
                    <p>Order Number: <strong>{orderConfirmation.order_num || orderConfirmation.order_number}</strong></p>
                    <p>Status: {orderConfirmation.status || 'Processing'}</p>
                    <p>Thank you for choosing Prakruti Organic!</p>
                  </div>
                )}

                <p className="secure-checkout-label">🔒 Secure and trusted checkout</p>
              </div>

              {/* Save Indicator */}
              {totalDiscount > 0 && (
                <div className="you-save-indicator-badge">
                  <span>✓</span> You save ₹{totalDiscount.toFixed(0)} on this order!
                </div>
              )}

            </div>

          </div>
        )}

        {/* You May Also Like Slider */}
        <section className="cart-recommend-section">
          <div className="recommend-section-header">
            <h2>You May Also Like</h2>
            <div className="carousel-nav-buttons">
              <button type="button" className="carousel-nav-btn prev" onClick={() => handleCarouselScroll('left')} disabled={carouselIndex === 0}>‹</button>
              <button type="button" className="carousel-nav-btn next" onClick={() => handleCarouselScroll('right')} disabled={carouselIndex >= Math.max(recommendProducts.length - 4, 0)}>›</button>
            </div>
          </div>

          <div className="recommend-carousel-viewport">
            <div className="recommend-carousel-track" style={{ transform: `translateX(-${carouselIndex * 25}%)` }}>
              {recommendProducts.map((prod) => (
                <div key={prod.id} className="recommend-product-card">
                  <div className="rec-card-image-box">
                    <img src={prod.image} alt={prod.name} />
                    {prod.badge && <span className={`rec-badge ${prod.badge}`}>{prod.badge.toUpperCase()}</span>}
                  </div>
                  <div className="rec-card-body">
                    <div className="rec-card-category">{String(prod.category || 'Organic').toUpperCase()}</div>
                    <h3 className="rec-card-title">{prod.name}</h3>
                    <p className="rec-card-desc">{prod.desc} | {prod.weight}</p>
                    <div className="rec-rating-row">
                      <span className="rec-stars">★ ★ ★ ★ ★</span>
                      <span className="rec-rating-val">{prod.rating}</span>
                      <span className="rec-reviews-cnt">({prod.reviews})</span>
                    </div>
                    <div className="rec-price-action-row">
                      <span className="rec-price-tag">₹{Number(prod.price).toFixed(2)}</span>
                      <button type="button" className="rec-add-to-cart-btn" onClick={() => addToCart(prod.id, 1)}>🛒 Add to Cart</button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </section>

        {/* 4-Item Trust Row */}
        <div className="cart-trust-indicators-bar">
          <div className="trust-indicator-card">
            <span className="trust-indicator-icon">💳</span>
            <div>
              <h4>Secure Payments</h4>
              <p>100% safe & encrypted</p>
            </div>
          </div>
          <div className="trust-indicator-card">
            <span className="trust-indicator-icon">🚚</span>
            <div>
              <h4>Fresh Delivery</h4>
              <p>On-time, every time</p>
            </div>
          </div>
          <div className="trust-indicator-card">
            <span className="trust-indicator-icon">🛡️</span>
            <div>
              <h4>No Preservatives</h4>
              <p>Pure & natural always</p>
            </div>
          </div>
          <div className="trust-indicator-card">
            <span className="trust-indicator-icon">🔬</span>
            <div>
              <h4>Quality Assured</h4>
              <p>Lab tested products</p>
            </div>
          </div>
        </div>

        {/* Bottom Subscription Banner */}
        <section className="cart-stay-updated-banner">
          <div className="stay-updated-inner-grid">
            <div className="updated-text-col">
              <h2>Stay Healthy, Stay Updated</h2>
              <p>Get exclusive offers, new arrivals & wellness tips straight to your inbox.</p>
            </div>
            <div className="updated-form-col">
              <form onSubmit={handleSubscribe} className="updated-subscribe-box">
                <input 
                  type="email" 
                  placeholder="Enter your email address" 
                  value={newsletterEmail}
                  onChange={(e) => setNewsletterEmail(e.target.value)}
                  required
                />
                <button type="submit" disabled={subscribed}>
                  {subscribed ? 'Subscribing...' : 'Subscribe'}
                </button>
              </form>
              <p className="no-spam-note-txt">No spam. Unsubscribe anytime.</p>
            </div>
          </div>
        </section>

      </div>
    </div>
  );
};

export default CartPage;
