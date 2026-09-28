import React, { useState } from 'react'
import { motion, AnimatePresence, useReducedMotion } from 'framer-motion'
import './FaqSection.css'

const faqItems = [
  {
    question: 'How long have you been in business?',
    answer: 'Prakruti has been serving pure organic goodness since 2021. What started as a direct farm-sourcing initiative for organic Ghee and Spices has grown into a trusted daily grocery brand serving over 10,000 health-conscious families across India.'
  },
  {
    question: 'What certifications do your products have?',
    answer: 'Our products are USDA Organic and Jaivik Bharat (FSSAI) organic certified. Furthermore, our processing and packaging units follow ISO 22000 standards, and every batch is certified by NABL-accredited labs for zero pesticide residue.'
  },
  {
    question: 'Where from do you source your products?',
    answer: 'We source directly from verified farmers and organic cooperatives across India. For example, our premium A2 Gir Cow Ghee comes from traditional pastures in Gujarat, our cold-pressed oils are pressed from Rajasthan seeds, and our Turmeric is grown in Salem, Tamil Nadu.'
  },
  {
    question: 'How do you guarantee quality and prevent adulteration?',
    answer: 'We enforce a strict 15-point quality check including multi-residue pesticide tests, heavy metal screens, double metal-detection processing, and hygienic vacuum-sealing. We guarantee 100% pure food with zero chemical additives or artificial colors.'
  },
  {
    question: 'How long does delivery take and is it traceable?',
    answer: 'We dispatch all orders within 24 hours via premium courier partners. Delivery takes 2–5 business days depending on your location. You will receive a tracking link via SMS/Email as soon as your vacuum-sealed, tamper-proof box is shipped.'
  }
]

const FaqSection = () => {
  const [activeIndex, setActiveIndex] = useState(null)
  const shouldReduceMotion = useReducedMotion()

  const toggleFaq = (index) => {
    setActiveIndex(activeIndex === index ? null : index)
  }

  return (
    <section className="faq-section" id="faq" aria-label="Frequently Asked Questions">
      <div className="container faq-container">
        <div className="faq-grid">
          
          {/* Left Column: Title, Subtitle and Contact Card */}
          <div className="faq-info-col">
            <div className="faq-badge">
              <span className="faq-badge-dot"></span>
              <span className="faq-badge-text">COMMON QUERIES</span>
            </div>

            <h2 className="faq-title">
              <span className="faq-title-green">Have Questions?</span>
              <span className="faq-title-brown">We Have Answers.</span>
            </h2>

            <p className="faq-intro-text">
              We believe in 100% transparency. If you have any other questions regarding our organic standards, farmers, or shipping, please don't hesitate to contact us.
            </p>
            
            <div className="faq-contact-card">
              <div className="contact-card-icon-wrap">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                  <rect width="20" height="16" x="2" y="4" rx="2" />
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                </svg>
              </div>
              <div className="contact-card-content">
                <h4 className="contact-card-heading">Still need help?</h4>
                <p className="contact-card-sub">Email our wellness support team at</p>
                <a href="mailto:info@prakrutiorganic.com" className="contact-card-email">info@prakrutiorganic.com</a>
              </div>
            </div>
          </div>

          {/* Right Column: Accordions */}
          <div className="faq-accordion-col">
            {faqItems.map((item, index) => {
              const isOpen = activeIndex === index
              return (
                <div 
                  key={index} 
                  className={`faq-item ${isOpen ? 'open' : ''}`}
                >
                  <button
                    type="button"
                    className="faq-question-btn"
                    onClick={() => toggleFaq(index)}
                    aria-expanded={isOpen}
                    aria-controls={`faq-answer-${index}`}
                  >
                    <span className="faq-question-text">{item.question}</span>
                    <span className={`faq-toggle-icon ${isOpen ? 'active' : ''}`}>
                      <svg 
                        width="14" 
                        height="14" 
                        viewBox="0 0 24 24" 
                        fill="none" 
                        stroke="currentColor" 
                        strokeWidth="2.5" 
                        strokeLinecap="round" 
                        strokeLinejoin="round"
                        style={{
                          transform: isOpen ? 'rotate(180deg)' : 'rotate(0deg)',
                          transition: 'transform 0.28s ease'
                        }}
                      >
                        <polyline points="6 9 12 15 18 9" />
                      </svg>
                    </span>
                  </button>

                  <AnimatePresence initial={false}>
                    {isOpen && (
                      <motion.div
                        id={`faq-answer-${index}`}
                        role="region"
                        aria-labelledby={`faq-question-${index}`}
                        initial={shouldReduceMotion ? { opacity: 0 } : { height: 0, opacity: 0 }}
                        animate={shouldReduceMotion ? { opacity: 1 } : { height: 'auto', opacity: 1 }}
                        exit={shouldReduceMotion ? { opacity: 0 } : { height: 0, opacity: 0 }}
                        transition={{ duration: 0.28, ease: [0.25, 1, 0.5, 1] }}
                        className="faq-answer-wrapper"
                      >
                        <div className="faq-answer-content">
                          <p>{item.answer}</p>
                        </div>
                      </motion.div>
                    )}
                  </AnimatePresence>
                </div>
              )
            })}
          </div>

        </div>
      </div>
    </section>
  )
}

export default FaqSection
