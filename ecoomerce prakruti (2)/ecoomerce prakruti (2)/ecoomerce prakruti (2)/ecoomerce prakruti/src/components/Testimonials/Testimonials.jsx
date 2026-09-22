import React, { useEffect, useState } from 'react'
import { AnimatePresence, motion, useReducedMotion } from 'framer-motion'
import './Testimonials.css'
import { reviewsApi } from '../../api/reviews'

const testimonials = [
  {
    id: 1,
    name: 'Priya S.',
    location: 'Bengaluru',
    rating: 5,
    review: 'The quality is exceptional. Feels like going back to our roots. My family loves the taste.',
    avatar: 'https://i.pravatar.cc/160?img=47'
  },
  {
    id: 2,
    name: 'Rohit M.',
    location: 'Pune',
    rating: 5,
    review: 'Pure, natural and trustworthy. From dals to ghee, everything feels carefully selected.',
    avatar: 'https://i.pravatar.cc/160?img=12'
  },
  {
    id: 3,
    name: 'Ananya K.',
    location: 'Hyderabad',
    rating: 5,
    review: 'Finally found a brand that cares about what we eat every day. Highly recommended.',
    avatar: 'https://i.pravatar.cc/160?img=32'
  },
  {
    id: 4,
    name: 'Meera D.',
    location: 'Mumbai',
    rating: 5,
    review: 'The packaging, aroma and freshness are all excellent. It has become our monthly staple order.',
    avatar: 'https://i.pravatar.cc/160?img=44'
  },
  {
    id: 5,
    name: 'Karan V.',
    location: 'Ahmedabad',
    rating: 5,
    review: 'Honest quality and very consistent. The ghee and pulses are now regulars in our kitchen.',
    avatar: 'https://i.pravatar.cc/160?img=13'
  }
]

const Testimonials = () => {
  const [activeIndex, setActiveIndex] = useState(0)
  const [customerTestimonials, setCustomerTestimonials] = useState([])
  const [form, setForm] = useState({ name: '', location: '', rating: 5, review: '' })
  const [isSubmitting, setIsSubmitting] = useState(false)
  const shouldReduceMotion = useReducedMotion()
  const allTestimonials = [...customerTestimonials, ...testimonials]

  const goToPrevious = () => {
    setActiveIndex((current) => (current - 1 + allTestimonials.length) % allTestimonials.length)
  }

  const goToNext = () => {
    setActiveIndex((current) => (current + 1) % allTestimonials.length)
  }

  const handleSubmit = async (event) => {
    event.preventDefault()
    if (!form.name.trim() || !form.review.trim()) return

    const payload = {
      name: form.name.trim(),
      location: form.location.trim() || 'Verified Customer',
      rating: Number(form.rating) || 5,
      review: form.review.trim()
    }

    setIsSubmitting(true)
    const res = await reviewsApi.addTestimonial(payload)
    setIsSubmitting(false)

    const savedReview = res.success && res.data
      ? { ...res.data, avatar: '' }
      : { id: `customer-${Date.now()}`, ...payload, avatar: '' }

    setCustomerTestimonials((current) => [savedReview, ...current])
    setActiveIndex(0)
    setForm({ name: '', location: '', rating: 5, review: '' })
  }

  useEffect(() => {
    let mounted = true
    reviewsApi.getTestimonials().then((res) => {
      if (!mounted || !res.success || !Array.isArray(res.data)) return
      setCustomerTestimonials(res.data.map((item) => ({ ...item, avatar: '' })))
    })
    return () => {
      mounted = false
    }
  }, [])

  useEffect(() => {
    if (shouldReduceMotion) return undefined

    const timer = window.setInterval(goToNext, 3600)
    return () => window.clearInterval(timer)
  }, [shouldReduceMotion])

  const visibleCards = [-1, 0, 1].map((offset) => {
    const index = (activeIndex + offset + allTestimonials.length) % allTestimonials.length
    return { ...allTestimonials[index], offset }
  })

  return (
    <section className="testimonials-section">
      <div className="container testimonials-container">
        <button className="nav-arrow prev-arrow" onClick={goToPrevious} aria-label="Previous testimonial">
          ‹
        </button>

        <div className="testimonials-inner">
          <div className="section-header">
            <h2 className="section-title">Loved by Thousand Families</h2>
          </div>

          <div className="testimonial-stage" aria-live="polite">
            <AnimatePresence initial={false}>
              {visibleCards.map((t) => (
                <motion.article
                  key={`${t.id}-${t.offset}`}
                  className={`testimonial-card testimonial-card-${t.offset === 0 ? 'active' : t.offset < 0 ? 'prev' : 'next'}`}
                  initial={{ opacity: 0, scale: 0.86, x: t.offset * 180 }}
                  animate={{
                    opacity: t.offset === 0 ? 1 : 0.42,
                    scale: t.offset === 0 ? 1 : 0.82,
                    x: shouldReduceMotion ? 0 : t.offset * 255,
                    y: t.offset === 0 ? 0 : 28,
                    rotate: shouldReduceMotion ? 0 : t.offset * -5,
                    zIndex: t.offset === 0 ? 3 : 1
                  }}
                  exit={{ opacity: 0, scale: 0.78 }}
                  transition={{ duration: 0.55, ease: [0.25, 1, 0.5, 1] }}
                  whileHover={shouldReduceMotion || t.offset !== 0 ? undefined : { y: -10, scale: 1.02 }}
                >
                  <div className="testimonial-stars">
                    {[...Array(t.rating)].map((_, i) => (
                      <span key={i} className="star-char">★</span>
                    ))}
                  </div>

                  <p className="testimonial-review">"{t.review}"</p>

                  <div className="testimonial-author">
                    <h4 className="author-name">{t.name}</h4>
                    <p className="author-location">{t.location}</p>
                  </div>
                </motion.article>
              ))}
            </AnimatePresence>
          </div>
        </div>

        <button className="nav-arrow next-arrow" onClick={goToNext} aria-label="Next testimonial">
          ›
        </button>
      </div>

      <form className="testimonial-submit-form" onSubmit={handleSubmit}>
        <div className="testimonial-form-header">
          <h3>Share Your Experience</h3>
          <p>Add your review and it will appear in the customer carousel.</p>
        </div>
        <div className="testimonial-form-grid">
          <input
            type="text"
            value={form.name}
            onChange={(event) => setForm((current) => ({ ...current, name: event.target.value }))}
            placeholder="Your name"
            required
          />
          <input
            type="text"
            value={form.location}
            onChange={(event) => setForm((current) => ({ ...current, location: event.target.value }))}
            placeholder="City"
          />
          <select
            value={form.rating}
            onChange={(event) => setForm((current) => ({ ...current, rating: event.target.value }))}
          >
            {[5, 4, 3, 2, 1].map((rating) => (
              <option key={rating} value={rating}>{rating} Star{rating > 1 ? 's' : ''}</option>
            ))}
          </select>
        </div>
        <textarea
          value={form.review}
          onChange={(event) => setForm((current) => ({ ...current, review: event.target.value }))}
          placeholder="Write your review"
          rows="4"
          required
        />
        <button type="submit" disabled={isSubmitting}>{isSubmitting ? 'Saving...' : 'Add Review'}</button>
      </form>
    </section>
  )
}

export default Testimonials
