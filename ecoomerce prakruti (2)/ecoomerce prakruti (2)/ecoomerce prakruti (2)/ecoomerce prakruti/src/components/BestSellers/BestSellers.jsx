import React, { useState } from 'react'
import './BestSellers.css'
import { useCart } from '../../context/CartContext'

import masoorDalImg from '../../assets/images/bestseller_masoor_dal.jpg'
import girGheeImg from '../../assets/images/ghee.png'
import jeerakaImg from '../../assets/images/bestseller_jeeraka.jpg'
import bengaluGunduImg from '../../assets/images/bestseller_bengalu_gundu.jpg'
import tuarDalImg from '../../assets/images/bestseller_tuar_dal.jpg'

export const BEST_SELLER_PRODUCTS = [
  {
    id: 1,
    name: 'Masoor Dal (Split Lentil)\n(1kg)',
    displayName: 'Masoor Dal (Split Lentil) (1kg)',
    rating: 5,
    reviews: 34,
    originalPrice: 160,
    price: 120,
    image: masoorDalImg,
    category: 'Cereal & Pulses'
  },
  {
    id: 29,
    name: 'Gir Organic A2 Desi\nCow Ghee (500ml)',
    displayName: 'Gir Organic A2 Desi Cow Ghee (500ml)',
    rating: 5,
    reviews: 28,
    originalPrice: 750,
    price: 599,
    image: girGheeImg,
    category: 'Oils & Ghee'
  },
  {
    id: 50,
    name: 'Pilipili Jeeraka\n(200g)',
    displayName: 'Pilipili Jeeraka (200g)',
    rating: 5,
    reviews: 12,
    originalPrice: 200,
    price: 150,
    image: jeerakaImg,
    category: 'Indian Spices'
  },
  {
    id: 51,
    name: 'Ralii Bengalu Gundu\n(2kg)',
    displayName: 'Ralii Bengalu Gundu (2kg)',
    rating: 5,
    reviews: 10,
    originalPrice: 160,
    price: 110,
    image: bengaluGunduImg,
    category: 'Cereal & Pulses'
  },
  {
    id: 5,
    name: 'Tuar Dal/Tur/Arhar\n(1kg)',
    displayName: 'Tuar Dal/Tur/Arhar (1kg)',
    rating: 5,
    reviews: 14,
    originalPrice: 160,
    price: 120,
    image: tuarDalImg,
    category: 'Cereal & Pulses'
  }
]

const BestSellers = ({ onViewProduct, onViewAll }) => {
  const [addingId, setAddingId] = useState(null)
  const { addToCart } = useCart()

  return (
    <section className="best-sellers" id="bestsellers">
      <div className="container">
        <div className="best-sellers-header">
          <h2 className="section-title">Best Sellers</h2>
          <button
            type="button"
            className="view-all-link"
            onClick={onViewAll}
          >
            <span>View All Products</span>
            <span className="view-all-arrow">→</span>
          </button>
        </div>

        <div className="products-grid">
          {BEST_SELLER_PRODUCTS.map((product) => (
            <div
              key={product.id}
              className="product-card"
              onClick={() => onViewProduct && onViewProduct(product.id)}
            >
              <div className="product-image-wrap">
                <img
                  src={product.image}
                  alt={product.displayName}
                  className="product-image"
                  loading="lazy"
                />
              </div>

              <div className="product-info">
                <h3 className="product-name">{product.displayName}</h3>

                <div className="product-rating">
                  <div className="stars">
                    {[...Array(product.rating || 5)].map((_, i) => (
                      <span key={i} className="star filled">★</span>
                    ))}
                  </div>
                  <span className="review-count">({product.reviews})</span>
                </div>

                <div className="product-pricing">
                  <span className="original-price">₹{product.originalPrice}</span>
                  <span className="current-price">₹{product.price}</span>
                </div>

                <button
                  className="add-to-cart-btn"
                  aria-label={`Add ${product.displayName} to Cart`}
                  disabled={addingId === product.id}
                  onClick={async (event) => {
                    event.stopPropagation()
                    setAddingId(product.id)
                    try {
                      await addToCart(product.id, 1)
                    } catch (e) {
                      console.error(e)
                    } finally {
                      setAddingId(null)
                    }
                  }}
                >
                  {addingId === product.id ? 'Adding...' : '+ Add to Cart'}
                </button>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}

export default BestSellers
