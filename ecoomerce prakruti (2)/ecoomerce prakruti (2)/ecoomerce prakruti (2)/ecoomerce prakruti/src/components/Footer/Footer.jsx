import React from 'react'
import './Footer.css'
import logo from '../../assets/images/Prakruti Logo_cc.png'

const Footer = ({ onHomeClick, onCategoriesClick, onPrivacyClick, onTermsClick }) => {
  return (
    <footer className="footer" id="contact">
      <div className="container footer-container">
        <div className="footer-columns">
          <div className="footer-col brand-col">
            <div className="footer-logo">
              <img src={logo} alt="Prakruti" className="footer-logo-img" />
              <span className="footer-logo-name">Prakruti</span>
            </div>
            <p className="brand-description">
              Rooted in Nature. Backed by Science. Sourcing the purest organic groceries for your daily wellness.
            </p>
            <div className="footer-socials">
              <a href="#facebook" aria-label="Facebook">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" /></svg>
              </a>
              <a href="#instagram" aria-label="Instagram">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4z" /></svg>
              </a>
              <a href="#youtube" aria-label="YouTube">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" /></svg>
              </a>
              <a href="mailto:hello@prakruti.com" aria-label="Email">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" /><polyline points="22,6 12,13 2,6" /></svg>
              </a>
            </div>
            <div className="payments-section">
              <span className="payments-title">2026 Secure Payments</span>
              <div className="payment-badges-row">
                <span className="pay-badge visa">Visa</span>
                <span className="pay-badge mc">Mastercard</span>
                <span className="pay-badge upi">UPI</span>
                <span className="pay-badge rupay">RuPay</span>
              </div>
            </div>
          </div>

          <div className="footer-col links-col">
            <h4 className="footer-col-title">Quick Links</h4>
            <ul className="footer-links-list">
              <li><a href="#" onClick={(e) => { e.preventDefault(); if (onHomeClick) onHomeClick(); }}>Home</a></li>
              <li><a href="#shop" onClick={(e) => { e.preventDefault(); if (onCategoriesClick) onCategoriesClick(); }}>Shop by Category</a></li>
              <li><a href="#all-products">Best Sellers</a></li>
              <li><a href="#about">About Us</a></li>
              <li><a href="#contact">Contact Us</a></li>
              <li><a href="#privacy" onClick={(e) => { e.preventDefault(); if (onPrivacyClick) onPrivacyClick(); }}>Privacy Policy</a></li>
              <li><a href="#terms" onClick={(e) => { e.preventDefault(); if (onTermsClick) onTermsClick(); }}>Terms &amp; Conditions</a></li>
            </ul>
          </div>

          <div className="footer-col contact-col">
            <h4 className="footer-col-title">Contact Us</h4>
            <div className="footer-contact-items">
              <p className="contact-meta-item">
                <span className="contact-label" aria-hidden="true">@</span> hello@prakruti.com
              </p>
              <p className="contact-meta-item">
                <span className="contact-label" aria-hidden="true">*</span> Mon - Sat: 9AM - 7PM
              </p>
              <p className="contact-meta-item">
                <span className="contact-label" aria-hidden="true">#</span> Pan India Delivery
              </p>
            </div>
          </div>
        </div>

        <div className="footer-watermark-svg">
          <svg viewBox="0 0 320 350" xmlns="http://www.w3.org/2000/svg">
            <g stroke="currentColor" strokeWidth="2.5" fill="none" opacity="0.3">
              <circle cx="160" cy="130" r="95" strokeWidth="1" strokeDasharray="3,5" />
              <circle cx="160" cy="130" r="80" strokeWidth="1.5" />
              <path d="M160 135 Q130 110 110 85" />
              <path d="M160 135 Q190 110 210 85" />
              <path d="M160 145 Q125 145 95 135" />
              <path d="M160 145 Q195 145 225 135" />
              <path d="M160 120 Q160 80 160 55" />
              <circle cx="160" cy="120" r="7" />
              <path d="M160 132 C154 126, 142 120, 134 116 C140 122, 150 128, 156 134 Z" />
              <path d="M160 132 C166 126, 178 120, 186 116 C180 122, 170 128, 164 134 Z" />
              <path d="M160 132 C157 140, 155 160, 157 175 C155 190, 154 210, 158 230 C162 230, 165 190, 163 175 C165 160, 163 140, 160 132 Z" />
              <path d="M158 228 Q140 250 110 260" />
              <path d="M162 228 Q180 250 210 260" />
              <path d="M159 230 Q130 270 90 285" />
              <path d="M161 230 Q190 270 230 285" />
              <path d="M160 230 L160 295" strokeWidth="3" />
            </g>
          </svg>
        </div>
      </div>
    </footer>
  )
}

export default Footer
