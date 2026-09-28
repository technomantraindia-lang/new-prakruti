import React from 'react'
import { FiArrowRight } from 'react-icons/fi'
import './PromoBanner.css'

import familyPackBg from '../../assets/images/22e5a66a-525b-4ef8-84aa-107d634de812.png'

const PromoBanner = () => {
  return (
    <section className="promo-banner-section" id="family-pack-banner">
      <div className="container promo-container-wrap">
        <div className="promo-banner-inner-custom" style={{ '--promo-background': `url("${familyPackBg}")` }}>
          <div className="promo-left-content">
            <div className="promo-kicker-row">
              <span>THE PRAKRUTI FAMILY PACK</span>
              <i aria-hidden="true" />
            </div>

            <h2 className="promo-headline">
              <span className="promo-headline-line">Your family.</span>
              <span className="promo-headline-line">Your favourites.</span>
              <em className="promo-headline-italic">One monthly pack.</em>
            </h2>

            <p className="promo-desc-text">
              Monthly groceries suggested around your family's size, age groups and food preferences. Adjust every quantity to make it yours.
            </p>

            <a href="#family-pack" className="promo-cta-btn">
              <span>Build My Family Pack</span>
              <FiArrowRight aria-hidden="true" />
            </a>

            <p className="promo-helper-text">
              Personalise your pack with staples your home uses every month.
            </p>
          </div>
        </div>
      </div>
    </section>
  )
}

export default PromoBanner
