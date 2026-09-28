import React, { useEffect, useMemo, useState } from 'react'
import './HeroBanner.css'
import defaultBanner from '../../assets/images/packaging/banner.png'
import { apiClient } from '../../api/client'

const HeroBanner = () => {
  const [adminBanners, setAdminBanners] = useState([])
  const [activeIndex, setActiveIndex] = useState(0)

  useEffect(() => {
    let mounted = true

    apiClient('/banners').then((res) => {
      if (mounted && res.success && Array.isArray(res.data)) {
        setAdminBanners(res.data.filter((banner) => banner.image))
      }
    })

    return () => {
      mounted = false
    }
  }, [])

  const banners = useMemo(() => {
    if (adminBanners.length) {
      return adminBanners.map((banner) => ({
        id: banner.id,
        title: banner.title || 'Prakruti Organic Banner',
        image: banner.image,
        link: banner.link,
      }))
    }

    return [{
      id: 'default-prakruti-banner',
      title: 'Prakruti Organic',
      image: defaultBanner,
      link: '#categories',
    }]
  }, [adminBanners])

  useEffect(() => {
    if (banners.length <= 1) return undefined

    const timer = window.setInterval(() => {
      setActiveIndex((current) => (current + 1) % banners.length)
    }, 4500)

    return () => window.clearInterval(timer)
  }, [banners.length])

  useEffect(() => {
    if (activeIndex >= banners.length) {
      setActiveIndex(0)
    }
  }, [activeIndex, banners.length])

  const goToSlide = (index) => {
    setActiveIndex((index + banners.length) % banners.length)
  }

  const handleBannerClick = (banner) => {
    if (!banner.link) return

    if (banner.link.startsWith('#')) {
      window.location.hash = banner.link.replace(/^#/, '')
      return
    }

    window.location.href = banner.link
  }

  const handleImageError = (event) => {
    // A banner can be removed from storage before a browser refreshes its API
    // response. Keep the carousel usable instead of leaving a broken image.
    if (event.currentTarget.dataset.fallbackApplied === 'true') return

    event.currentTarget.dataset.fallbackApplied = 'true'
    event.currentTarget.src = defaultBanner
  }

  return (
    <section className="hero-banner-carousel" aria-label="Prakruti home banner carousel">
      <div
        className="hero-banner-track"
        style={{ transform: `translateX(-${activeIndex * 100}%)` }}
      >
        {banners.map((banner) => {
          const Wrapper = banner.link ? 'button' : 'div'

          return (
            <Wrapper
              key={banner.id}
              type={banner.link ? 'button' : undefined}
              className="hero-banner-slide"
              onClick={banner.link ? () => handleBannerClick(banner) : undefined}
              aria-label={banner.link ? `${banner.title} - open banner link` : banner.title}
            >
              <img
                src={banner.image}
                alt={banner.title}
                className="hero-banner-image"
                onError={handleImageError}
              />
            </Wrapper>
          )
        })}
      </div>

      {banners.length > 1 && (
        <>
          <button type="button" className="hero-carousel-arrow hero-carousel-prev" onClick={() => goToSlide(activeIndex - 1)} aria-label="Previous banner">
            ‹
          </button>
          <button type="button" className="hero-carousel-arrow hero-carousel-next" onClick={() => goToSlide(activeIndex + 1)} aria-label="Next banner">
            ›
          </button>
          <div className="hero-carousel-dots" aria-label="Banner carousel pagination">
            {banners.map((banner, index) => (
              <button
                key={`${banner.id}-dot`}
                type="button"
                className={index === activeIndex ? 'active' : ''}
                onClick={() => goToSlide(index)}
                aria-label={`Show banner ${index + 1}`}
                aria-current={index === activeIndex ? 'true' : undefined}
              />
            ))}
          </div>
        </>
      )}
    </section>
  )
}

export default HeroBanner
