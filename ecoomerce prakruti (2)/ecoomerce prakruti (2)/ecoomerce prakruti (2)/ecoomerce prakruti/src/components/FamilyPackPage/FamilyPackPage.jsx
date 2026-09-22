import React, { useEffect, useMemo, useState } from 'react';
import './FamilyPackPage.css';
import { productsApi } from '../../api/products';
import { familyPacksApi, getLocalFamilyPack } from '../../api/familyPacks';
import { useCart } from '../../context/CartContext';
import familyPackImg from '../../assets/images/family pack.png';
import packgringImg from '../../assets/images/new packing .png';

const defaultMembers = [
  { id: 1, name: 'Adult 1', age: 32 },
  { id: 2, name: 'Adult 2', age: 30 },
  { id: 3, name: 'Child 1', age: 8 },
];

const productPlan = [
  { key: 'wheat-flour', label: 'Daily Atta', keywords: ['wheat flour', 'multigrain flour', 'bajra atta'], baseQty: 2.2, benefit: 'Energy & Dietary Fiber' },
  { key: 'rice', label: 'Rice', keywords: ['basmati rice', 'sona masoori', 'rice'], baseQty: 1.8, benefit: 'Daily Essential Carbohydrates' },
  { key: 'main-dal', label: 'Main Dal', keywords: ['tuar dal', 'arhar', 'masoor dal', 'panchratna'], baseQty: 1.2, benefit: 'High Plant-Based Protein' },
  { key: 'second-dal', label: 'Mixed Pulses', keywords: ['panchratna', 'green gram', 'chana dal', 'rajma'], baseQty: 0.8, benefit: 'Protein Variety & Iron' },
  { key: 'oil', label: 'Cooking Oil', keywords: ['groundnut oil', 'sunflower oil', 'sesame oil'], baseQty: 0.7, benefit: 'Cold-Pressed Healthy Fats' },
  { key: 'ghee', label: 'Ghee', keywords: ['ghee'], baseQty: 0.35, benefit: 'Traditional A2 Good Fats' },
  { key: 'turmeric', label: 'Turmeric', keywords: ['turmeric'], baseQty: 0.18, benefit: 'Immunity & Daily Spice' },
  { key: 'cumin', label: 'Cumin', keywords: ['cumin whole', 'cumin powder', 'jeera'], baseQty: 0.18, benefit: 'Aromatic & Digestive' },
  { key: 'seeds', label: 'Seeds', keywords: ['chia', 'flax', 'pumpkin', 'sunflower seed'], baseQty: 0.45, benefit: 'Omega-3 & Vital Minerals' },
  { key: 'sweetener', label: 'Natural Sweetener', keywords: ['jaggery', 'raw sugar', 'khandasari', 'sweetener'], baseQty: 0.35, benefit: 'Unrefined Pure Sweetener' },
];

function ageGroup(age) {
  const value = Number(age || 0);
  if (value <= 12) return 'Child';
  if (value >= 60) return 'Senior';
  return 'Adult';
}

function memberFactor(age) {
  const group = ageGroup(age);
  if (group === 'Child') return 0.55;
  if (group === 'Senior') return 0.85;
  return 1;
}

function nutritionFor(age) {
  const group = ageGroup(age);
  if (group === 'Child') return { calories: 1700, protein: 25, fiber: 18, fats: 45 };
  if (group === 'Senior') return { calories: 1900, protein: 45, fiber: 28, fats: 50 };
  return { calories: 2200, protein: 55, fiber: 30, fats: 60 };
}

function findProduct(products, keywords, usedIds) {
  for (const keyword of keywords) {
    const found = products.find((product) => {
      const name = String(product.name || '').toLowerCase();
      return !usedIds.has(product.id) && name.includes(keyword);
    });
    if (found) return found;
  }
  return products.find((product) => !usedIds.has(product.id)) || null;
}

function buildRecommendations(products, members) {
  const adultUnits = members.reduce((sum, member) => sum + memberFactor(member.age), 0);
  const usedIds = new Set();

  return productPlan.map((entry) => {
    const product = findProduct(products, entry.keywords, usedIds);
    if (product) usedIds.add(product.id);
    const qty = Math.max(1, Math.ceil(entry.baseQty * Math.max(adultUnits, 1)));

    return {
      key: entry.key,
      label: entry.label,
      benefit: entry.benefit,
      product_id: product?.id || null,
      name: product?.name || entry.label,
      image: product?.image || packgringImg,
      price: Number(product?.price || 0),
      qty,
    };
  }).filter((item) => item.product_id);
}

function hydrateRecommendations(products, items = []) {
  return items.map((item) => {
    const product = products.find((entry) => entry.id === item.product_id);
    return {
      ...item,
      key: item.key || `saved-${item.product_id}`,
      label: item.label || 'Monthly Product',
      benefit: item.benefit || product?.category || 'Monthly essential staple',
      name: item.name || product?.name || 'Product',
      image: item.image || product?.image || packgringImg,
      price: Number(item.price ?? product?.price ?? 0),
      qty: Math.max(1, Number(item.qty || 1)),
    };
  }).filter((item) => item.product_id);
}

function summarizeMembers(members) {
  return members.reduce((summary, member) => {
    const group = ageGroup(member.age);
    return {
      ...summary,
      [group.toLowerCase()]: (summary[group.toLowerCase()] || 0) + 1,
    };
  }, { adult: 0, child: 0, senior: 0 });
}

const formatPrice = (value) => `₹${Number(value || 0).toFixed(0)}`;

const FamilyPackPage = ({ onGoToCart }) => {
  const { addToCart, flash } = useCart();
  const [products, setProducts] = useState([]);
  const [members, setMembers] = useState(defaultMembers);
  const [recommendations, setRecommendations] = useState([]);
  const [savedMessage, setSavedMessage] = useState('');
  const [addingPack, setAddingPack] = useState(false);
  const [extraSearch, setExtraSearch] = useState('');
  const [userChangedMembers, setUserChangedMembers] = useState(false);

  useEffect(() => {
    let mounted = true;
    productsApi.getProducts({ per_page: 100 }).then(async (res) => {
      if (!mounted) return;
      const loaded = res.data || [];
      setProducts(loaded);

      const latest = await familyPacksApi.getLatest();
      if (!mounted) return;

      const saved = latest.success && latest.data ? latest.data : getLocalFamilyPack();
      if (saved?.profile?.members?.length) {
        setMembers(saved.profile.members);
        if (Array.isArray(saved.recommendations) && saved.recommendations.length) {
          setRecommendations(hydrateRecommendations(loaded, saved.recommendations));
        }
      } else {
        setRecommendations(buildRecommendations(loaded, defaultMembers));
      }
    });

    return () => {
      mounted = false;
    };
  }, []);

  useEffect(() => {
    if (!products.length || !userChangedMembers) return;
    setRecommendations(buildRecommendations(products, members));
  }, [products, members, userChangedMembers]);

  useEffect(() => {
    if (!products.length || userChangedMembers) return;
    setRecommendations((current) => hydrateRecommendations(products, current));
  }, [products, userChangedMembers]);

  const familySummary = useMemo(() => summarizeMembers(members), [members]);

  const nutrition = useMemo(() => members.reduce((total, member) => {
    const perDay = nutritionFor(member.age);
    return {
      calories: total.calories + perDay.calories,
      protein: total.protein + perDay.protein,
      fiber: total.fiber + perDay.fiber,
      fats: total.fats + perDay.fats,
    };
  }, { calories: 0, protein: 0, fiber: 0, fats: 0 }), [members]);

  const packTotal = recommendations.reduce((sum, item) => sum + (item.price * item.qty), 0);

  const filteredExtras = products
    .filter((product) => {
      const query = extraSearch.trim().toLowerCase();
      if (!query) return true;
      return String(product.name || '').toLowerCase().includes(query) ||
        String(product.category || '').toLowerCase().includes(query);
    })
    .filter((product) => !recommendations.some((item) => item.product_id === product.id))
    .slice(0, 10);

  const updateMember = (id, field, value) => {
    setUserChangedMembers(true);
    setMembers((current) => current.map((member) => (
      member.id === id ? { ...member, [field]: field === 'age' ? Math.max(1, Number(value) || 1) : value } : member
    )));
  };

  const addMember = (type) => {
    setUserChangedMembers(true);
    const nextId = Math.max(0, ...members.map((member) => member.id)) + 1;
    let defaultAge = 30;
    let label = 'Adult';
    if (type === 'child') {
      defaultAge = 8;
      label = 'Child';
    } else if (type === 'senior') {
      defaultAge = 65;
      label = 'Senior';
    }
    setMembers((current) => [
      ...current,
      { id: nextId, name: `${label} ${current.length + 1}`, age: defaultAge },
    ]);
  };

  const removeMember = (id) => {
    setUserChangedMembers(true);
    setMembers((current) => current.length > 1 ? current.filter((member) => member.id !== id) : current);
  };

  const updateRecommendationQty = (key, delta) => {
    setRecommendations((current) => current.map((item) => (
      item.key === key ? { ...item, qty: Math.max(1, item.qty + delta) } : item
    )));
  };

  const removeRecommendation = (key) => {
    setRecommendations((current) => current.filter((item) => item.key !== key));
  };

  const addExtraProduct = (product) => {
    if (recommendations.some((item) => item.product_id === product.id)) return;
    setRecommendations((current) => [
      ...current,
      {
        key: `extra-${product.id}`,
        label: product.category || 'Pantry Extra',
        benefit: 'Added by customer',
        product_id: product.id,
        name: product.name,
        image: product.image || packgringImg,
        price: Number(product.price || 0),
        qty: 1,
      },
    ]);
  };

  const savePack = async () => {
    const payload = {
      profile: {
        members,
        adults: familySummary.adult + familySummary.senior,
        children: familySummary.child,
      },
      nutrient_summary: nutrition,
      recommendations,
      monthly_total: packTotal,
    };

    const res = await familyPacksApi.save(payload);
    setSavedMessage(res.fromLocal
      ? '✓ Family pack saved on your browser!'
      : '✓ Family pack successfully saved to your account!');
    setTimeout(() => setSavedMessage(''), 3500);
  };

  const addPackToCart = async () => {
    setAddingPack(true);
    await savePack();
    localStorage.setItem('prakruti_pending_order_type', 'family_pack');
    for (const item of recommendations) {
      await addToCart(item.product_id, item.qty);
    }
    setAddingPack(false);
    flash('Family pack added to cart');
    if (onGoToCart) onGoToCart();
  };

  return (
    <div className="family-pack-page">
      {/* 1. Hero Section */}
      <section className="family-pack-hero">
        <div className="container family-pack-hero-grid">
          <div className="family-pack-hero-copy">
            <span className="family-pack-pill-badge">🌾 Monthly Ration Planner</span>
            <h1>
              Build a Custom <em>Family Pack</em> for Your Home
            </h1>
            <p>
              Configure your household members and age groups to automatically generate a complete, 
              nutrient-balanced monthly basket of farm-fresh organic staples, pulses, cold-pressed oils, and spices.
            </p>

            <div className="family-pack-actions">
              <button 
                type="button" 
                className="btn btn-primary-fp" 
                onClick={addPackToCart} 
                disabled={addingPack || !recommendations.length}
              >
                {addingPack ? 'Adding to Cart...' : '🛒 Add Entire Pack to Cart'}
              </button>
              <button 
                type="button" 
                className="btn btn-outline-fp" 
                onClick={savePack}
              >
                💾 Save Configuration
              </button>
            </div>

            {savedMessage && <div className="family-pack-save-toast">{savedMessage}</div>}

            <div className="family-pack-stats-row">
              <div className="stat-card">
                <span className="stat-num">{members.length}</span>
                <span className="stat-lbl">Family Members</span>
              </div>
              <div className="stat-card">
                <span className="stat-num">{recommendations.length}</span>
                <span className="stat-lbl">Monthly Staples</span>
              </div>
              <div className="stat-card highlight">
                <span className="stat-num">{formatPrice(packTotal)}</span>
                <span className="stat-lbl">Est. Monthly Total</span>
              </div>
            </div>
          </div>

          <div className="family-pack-hero-image-wrap">
            <div className="hero-img-frame">
              <img src={familyPackImg} alt="Family monthly grocery essentials pack" />
              <div className="img-floating-tag">
                <span className="floating-icon">🌱</span>
                <div>
                  <p>Farm Fresh Monthly Supply</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* 2. Step 1: Configure Family Members */}
      <section className="container fp-section-container">
        <div className="fp-card fp-members-card">
          <div className="fp-card-header">
            <div>
              <span className="step-tag">Step 01</span>
              <h2>Family Profile &amp; Demographics</h2>
              <p>Adjust the number of adults, seniors, and children to scale monthly food quantities accurately.</p>
            </div>

            <div className="member-quick-add">
              <button type="button" className="btn-add-member" onClick={() => addMember('adult')}>
                + Add Adult
              </button>
              <button type="button" className="btn-add-member" onClick={() => addMember('child')}>
                + Add Child
              </button>
              <button type="button" className="btn-add-member" onClick={() => addMember('senior')}>
                + Add Senior
              </button>
            </div>
          </div>

          {/* Member summary chips */}
          <div className="member-chips-row">
            <div className="chip">
              <span className="chip-icon">👨</span>
              <span className="chip-count">{familySummary.adult}</span>
              <span className="chip-label">Adults (13-59)</span>
            </div>
            <div className="chip">
              <span className="chip-icon">🧒</span>
              <span className="chip-count">{familySummary.child}</span>
              <span className="chip-label">Children (0-12)</span>
            </div>
            <div className="chip">
              <span className="chip-icon">👴</span>
              <span className="chip-count">{familySummary.senior}</span>
              <span className="chip-label">Seniors (60+)</span>
            </div>
          </div>

          {/* Interactive Member List */}
          <div className="member-grid-list">
            {members.map((member, index) => {
              const grp = ageGroup(member.age);

              return (
                <div className="member-card-item" key={member.id}>
                  <div className="member-inputs-group">
                    <div className="input-wrap name-wrap">
                      <label>Name</label>
                      <input
                        type="text"
                        value={member.name}
                        onChange={(e) => updateMember(member.id, 'name', e.target.value)}
                        placeholder={`Member ${index + 1}`}
                      />
                    </div>

                    <div className="input-wrap age-wrap">
                      <label>Age (Yrs)</label>
                      <input
                        type="number"
                        min="1"
                        max="110"
                        value={member.age}
                        onChange={(e) => updateMember(member.id, 'age', e.target.value)}
                      />
                    </div>
                  </div>

                  <span className={`age-group-pill ${grp.toLowerCase()}`}>
                    {grp}
                  </span>

                  {members.length > 1 && (
                    <button 
                      type="button" 
                      className="btn-remove-member"
                      onClick={() => removeMember(member.id)}
                      title="Remove member"
                      aria-label={`Remove ${member.name}`}
                    >
                      ✕
                    </button>
                  )}
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* 3. Step 2: Daily Nutrition Insights */}
      <section className="container fp-section-container">
        <div className="fp-card fp-nutrition-card">
          <div className="fp-card-header">
            <div>
              <span className="step-tag">Step 02</span>
              <h2>Estimated Daily Family Nutrition</h2>
              <p>Recommended nutritional intake calculated across your active family demographic profile.</p>
            </div>
          </div>

          <div className="nutrition-cards-grid">
            <div className="nutrition-stat-card calories">
              <span className="nutri-icon">🔥</span>
              <span className="nutri-label">Daily Calories</span>
              <strong className="nutri-val">{nutrition.calories.toLocaleString()} <small>kcal</small></strong>
              <span className="nutri-sub">Total energy requirement</span>
            </div>

            <div className="nutrition-stat-card protein">
              <span className="nutri-icon">💪</span>
              <span className="nutri-label">Protein Target</span>
              <strong className="nutri-val">{nutrition.protein} <small>g/day</small></strong>
              <span className="nutri-sub">Plant proteins &amp; pulses</span>
            </div>

            <div className="nutrition-stat-card fiber">
              <span className="nutri-icon">🌾</span>
              <span className="nutri-label">Dietary Fiber</span>
              <strong className="nutri-val">{nutrition.fiber} <small>g/day</small></strong>
              <span className="nutri-sub">Whole grains &amp; millets</span>
            </div>

            <div className="nutrition-stat-card fats">
              <span className="nutri-icon">🫒</span>
              <span className="nutri-label">Healthy Fats</span>
              <strong className="nutri-val">{nutrition.fats} <small>g/day</small></strong>
              <span className="nutri-sub">Cold-pressed oils &amp; ghee</span>
            </div>
          </div>
        </div>
      </section>

      {/* 4. Step 3: Recommended Monthly Basket */}
      <section className="container fp-section-container">
        <div className="fp-card fp-basket-card">
          <div className="fp-card-header basket-header">
            <div>
              <span className="step-tag">Step 03</span>
              <h2>Your Ready Monthly Basket</h2>
              <p>Adjust item quantities as needed. All items will be added directly into your cart.</p>
            </div>

            <div className="basket-total-badge">
              <span>Estimated Monthly Pack</span>
              <strong>{formatPrice(packTotal)}</strong>
            </div>
          </div>

          <div className="basket-items-list">
            {recommendations.map((item, index) => {
              const itemTotal = item.price * item.qty;

              return (
                <div className="basket-item-row" key={item.key}>
                  <span className="item-num">{String(index + 1).padStart(2, '0')}</span>

                  <div className="item-thumb-box">
                    <img src={item.image} alt={item.name} />
                  </div>

                  <div className="item-details">
                    <span className="item-category-tag">{item.label}</span>
                    <h3 className="item-title">{item.name}</h3>
                    <p className="item-benefit">{item.benefit}</p>
                  </div>

                  <div className="item-unit-price">
                    <span className="price-label">Unit Price</span>
                    <span className="price-amount">{formatPrice(item.price)}</span>
                  </div>

                  <div className="item-qty-stepper">
                    <button type="button" onClick={() => updateRecommendationQty(item.key, -1)} aria-label="Decrease quantity">-</button>
                    <span className="qty-val">{item.qty}</span>
                    <button type="button" onClick={() => updateRecommendationQty(item.key, 1)} aria-label="Increase quantity">+</button>
                  </div>

                  <div className="item-subtotal">
                    <span className="price-label">Subtotal</span>
                    <span className="subtotal-amount">{formatPrice(itemTotal)}</span>
                  </div>

                  <button 
                    type="button" 
                    className="btn-delete-item"
                    onClick={() => removeRecommendation(item.key)}
                    title="Remove from pack"
                    aria-label={`Remove ${item.name}`}
                  >
                    ✕
                  </button>
                </div>
              );
            })}
          </div>

          {/* Bottom Basket Action Footer */}
          <div className="basket-action-footer">
            <div className="footer-left">
              <span className="item-count-badge">{recommendations.length} Items Selected</span>
            </div>
            
            <div className="footer-right">
              <button type="button" className="btn btn-outline-fp" onClick={savePack}>
                💾 Save Pack
              </button>
              <button 
                type="button" 
                className="btn btn-primary-fp" 
                onClick={addPackToCart} 
                disabled={addingPack || !recommendations.length}
              >
                {addingPack ? 'Adding Pack...' : `🛒 Add Pack to Cart (${formatPrice(packTotal)})`}
              </button>
            </div>
          </div>
        </div>
      </section>

      {/* 5. Step 4: Add Extra Pantry Staples */}
      <section className="container fp-section-container">
        <div className="fp-card fp-extras-card">
          <div className="fp-card-header extras-header">
            <div>
              <span className="step-tag">Optional</span>
              <h2>Add More Organic Essentials</h2>
              <p>Search our complete organic pantry and include extra items into this monthly ration pack.</p>
            </div>

            <div className="extras-search-wrap">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.2" strokeLinecap="round" strokeLinejoin="round" className="search-svg-icon">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
              <input
                type="search"
                placeholder="Search rice, spices, millets, honey..."
                value={extraSearch}
                onChange={(e) => setExtraSearch(e.target.value)}
              />
            </div>
          </div>

          <div className="extras-products-grid">
            {filteredExtras.map((product) => (
              <div className="extra-item-card" key={product.id}>
                <div className="extra-img-box">
                  <img src={product.image || packgringImg} alt={product.name} />
                  <span className="extra-cat-tag">{product.category || 'Organic'}</span>
                </div>

                <div className="extra-info-box">
                  <h4 className="extra-name" title={product.name}>{product.name}</h4>
                  <div className="extra-price-row">
                    <span className="extra-price">{formatPrice(product.price)}</span>
                    <button 
                      type="button" 
                      className="btn-add-extra"
                      onClick={() => addExtraProduct(product)}
                    >
                      + Add to Pack
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>
    </div>
  );
};

export default FamilyPackPage;
