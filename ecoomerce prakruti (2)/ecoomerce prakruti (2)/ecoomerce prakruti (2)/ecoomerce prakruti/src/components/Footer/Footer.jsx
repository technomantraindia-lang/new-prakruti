import React from 'react'
import './Footer.css'
import logo from '../../assets/images/Prakruti Logo_cc.png'
import { FiClock, FiMail, FiMapPin, FiMessageCircle } from 'react-icons/fi'

const BUSINESS_EMAIL = 'info@prakrutiorganic.com'

const fallbackContact = {
  email: BUSINESS_EMAIL,
  address: '123, Green Valley, Near Organic Park, Ahmedabad, Gujarat - 380060, India'
}

const Footer = ({ onHomeClick, onCategoriesClick, onPrivacyClick, onTermsClick }) => {
  const emailHref = `mailto:${BUSINESS_EMAIL}`

  return (
    <footer className="footer" id="contact">
      <div className="footer-main">
        <div className="container footer-container">
          <div className="footer-grid">
            <div className="footer-brand-panel">
              <div className="footer-logo">
                <img src={logo} alt="Prakruti" className="footer-logo-img" />
                <div>
                  <span className="footer-logo-name">Prakruti</span>
                  <span className="footer-logo-tagline">Crafted Traditionally. Tested Scientifically.</span>
                </div>
              </div>
              <p className="brand-description">
                Pure organic groceries sourced with care, tested for trust, and packed for your daily family wellness.
              </p>
              <div className="footer-trust-row">
                <span>100% Natural</span>
                <span>Secure Payments</span>
                <span>Pan India Delivery</span>
              </div>
            </div>

            <div className="footer-col links-col">
              <h4 className="footer-col-title">Quick Links</h4>
              <ul className="footer-links-list">
                <li><a href="#" onClick={(e) => { e.preventDefault(); if (onHomeClick) onHomeClick(); }}>Home</a></li>
                <li><a href="#shop" onClick={(e) => { e.preventDefault(); if (onCategoriesClick) onCategoriesClick(); }}>Shop by Category</a></li>
                <li><a href="#bestsellers">Best Sellers</a></li>
                <li><a href="#about">About Us</a></li>
                <li><a href="#contact">Contact Us</a></li>
              </ul>
            </div>

            <div className="footer-col help-col">
              <h4 className="footer-col-title">Customer Care</h4>
              <ul className="footer-links-list">
                <li><a href="#privacy" onClick={(e) => { e.preventDefault(); if (onPrivacyClick) onPrivacyClick(); }}>Privacy Policy</a></li>
                <li><a href="#terms" onClick={(e) => { e.preventDefault(); if (onTermsClick) onTermsClick(); }}>Terms &amp; Conditions</a></li>
                <li><a href="#family-pack">Family Pack</a></li>
                <li><a href="#farm-gallery">Farm Gallery</a></li>
              </ul>
              <div className="footer-payment-block">
                <span className="footer-payment-title">Secure Payments</span>
                <div className="payment-badges-row">
                  <span className="pay-badge">Visa</span>
                  <span className="pay-badge">Mastercard</span>
                  <span className="pay-badge">UPI</span>
                  <span className="pay-badge">RuPay</span>
                </div>
              </div>
            </div>

            <section className="footer-contact-section" aria-labelledby="footer-contact-title">
              <div className="footer-contact-heading">
                <span className="footer-contact-eyebrow">We are here to help</span>
                <h3 id="footer-contact-title">Contact Information</h3>
              </div>

              <div className="footer-contact-list">
                <div className="footer-contact-item">
                  <span className="footer-contact-icon" aria-hidden="true"><FiClock /></span>
                  <div>
                    <h4>Support Hours</h4>
                    <strong>Mon - Sat: 9:00 AM to 6:00 PM</strong>
                    <span>Reach us by email for support</span>
                  </div>
                </div>

                <a className="footer-contact-item" href={emailHref}>
                  <span className="footer-contact-icon" aria-hidden="true"><FiMail /></span>
                  <div>
                    <h4>Email</h4>
                    <strong>{BUSINESS_EMAIL}</strong>
                    <span>We'll reply as soon as possible</span>
                  </div>
                </a>

                <div className="footer-contact-item">
                  <span className="footer-contact-icon" aria-hidden="true"><FiMapPin /></span>
                  <div>
                    <h4>Address</h4>
                    <strong>Prakruti Natural Foods Pvt. Ltd.</strong>
                    <span>{fallbackContact.address}</span>
                  </div>
                </div>

                <a className="footer-contact-item" href={emailHref}>
                  <span className="footer-contact-icon" aria-hidden="true"><FiMessageCircle /></span>
                  <div>
                    <h4>Customer Support</h4>
                    <strong>{BUSINESS_EMAIL}</strong>
                    <span>Email us anytime</span>
                  </div>
                </a>
              </div>
            </section>
          </div>
        </div>
      </div>

      <div className="footer-bottom-bar">
        <div className="container footer-container footer-bottom">
          <span>&copy; 2026 Prakruti. All rights reserved.</span>
        </div>
      </div>
    </footer>
  )
}

export default Footer
