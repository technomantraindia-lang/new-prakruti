import React from 'react'
import './InstagramFeed.css'

const instagramPosts = [
  {
    id: 1,
    image: 'https://images.unsplash.com/photo-1586201375761-83865001e31c?w=300&h=300&fit=crop&q=80',
    alt: 'Organic lentils and pulses'
  },
  {
    id: 2,
    image: 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=300&h=300&fit=crop&q=80',
    alt: 'Fresh spices and herbs'
  },
  {
    id: 3,
    image: 'https://images.unsplash.com/photo-1628088062854-d1870b4553da?w=300&h=300&fit=crop&q=80',
    alt: 'Pure A2 cow ghee'
  },
  {
    id: 4,
    image: 'https://images.unsplash.com/photo-1615485925600-97237c4fc1ec?w=300&h=300&fit=crop&q=80',
    alt: 'Golden turmeric powder'
  },
  {
    id: 5,
    image: 'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=300&h=300&fit=crop&q=80',
    alt: 'Whole wheat flour'
  },
  {
    id: 6,
    image: 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=300&h=300&fit=crop&q=80',
    alt: 'Mixed dry fruits and nuts'
  }
]

const InstagramFeed = () => {
  return (
    <section className="instagram-feed">
      <div className="container">
        <div className="section-header-left">
          <h2 className="section-title">From Our Instagram</h2>
        </div>

        <div className="instagram-row">
          {instagramPosts.map((post) => (
            <div key={post.id} className="instagram-post">
              <div className="post-image-wrap">
                <img
                  src={post.image}
                  alt={post.alt}
                  className="post-image"
                  loading="lazy"
                />
                <div className="post-overlay">
                  <svg
                    className="instagram-icon"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                  >
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                  </svg>
                </div>
              </div>
            </div>
          ))}

          {/* 7th: Instagram Follow Us CTA Card */}
          <div className="instagram-follow-card">
            <div className="follow-card-content">
              <div className="follow-insta-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                  <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                  <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                  <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                </svg>
              </div>
              <p className="follow-text">
                Follow <span className="insta-handle">@prakruti</span> for daily wellness inspiration!
              </p>
              <a 
                href="https://instagram.com/prakruti" 
                className="btn btn-primary follow-btn"
                target="_blank"
                rel="noopener noreferrer"
              >
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round" style={{marginRight: '6px'}}>
                  <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                  <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                  <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                </svg>
                Follow Us
              </a>
            </div>
          </div>
        </div>

      </div>
    </section>
  )
}

export default InstagramFeed