import React, { useState } from 'react';
import './AboutPage.css';

// Import Assets
import aboutCenterImg from '../../assets/images/about_center.png';
import seedsImg from '../../assets/images/seeds.png';
import gheeImg from '../../assets/images/bestseller_gir_ghee.jpg';
import packagingImg from '../../assets/images/packge.png';
import spicesImg from '../../assets/images/spices.png';
import aboutUsBanner from '../../assets/images/new aabout us iamge.png';
import sectionBg from '../../assets/images/SECTION .png';

// Certification Badges
import jaivikBharatImg from '../../assets/images/jaivik bharat.png';
import usdaLogoImg from '../../assets/images/usda logo.png';
import labTestedImg from '../../assets/images/lab tested.jpg';
import sedexImg from '../../assets/images/sedex.png';
import fssaiImg from '../../assets/images/fssai.jpg';

const FAQ_ITEMS = [
  {
    q: "How does Prakruti ensure 100% chemical-free purity?",
    a: "Every single crop is cultivated in certified pesticide-free organic soils without synthetic fertilizers or chemical sprays. Each harvested batch is sent to NABL-accredited laboratories to test for over 200+ chemical pesticide residues, heavy metals, and adulterants before it is packaged."
  },
  {
    q: "Where do you source your organic staples from?",
    a: "We source directly from generational farmer cooperatives across India's most fertile agricultural regions. Our A2 Gir Cow Ghee comes from Saurashtra & Gir pastures in Gujarat, our cold-pressed mustard and sesame seeds from Rajasthan, high-fiber Spelt & Emmer wheat from Madhya Pradesh, and Salem turmeric from Tamil Nadu."
  },
  {
    q: "What makes your traditional processing different from commercial brands?",
    a: "Commercial brands use chemical solvents, high temperatures exceeding 180°C, and synthetic bleaching agents. Prakruti uses time-honored slow wooden Ghani cold-pressing (kept strictly below 40°C) and traditional stone-milling. This preserves living enzymes, natural dietary fibers, antioxidants, and original aromas intact."
  },
  {
    q: "What certifications do Prakruti products hold?",
    a: "Our products are certified by Jaivik Bharat (FSSAI), USDA Organic, and packaged in ISO 22000 certified clean-room facilities. Furthermore, all our facilities adhere to Sedex ethical supply chain and fair-trade guidelines."
  },
  {
    q: "How does your eco-friendly packaging protect freshness?",
    a: "All staples are packed in food-grade, moisture-barrier multi-layer recyclable pouches and glass jars. This blocks oxygen and UV degradation without the need for artificial preservatives or chemical fumigants."
  }
];

const JOURNEY_STEPS = [
  {
    step: "01",
    title: "Native Heirloom Sowing",
    desc: "We preserve non-GMO native seed varieties sown in nutrient-rich organic soils nourished with bio-compost and green manure.",
    icon: "🌱"
  },
  {
    step: "02",
    title: "Generational Hand Harvest",
    desc: "Crops are hand-harvested at peak solar ripeness by experienced farming families to preserve vital micronutrients and flavor.",
    icon: "🧑‍🌾"
  },
  {
    step: "03",
    title: "Traditional Slow Processing",
    desc: "Extracted via slow wooden Ghana cold-pressing and slow stone-grinding without high heat, chemical solvents, or synthetic polish.",
    icon: "🪵"
  },
  {
    step: "04",
    title: "Eco-Fresh Sealed Delivery",
    desc: "Triple gravity-cleaned, batch-tested in NABL labs, and vacuum-sealed in food-grade recyclable packaging delivered to your doorstep.",
    icon: "📦"
  }
];

const AboutPage = ({ onShopClick }) => {
  const [openFaqIdx, setOpenFaqIdx] = useState(null);

  const toggleFaq = (idx) => {
    setOpenFaqIdx(prev => prev === idx ? null : idx);
  };

  return (
    <div className="about-page">
      
      {/* 1. Header Hero Banner */}
      <section className="about-hero" style={{ backgroundImage: `url(${aboutUsBanner})` }}>
        {/* Clean Graphic Banner */}
      </section>

      {/* 2. Key Metrics Strip */}
      <section className="about-stats-strip">
        <div className="container">
          <div className="about-stats-grid">
            <div className="about-stat-box">
              <span className="a-stat-num">25+</span>
              <span className="a-stat-lbl">Certified Pure Staples</span>
            </div>
            <div className="about-stat-box">
              <span className="a-stat-num">500+</span>
              <span className="a-stat-lbl">Partner Organic Farmers</span>
            </div>
            <div className="about-stat-box">
              <span className="a-stat-num">10,000+</span>
              <span className="a-stat-lbl">Healthy Indian Homes</span>
            </div>
            <div className="about-stat-box">
              <span className="a-stat-num">Lab</span>
              <span className="a-stat-lbl">NABL Lab Tested</span>
            </div>
          </div>
        </div>
      </section>

      {/* 2. Our Story Section */}
      <section className="about-story-section">
        <div className="container">
          <div className="story-grid">
            
            {/* Left Image Frame */}
            <div className="story-image-col">
              <div className="story-image-wrap">
                <img src={aboutCenterImg} alt="Prakruti Organic Ingredients & Cold Pressed Staples" className="story-main-img" />
                <div className="story-image-floating-badge">
                  <span className="badge-icon">🌾</span>
                  <div className="badge-text">
                    <strong>Organic Origins</strong>
                    <span>Direct Farm Traceable</span>
                  </div>
                </div>
              </div>
            </div>

            {/* Right Story Narrative */}
            <div className="story-text-col">
              <span className="section-eyebrow">OUR ORIGINS &amp; PHILOSOPHY</span>
              <h2 className="story-heading">Pure by Nature. Honest by Choice.</h2>
              
              <p className="story-paragraph">
                In a world crowded with ultra-processed foods, artificial preservatives, and chemically polished grains, 
                <strong> Prakruti</strong> was born from a fundamental return to basics: food should heal and energize, never harm.
              </p>
              
              <p className="story-paragraph">
                What began as a collaborative mission with traditional farmer cooperatives in Gujarat and Madhya Pradesh 
                has evolved into India's trusted daily organic staple brand. We work closely with generational farming families, 
                championing soil regeneration, biodiversity, and clean heritage processing techniques.
              </p>

              <div className="story-highlights-grid">
                <div className="story-hl-card">
                  <span className="hl-icon">🌱</span>
                  <div>
                    <h4>Chemical-Free Soil</h4>
                    <p>Zero synthetic pesticides, non-GMO heirloom seeds.</p>
                  </div>
                </div>
                <div className="story-hl-card">
                  <span className="hl-icon">🏺</span>
                  <div>
                    <h4>Traditional Slow Cold-Press</h4>
                    <p>Wooden Ghani extraction below 40°C to keep enzymes intact.</p>
                  </div>
                </div>
                <div className="story-hl-card">
                  <span className="hl-icon">🤝</span>
                  <div>
                    <h4>Direct Farmer Trade</h4>
                    <p>Fair compensation empowering 500+ rural farm families.</p>
                  </div>
                </div>
                <div className="story-hl-card">
                  <span className="hl-icon">🔬</span>
                  <div>
                    <h4>NABL Lab Verified</h4>
                    <p>Every single batch certified for purity and safety.</p>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      {/* 3. Mission & Vision */}
      <section className="about-mission-section">
        <div className="container">
          <div className="mission-grid">
            <div className="mission-card mission-card-primary">
              <div className="mission-icon-box">🎯</div>
              <span className="mission-tag">PURPOSE &amp; DRIVE</span>
              <h3 className="mission-title">Our Mission</h3>
              <p className="mission-text">
                To make unpolished, nutrient-dense, certified organic daily staples accessible to every household across India, 
                while empowering rural smallholder farming communities with fair, sustainable livelihoods and preserving ecological balance.
              </p>
            </div>
            
            <div className="mission-card mission-card-secondary">
              <div className="mission-icon-box">👁️</div>
              <span className="mission-tag">LONG-TERM ASPIRATION</span>
              <h3 className="mission-title">Our Vision</h3>
              <p className="mission-text">
                To be India's most trusted natural nutrition brand, leading a national transformation toward chemical-free living, 
                transparent farm traceability, and ancient Vedic wisdom backed by rigorous modern food science.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* 4. Our 5 Core Values */}
      <section className="about-values-section">
        <div className="container">
          <div className="section-header-centered">
            <span className="section-eyebrow">OUR GUIDING PRINCIPLES</span>
            <h2 className="section-heading">The 5 Pillars of Prakruti</h2>
            <p className="section-desc">The core convictions that guide our farming partnerships, processing methods, and customer commitment</p>
          </div>

          <div className="values-grid">
            <div className="value-card">
              <div className="val-icon-wrap">🌱</div>
              <h4 className="val-title">Pure &amp; Natural</h4>
              <p className="val-desc">Zero chemical pesticides, synthetic polish, artificial colors, or chemical preservatives.</p>
            </div>
            
            <div className="value-card">
              <div className="val-icon-wrap">🚜</div>
              <h4 className="val-title">Direct Farm Sourcing</h4>
              <p className="val-desc">Fair, ethical partnerships directly with verified organic farming cooperatives.</p>
            </div>
            
            <div className="value-card">
              <div className="val-icon-wrap">🏺</div>
              <h4 className="val-title">Time-Honored Wisdom</h4>
              <p className="val-desc">Wooden Ghani cold-pressing, Bilona ghee churning, and slow stone grinding.</p>
            </div>
            
            <div className="value-card">
              <div className="val-icon-wrap">🔬</div>
              <h4 className="val-title">Scientific Purity</h4>
              <p className="val-desc">Triple-stage NABL lab testing for heavy metals, moisture, and zero adulteration.</p>
            </div>
            
            <div className="value-card">
              <div className="val-icon-wrap">👪</div>
              <h4 className="val-title">Family Health First</h4>
              <p className="val-desc">We only harvest and pack what we are completely confident serving our own children.</p>
            </div>
          </div>
        </div>
      </section>

      {/* 5. From Source to Shelf Journey Timeline */}
      <section className="about-journey-section">
        <div className="container">
          <div className="section-header-centered">
            <span className="section-eyebrow">OUR PROCESS</span>
            <h2 className="section-heading">From Sacred Soil to Kitchen Shelf</h2>
            <p className="section-desc">A transparent 4-stage journey ensuring uncompromised nutritional integrity</p>
          </div>

          <div className="journey-grid">
            {JOURNEY_STEPS.map((stepItem, idx) => (
              <div key={idx} className="journey-card">
                <div className="journey-step-badge">{stepItem.step}</div>
                <div className="journey-icon">{stepItem.icon}</div>
                <h3 className="journey-title">{stepItem.title}</h3>
                <p className="journey-desc">{stepItem.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* 6. Purity Certifications Strip */}
      <section className="about-certifications-section">
        <div className="container">
          <div className="cert-header">
            <span className="section-eyebrow">ACCREDITED STANDARDS</span>
            <h3 className="cert-title">Certified for Purity &amp; Quality</h3>
          </div>
          
          <div className="cert-badges-grid">
            <div className="cert-badge-box">
              <img src={jaivikBharatImg} alt="Jaivik Bharat FSSAI Organic Certification" />
              <span>Jaivik Bharat (FSSAI)</span>
            </div>
            <div className="cert-badge-box">
              <img src={usdaLogoImg} alt="USDA Organic Certified" />
              <span>USDA Organic</span>
            </div>
            <div className="cert-badge-box">
              <img src={labTestedImg} alt="NABL Accredited Lab Tested" />
              <span>NABL Lab Tested</span>
            </div>
            <div className="cert-badge-box">
              <img src={fssaiImg} alt="FSSAI Food Safety Standard" />
              <span>FSSAI Certified</span>
            </div>
            <div className="cert-badge-box">
              <img src={sedexImg} alt="Sedex Ethical Trade Certification" />
              <span>Sedex Ethical Sourcing</span>
            </div>
          </div>
        </div>
      </section>

      {/* 7. Why Families Choose Prakruti Feature CTA */}
      <section className="about-promo-section">
        <div className="container">
          <div className="about-promo-grid">
            
            <div className="about-promo-copy">
              <span className="promo-badge">THE PRAKRUTI PROMISE</span>
              <h2 className="promo-heading">Every Product.<br />Every Promise.</h2>
              
              <ul className="promo-checklist">
                <li>
                  <span className="check-box">✓</span>
                  <div>
                    <strong>Unpolished &amp; Chemical Free</strong>
                    <p>No synthetic polish, toxic fumigants, or artificial colors.</p>
                  </div>
                </li>
                <li>
                  <span className="check-box">✓</span>
                  <div>
                    <strong>Native High-Curcumin &amp; High-Fiber</strong>
                    <p>Heirloom varieties preserved with maximum natural nutrition.</p>
                  </div>
                </li>
                <li>
                  <span className="check-box">✓</span>
                  <div>
                    <strong>Vacuum-Sealed Eco Packaging</strong>
                    <p>Tamper-evident food-grade pouches to preserve fresh aroma.</p>
                  </div>
                </li>
                <li>
                  <span className="check-box">✓</span>
                  <div>
                    <strong>Trusted by 10,000+ Indian Families</strong>
                    <p>Consistent 4.9★ rating for taste, freshness, and purity.</p>
                  </div>
                </li>
              </ul>

              <button type="button" className="promo-cta-btn" onClick={onShopClick}>
                Explore Our Pure Organic Staples →
              </button>
            </div>

            <div className="about-promo-visual">
              <div className="promo-img-container">
                <img src={seedsImg} alt="Organic Seeds and Natural Spices" className="promo-main-img" />
                <div className="promo-floating-stat">
                  <span className="p-stat-num">Pure</span>
                  <span className="p-stat-text">Chemical Free Guarantee</span>
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      {/* 8. FAQs Accordion */}
      <section className="about-faq-section">
        <div className="container">
          <div className="section-header-centered">
            <span className="section-eyebrow">TRANSPARENCY &amp; TRUST</span>
            <h2 className="section-heading">Frequently Asked Questions</h2>
            <p className="section-desc">Clear answers about our organic sourcing, purity guarantees, and processing standards</p>
          </div>

          <div className="faq-accordion-list">
            {FAQ_ITEMS.map((item, idx) => {
              const isOpen = openFaqIdx === idx;
              return (
                <div key={idx} className={`faq-card ${isOpen ? 'open' : ''}`}>
                  <button 
                    type="button" 
                    className="faq-question-btn"
                    onClick={() => toggleFaq(idx)}
                    aria-expanded={isOpen}
                  >
                    <span className="faq-q-text">{item.q}</span>
                    <span className="faq-toggle-icon">{isOpen ? '−' : '+'}</span>
                  </button>
                  {isOpen && (
                    <div className="faq-answer-pane">
                      <p>{item.a}</p>
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* 9. Closing CTA Banner */}
      <section className="about-closing-section" style={{ backgroundImage: `linear-gradient(rgba(19, 44, 21, 0.9), rgba(19, 44, 21, 0.95)), url(${sectionBg})` }}>
        <div className="container">
          <div className="closing-box">
            <span className="closing-badge">🌱 JOIN THE MOVEMENT</span>
            <h2 className="closing-title">Bring Home Purity.<br />Nourish Your Loved Ones.</h2>
            <p className="closing-desc">
              Experience the unmatched vitality and authentic flavors of certified organic daily staples 
              delivered straight from the farmlands to your doorstep.
            </p>
            <div className="closing-actions">
              <button type="button" className="btn-closing-primary" onClick={onShopClick}>
                Shop All Categories
              </button>
              <button 
                type="button" 
                className="btn-closing-outline" 
                onClick={() => {
                  onShopClick();
                  setTimeout(() => {
                    const el = document.getElementById('bestsellers') || document.querySelector('.best-sellers');
                    if (el) el.scrollIntoView({ behavior: 'smooth' });
                  }, 150);
                }}
              >
                View Bestsellers
              </button>
            </div>
          </div>
        </div>
      </section>

    </div>
  );
};

export default AboutPage;
