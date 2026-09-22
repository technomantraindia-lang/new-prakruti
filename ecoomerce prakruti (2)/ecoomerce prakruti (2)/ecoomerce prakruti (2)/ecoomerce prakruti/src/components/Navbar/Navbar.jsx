import React, { useState } from 'react'
import './Navbar.css'
import logo from '../../assets/images/Prakruti Logo_cc.png'

const navLinks = [
  { label: 'Home', href: '#' },
  { label: 'Categories', href: '#categories' },
  { label: 'Family Pack', href: '#family-pack' },
  { label: 'Farm Gallery', href: '#farm-gallery' },
  { label: 'Education & Awareness', href: '#education' },
  { label: 'About Us', href: '#about' },
  { label: 'Contact Us', href: '#contact' },
]

const Navbar = ({ onAccountClick, currentPage, setCurrentPage, onCartClick, cartCount = 0, onSearch, currentUser, onLogout }) => {
  const [searchOpen, setSearchOpen] = useState(false)
  const [searchValue, setSearchValue] = useState('')
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)

  const handleSearchSubmit = () => {
    if (onSearch && searchValue.trim() !== '') {
      onSearch(searchValue);
    }
  };

  return (
    <header className="navbar">
      <div className="navbar-inner">
        {/* Logo */}
        <a 
          href="/" 
          className="navbar-logo" 
          aria-label="Prakruti Home"
          onClick={(e) => {
            e.preventDefault();
            setCurrentPage('home');
            window.scrollTo({ top: 0, behavior: 'smooth' });
          }}
        >
          <img src={logo} alt="Prakruti Logo" className="logo-img" />
          <div className="logo-text">
            <span className="logo-name">Prakruti</span>
            <span className="logo-tagline">Rooted in Nature. Backed by Science.</span>
          </div>
        </a>

        {/* Nav Links */}
        <nav className={`navbar-nav ${mobileMenuOpen ? 'open' : ''}`} aria-label="Main navigation">
          <ul className="nav-list">
            {navLinks.map((link) => {
              const isActive = (link.label === 'Home' && currentPage === 'home') || 
                               (link.label === 'Categories' && currentPage === 'categories') ||
                               (link.label === 'Family Pack' && currentPage === 'family-pack') ||
                               (link.label === 'Farm Gallery' && currentPage === 'farm-gallery') ||
                               (link.label === 'Education & Awareness' && currentPage === 'education') ||
                               (link.label === 'About Us' && currentPage === 'about') ||
                               (link.label === 'Contact Us' && currentPage === 'contact');
              return (
                <li key={link.label} className="nav-item">
                  <a 
                    href={link.href} 
                    className={`nav-link ${isActive ? 'active' : ''}`}
                    onClick={(e) => {
                      if (link.label === 'Home') {
                        e.preventDefault();
                        setCurrentPage('home');
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                      } else if (link.label === 'Categories') {
                        e.preventDefault();
                        setCurrentPage('categories');
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                      } else if (link.label === 'Family Pack') {
                        e.preventDefault();
                        setCurrentPage('family-pack');
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                      } else if (link.label === 'Farm Gallery') {
                        e.preventDefault();
                        setCurrentPage('farm-gallery');
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                      } else if (link.label === 'Education & Awareness') {
                        e.preventDefault();
                        setCurrentPage('education');
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                      } else if (link.label === 'About Us') {
                        e.preventDefault();
                        setCurrentPage('about');
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                      } else if (link.label === 'Contact Us') {
                        e.preventDefault();
                        setCurrentPage('contact');
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                      } else {
                        // Anchors for other pages on Home page
                        if (currentPage !== 'home') {
                          e.preventDefault();
                          setCurrentPage('home');
                          setTimeout(() => {
                            const id = link.href.replace('#', '');
                            const el = document.getElementById(id);
                            if (el) el.scrollIntoView({ behavior: 'smooth' });
                          }, 100);
                        }
                      }
                      setMobileMenuOpen(false);
                    }}
                  >
                    {link.label}
                  </a>
                </li>
              );
            })}
          </ul>
        </nav>

        {/* Right Actions */}
        <div className="navbar-actions">
          {/* Persistent Search Bar */}
          <div className="search-wrap persistent">
            <input
              type="text"
              className="search-input"
              placeholder="Search products..."
              value={searchValue}
              onChange={(e) => setSearchValue(e.target.value)}
              onKeyDown={(e) => {
                if (e.key === 'Enter') {
                  handleSearchSubmit();
                }
              }}
              aria-label="Search products"
            />
            <span className="search-icon-inside" onClick={handleSearchSubmit} style={{ cursor: 'pointer' }}>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
              </svg>
            </span>
          </div>

          {/* Account & User Status */}
          {currentUser ? (
            <div className="user-profile-nav">
              <button className="icon-btn account-btn" type="button" onClick={onAccountClick} title={currentUser.email}>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                  <circle cx="12" cy="7" r="4" />
                </svg>
                <span className="icon-label">{currentUser.name ? currentUser.name.split(' ')[0] : 'Account'}</span>
              </button>
              <button 
                type="button" 
                className="logout-link-btn"
                onClick={onLogout} 
                title="Logout"
              >
                Logout
              </button>
            </div>
          ) : (
            <button className="icon-btn account-btn" type="button" onClick={onAccountClick} aria-label="Account">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                <circle cx="12" cy="7" r="4" />
              </svg>
              <span className="icon-label">Account</span>
            </button>
          )}

          {/* Cart */}
          <button className="icon-btn cart-btn" aria-label="Cart" onClick={onCartClick}>
            <div className="cart-icon-wrap">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <circle cx="9" cy="21" r="1" />
                <circle cx="20" cy="21" r="1" />
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6" />
              </svg>
              <span className="cart-badge">{cartCount}</span>
            </div>
            <span className="icon-label">Cart</span>
          </button>

          {/* Mobile menu button */}
          <button
            className="mobile-menu-btn"
            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            aria-label="Toggle menu"
          >
            <span></span>
            <span></span>
            <span></span>
          </button>
        </div>
      </div>
    </header>
  )
}

export default Navbar
