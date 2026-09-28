import React, { useState } from 'react';
import './ContactPage.css';
import { api } from '../../services/api';

const ContactPage = ({ onShopClick }) => {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    subject: '',
    message: '',
    agree: false
  });
  const [submitted, setSubmitted] = useState(false);
  const [newsletterEmail, setNewsletterEmail] = useState('');
  const [subscribed, setSubscribed] = useState(false);

  const handleInputChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }));
  };

  const handleFormSubmit = async (e) => {
    e.preventDefault();
    if (!formData.name || !formData.email || !formData.message) {
      alert("Please fill in all required (*) fields.");
      return;
    }
    if (!formData.agree) {
      alert("Please agree to our Privacy Policy and Terms.");
      return;
    }
    
    setSubmitted(true);
    
    const res = await api.sendInquiry({
      name: formData.name,
      email: formData.email,
      msg: `[Subject: ${formData.subject || 'General'}] ${formData.message}`,
    });

    setSubmitted(false);
    setFormData({
      name: '',
      email: '',
      subject: '',
      message: '',
      agree: false
    });
    alert(res.message || "Thank you! Your message has been sent successfully. 🚀");
  };

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
    <div className="contact-page">
      
      {/* Main Columns Section (Form + Information) */}
      <section className="contact-main-section">
        <div className="container grid-contact-box">
          
          {/* Left Column: Form Card */}
          <div className="contact-form-card">
            <h2>Send Us a Message</h2>
            <p className="card-sub-info">Fill in the details below and our team will get back to you.</p>

            <form onSubmit={handleFormSubmit} className="interactive-contact-form">
              
              <div className="input-row-split">
                <div className="input-wrap icon-name">
                  <input 
                    type="text" 
                    name="name" 
                    placeholder="Your Name *"
                    value={formData.name}
                    onChange={handleInputChange}
                    required
                  />
                </div>
                
                <div className="input-wrap">
                  <input 
                    type="email" 
                    name="email" 
                    placeholder="Email Address *"
                    value={formData.email}
                    onChange={handleInputChange}
                    required
                  />
                </div>
              </div>

              <div className="input-row-split single-field-row">
                <div className="input-wrap icon-subject">
                  <input
                    type="text"
                    name="subject"
                    placeholder="Subject *"
                    value={formData.subject}
                    onChange={handleInputChange}
                    required
                  />
                </div>
              </div>

              <div className="textarea-wrap">
                <textarea 
                  name="message" 
                  rows="5" 
                  placeholder="Your Message *"
                  value={formData.message}
                  onChange={handleInputChange}
                  required
                ></textarea>
              </div>

              <div className="policy-checkbox-row">
                <label className="checkbox-container-policy">
                  <input 
                    type="checkbox" 
                    name="agree" 
                    checked={formData.agree}
                    onChange={handleInputChange}
                  />
                  <span className="policy-checkmark"></span>
                  <span className="policy-label-text">
                    I agree to the <a href="#privacy">Privacy Policy</a> and <a href="#terms">Terms & Conditions</a>
                  </span>
                </label>
              </div>

              <button 
                type="submit" 
                className={`btn btn-primary submit-msg-btn ${submitted ? 'loading' : ''}`}
                disabled={submitted}
              >
                {submitted ? 'Sending Message...' : 'Send Message'}
              </button>

            </form>
          </div>

          {/* Right Column: Contact Info + Map Card */}
          <div className="contact-info-panel">
            
            <h2>Contact Information</h2>
            
            <div className="info-list-rebuilt">
              
              <div className="info-item-card">
                <div className="info-item-icon">📞</div>
                <div className="info-item-text">
                  <h4>Support Hours</h4>
                  <p className="main-info-txt">Mon - Sat: 9:00 AM to 6:00 PM</p>
                  <p className="sub-info-txt">Reach us by email for support</p>
                </div>
              </div>

              <div className="info-item-card">
                <div className="info-item-icon">✉️</div>
                <div className="info-item-text">
                  <h4>Email</h4>
                  <p className="main-info-txt">info@prakrutiorganic.com</p>
                  <p className="sub-info-txt">We'll reply as soon as possible</p>
                </div>
              </div>

              <div className="info-item-card">
                <div className="info-item-icon">📍</div>
                <div className="info-item-text">
                  <h4>Address</h4>
                  <p className="main-info-txt">Prakruti Natural Foods Pvt. Ltd.</p>
                  <p className="sub-info-txt">123, Green Valley, Near Organic Park, Ahmedabad, Gujarat - 380060, India</p>
                </div>
              </div>

              <a 
                href="mailto:info@prakrutiorganic.com" 
                target="_blank" 
                rel="noopener noreferrer" 
                className="info-item-card support-link-card"
              >
                <div className="info-item-icon green-support">💬</div>
                <div className="info-item-text">
                  <h4>Customer Support</h4>
                  <p className="main-info-txt">info@prakrutiorganic.com</p>
                  <p className="sub-info-txt font-green-link">Email us anytime</p>
                </div>
              </a>

            </div>

            {/* Google Maps Iframe mock card */}
            <div className="maps-location-frame">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3671.697920150993!2d72.571362!3d23.022505!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e848aba5bd449%3A0x4fccd7d142bdfb!2sAhmedabad%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1655000000000!5m2!1sen!2sin" 
                width="100%" 
                height="260" 
                style={{ border: 0, borderRadius: '16px' }} 
                allowFullScreen="" 
                loading="lazy" 
                referrerPolicy="no-referrer-when-downgrade"
                title="Prakruti Office Map Location"
              ></iframe>
            </div>

          </div>

        </div>
      </section>

      {/* 3. Trust Row Banner */}
      <section className="contact-trust-bar">
        <div className="container trust-flex-grid">
          <div className="trust-badge">
            <span className="badge-icon">🌿</span>
            <div className="badge-text">
              <h4>Natural Products</h4>
              <p>Pure & Unpolished Goodness</p>
            </div>
          </div>
          <div className="trust-badge">
            <span className="badge-icon">🔒</span>
            <div className="badge-text">
              <h4>Secure Payments</h4>
              <p>Safe & Encrypted Transactions</p>
            </div>
          </div>
          <div className="trust-badge">
            <span className="badge-icon">🚚</span>
            <div className="badge-text">
              <h4>Fast & Reliable Delivery</h4>
              <p>Pan India Shipping</p>
            </div>
          </div>
          <div className="trust-badge">
            <span className="badge-icon">💬</span>
            <div className="badge-text">
              <h4>Customer Support</h4>
              <p>We're Here to Help</p>
            </div>
          </div>
        </div>
      </section>



    </div>
  );
};

export default ContactPage;
