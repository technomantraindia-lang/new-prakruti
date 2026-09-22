import React from 'react'
import { motion, useReducedMotion } from 'framer-motion'
import './Benefits.css'

const benefits = [
  {
    icon: (
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 3.5 1 8a7 7 0 0 1-9 10z" />
        <path d="M9 22v-4" />
      </svg>
    ),
    title: 'Pure & Natural Goodness',
    description: 'Pure, natural and organic ingredients sourced directly from trusted farms.'
  },
  {
    icon: (
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M20 13c0 5-3.5 7.5-7.66 9.7a1 1 0 0 1-.68 0C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.5 3.8 17 5 19 5a1 1 0 0 1 1 1z" />
        <path d="m9 12 2 2 4-4" />
      </svg>
    ),
    title: 'Pesticide Free',
    description: 'Grown naturally without synthetic fertilizers or harmful chemical pesticides.'
  },
  {
    icon: (
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M10 2h4M12 2v6" />
        <path d="m21 22-4.3-8.6c-.6-1.2-.7-2.7-.1-3.9L18 6H6l1.4 3.5c.6 1.2.5 2.7-.1 3.9L3 22h18Z" />
        <line x1="3" y1="3" x2="21" y2="21" />
      </svg>
    ),
    title: 'Zero Chemical',
    description: 'Zero artificial colors, synthetic preservatives, or chemical processing.'
  }
]

const Benefits = () => {
  const shouldReduceMotion = useReducedMotion()

  const cardVariants = {
    hidden: (index) => ({
      opacity: 0,
      x: shouldReduceMotion ? 0 : [-34, 0, 34][index] || 0,
      y: shouldReduceMotion ? 0 : 24,
      scale: shouldReduceMotion ? 1 : 0.96,
    }),
    visible: (index) => ({
      opacity: 1,
      x: 0,
      y: 0,
      scale: 1,
      transition: {
        duration: 0.65,
        delay: shouldReduceMotion ? 0 : index * 0.12,
        ease: [0.25, 1, 0.5, 1],
      },
    }),
  }

  return (
    <section className="benefits-strip" aria-label="Our brand guarantees">
      <div className="container">
        <div className="benefits-row">
          {benefits.map((benefit, index) => (
            <motion.div
              key={index}
              className="benefit-item animate-stagger-benefit"
              custom={index}
              initial="hidden"
              whileInView="visible"
              viewport={{ once: true, amount: 0.35 }}
              variants={cardVariants}
              whileHover={shouldReduceMotion ? undefined : { y: -8, scale: 1.025 }}
            >
              <div className="benefit-icon-box">{benefit.icon}</div>
              <div className="benefit-info">
                <h4 className="benefit-title">{benefit.title}</h4>
                <p className="benefit-desc">{benefit.description}</p>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  )
}

export default Benefits
