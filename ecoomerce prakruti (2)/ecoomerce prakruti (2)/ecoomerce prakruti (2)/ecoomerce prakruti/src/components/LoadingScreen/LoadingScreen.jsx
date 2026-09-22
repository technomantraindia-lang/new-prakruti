import React, { useState, useEffect } from 'react';
import './LoadingScreen.css';
import logoImg from '../../assets/images/Prakruti Logo_cc.png';

const LoadingScreen = () => {
  const [fadeOut, setFadeOut] = useState(false);

  useEffect(() => {
    // Disable body scroll when loader is visible
    document.body.style.overflow = 'hidden';

    // Start fading out 600ms before unmount (App.jsx timer is 5.6s)
    const fadeTimer = setTimeout(() => {
      setFadeOut(true);
    }, 5000);

    return () => {
      clearTimeout(fadeTimer);
      // Restore normal scrolling on unmount
      document.body.style.overflow = '';
    };
  }, []);

  return (
    <div
      className={`loading-screen ${fadeOut ? 'fade-out' : ''}`}
      aria-label="Loading Prakruti"
    >
      {/* Background Decorative Faint Leaves */}
      <div className="corner-decor top-left" aria-hidden="true">
        <svg viewBox="0 0 100 100">
          <path d="M0,0 Q50,20 100,50 Q40,80 0,0 Z" fill="var(--color-primary)" opacity="0.05" />
          <path d="M0,0 Q20,50 50,100 Q80,40 0,0 Z" fill="var(--color-primary)" opacity="0.03" />
        </svg>
      </div>
      <div className="corner-decor bottom-right" aria-hidden="true">
        <svg viewBox="0 0 100 100">
          <path d="M100,100 Q50,80 0,50 Q60,20 100,100 Z" fill="var(--color-primary)" opacity="0.05" />
          <path d="M100,100 Q80,50 50,0 Q20,60 100,100 Z" fill="var(--color-primary)" opacity="0.03" />
        </svg>
      </div>

      <div className="loading-content-wrap">
        {/* Animated Leaf Ring Wrapper */}
        <div className="loading-logo-wrapper">
          <div className="leaf-ring-container">
            <svg className="leaf-ring-svg" viewBox="0 0 200 200">
              <circle cx="100" cy="100" r="80" fill="none" stroke="var(--color-primary)" strokeWidth="1.2" strokeDasharray="4, 7" opacity="0.22" />
              {[...Array(8)].map((_, i) => (
                <path
                  key={i}
                  className="ring-leaf"
                  d="M100,22 Q108,12 100,2 Q92,12 100,22"
                  fill="var(--color-primary)"
                  opacity="0.55"
                  transform={`rotate(${i * 45} 100 100)`}
                />
              ))}
            </svg>
          </div>
          {/* Logo Frame */}
          <div className="logo-img-container">
            <img src={logoImg} className="loading-logo" alt="Prakruti Logo" />
          </div>
        </div>

        {/* Brand Typography */}
        <div className="loading-brand-identity">
          <h1 className="loading-brand-title">Prakruti</h1>
          <p className="loading-tagline">Bringing Nature Closer...</p>
        </div>

        {/* Thin elegant progress bar */}
        <div className="loading-progress-container">
          <div className="loading-progress-track">
            <div className="loading-progress-bar"></div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default LoadingScreen;
