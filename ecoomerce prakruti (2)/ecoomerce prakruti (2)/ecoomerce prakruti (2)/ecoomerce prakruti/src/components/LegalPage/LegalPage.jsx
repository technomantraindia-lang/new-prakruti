import React from 'react';
import './LegalPage.css';

const policySections = [
  {
    title: 'Information We Collect',
    text: 'We collect details you share while creating an account, placing an order, contacting support, or subscribing to updates. This can include your name, phone number, email address, shipping address, order details, and payment status.'
  },
  {
    title: 'How We Use Your Information',
    text: 'Your information is used to process orders, deliver products, provide customer support, improve our website experience, prevent misuse, and send service or promotional updates when permitted.'
  },
  {
    title: 'Payments And Security',
    text: 'Payments are processed through secure payment partners. We do not store sensitive card or banking credentials on our website.'
  },
  {
    title: 'Sharing Of Information',
    text: 'We only share information with trusted service partners such as logistics, payment, technology, and support providers when required to complete your order or provide service.'
  },
  {
    title: 'Your Choices',
    text: 'You may contact us to update your personal details, unsubscribe from marketing communication, or request assistance with your account information.'
  }
];

const termsSections = [
  {
    title: 'Use Of Website',
    text: 'By using the Prakruti website, you agree to use it only for lawful personal shopping and information purposes. Misuse, scraping, fraud, or interference with the website is not permitted.'
  },
  {
    title: 'Products And Pricing',
    text: 'Product images, prices, offers, pack sizes, and availability may change from time to time. We try to keep all information accurate, but occasional errors may occur and will be corrected when noticed.'
  },
  {
    title: 'Orders And Delivery',
    text: 'Orders are accepted subject to product availability, payment confirmation, and serviceable delivery locations. Delivery timelines are estimates and may vary due to logistics or external conditions.'
  },
  {
    title: 'Cancellations, Returns And Refunds',
    text: 'Return and refund eligibility depends on product condition, category, and applicable food-safety rules. Please contact support promptly if there is a damaged, incorrect, or missing item.'
  },
  {
    title: 'Customer Content',
    text: 'Reviews, messages, and feedback submitted on the website should be honest, respectful, and free from unlawful or misleading content. We may moderate content to protect customers and the brand experience.'
  }
];

const LegalPage = ({ type = 'privacy' }) => {
  const isTerms = type === 'terms';
  const title = isTerms ? 'Terms & Conditions' : 'Privacy Policy';
  const subtitle = isTerms
    ? 'Clear terms for using Prakruti services, placing orders, and interacting with our website.'
    : 'How Prakruti handles customer information with care, transparency, and responsibility.';
  const sections = isTerms ? termsSections : policySections;

  return (
    <section className="legal-page">
      <div className="container legal-container">
        <div className="legal-hero">
          <span className="legal-eyebrow">Prakruti Customer Care</span>
          <h1>{title}</h1>
          <p>{subtitle}</p>
        </div>

        <div className="legal-content-card">
          {sections.map((section) => (
            <div className="legal-section" key={section.title}>
              <h2>{section.title}</h2>
              <p>{section.text}</p>
            </div>
          ))}

          <div className="legal-contact-note">
            <h2>Need Help?</h2>
            <p>For questions about this page, contact us at info@prakrutiorganic.com. Last updated: September 22, 2026.</p>
          </div>
        </div>
      </div>
    </section>
  );
};

export default LegalPage;
