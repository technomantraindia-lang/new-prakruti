import React from 'react'
import { motion, useReducedMotion } from 'framer-motion'
import './WellnessStaples.css'
import cerealsImg from '../../assets/images/cerals .png'
import seedsImg from '../../assets/images/seeds.png'
import spicesImg from '../../assets/images/indian spices.png'
import milletsImg from '../../assets/images/milltet.png'
import oilsGheeImg from '../../assets/images/oil and ghe.png'
import sweetenersImg from '../../assets/images/sweeterns.png'
import riceFlourImg from '../../assets/images/rice flour.png'

const staples = [
  {
    name: 'Pulses',
    image: cerealsImg,
    href: '#pulses'
  },
  {
    name: 'Seeds',
    image: seedsImg,
    href: '#seeds'
  },
  {
    name: 'Spices',
    image: spicesImg,
    href: '#spices'
  },
  {
    name: 'Millets',
    image: milletsImg,
    href: '#millets'
  },
  {
    name: 'Oils & Ghee',
    image: oilsGheeImg,
    href: '#oils-ghee'
  },
  {
    name: 'Rice & Flours',
    image: riceFlourImg,
    href: '#rice-flours'
  },
  {
    name: 'Sweeteners',
    image: sweetenersImg,
    href: '#sweeteners'
  }
]

const stapleCategoryMap = {
  'Pulses': 'Cereal & Pulses',
  'Seeds': 'Healthy Seeds',
  'Spices': 'Indian Spices',
  'Millets': 'Millets',
  'Oils & Ghee': 'Oils & Ghee',
  'Rice & Flours': 'Rice & Flours',
  'Sweeteners': 'Sweeteners',
}

const WellnessStaples = ({ onCategoryClick }) => {
  const shouldReduceMotion = useReducedMotion()

  return (
    <motion.section
      className="wellness-staples"
      initial="hidden"
      whileInView="visible"
      viewport={{ once: true, margin: '-70px' }}
    >
      <div className="container">
        <div className="section-header">
          <h2 className="section-title">Shop by Wellness Staples</h2>
        </div>

        <motion.div
          className="staples-marquee"
          variants={{
            hidden: {},
            visible: { transition: { staggerChildren: 0.13 } }
          }}
        >
          {[...staples, ...staples].map((staple, index) => (
            <motion.a
              key={index}
              href={staple.href}
              className="staple-item"
              aria-label={`Shop ${staple.name}`}
              onClick={(event) => {
                if (!onCategoryClick) return
                event.preventDefault()
                onCategoryClick(stapleCategoryMap[staple.name] || staple.name)
              }}
              variants={{
                hidden: { opacity: 0, y: shouldReduceMotion ? 0 : 34, scale: shouldReduceMotion ? 1 : 0.88 },
                visible: {
                  opacity: 1,
                  y: 0,
                  scale: 1,
                  transition: { duration: 0.65, ease: [0.25, 1, 0.5, 1] }
                }
              }}
              whileHover={shouldReduceMotion ? undefined : { y: -12 }}
              whileTap={shouldReduceMotion ? undefined : { scale: 0.96 }}
            >
              <div className="staple-circle">
                <img
                  src={staple.image}
                  alt={staple.name}
                  className="staple-img"
                  loading="lazy"
                />
              </div>
              <h3 className="staple-name">{staple.name}</h3>
            </motion.a>
          ))}
        </motion.div>
      </div>
    </motion.section>
  )
}

export default WellnessStaples
