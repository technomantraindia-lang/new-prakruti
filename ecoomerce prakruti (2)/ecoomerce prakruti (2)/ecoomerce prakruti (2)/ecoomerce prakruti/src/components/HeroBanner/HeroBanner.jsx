import React from 'react'
import './HeroBanner.css'
import bannerVideo from '../../assets/images/final banner.mp4'

const HeroBanner = () => {
  return (
    <section className="hero-banner-video" aria-label="Prakruti video banner">
      <video
        src={bannerVideo}
        autoPlay
        loop
        muted
        playsInline
        className="hero-video-element"
      />
    </section>
  )
}

export default HeroBanner
