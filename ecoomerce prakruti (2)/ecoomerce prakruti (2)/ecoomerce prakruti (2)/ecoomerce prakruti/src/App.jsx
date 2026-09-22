import React, { useEffect, useState } from 'react'
import { motion, useReducedMotion } from 'framer-motion'
import './App.css'
import AnnouncementBar from './components/AnnouncementBar/AnnouncementBar'
import Navbar from './components/Navbar/Navbar'
import HeroBanner from './components/HeroBanner/HeroBanner'
import ShopByCategory from './components/ShopByCategory/ShopByCategory'
import BestSellers from './components/BestSellers/BestSellers'
import Benefits from './components/Benefits/Benefits'
import AboutSection from './components/AboutSection/AboutSection'
import PrakrutiPromise from './components/PrakrutiPromise/PrakrutiPromise'
import Testimonials from './components/Testimonials/Testimonials'
import PromoBanner from './components/PromoBanner/PromoBanner'
import InstagramFeed from './components/InstagramFeed/InstagramFeed'
import FaqSection from './components/FaqSection/FaqSection'
import Footer from './components/Footer/Footer'
import LoadingScreen from './components/LoadingScreen/LoadingScreen'
import CategoryPage from './components/CategoryPage/CategoryPage'
import AuthPage from './components/AuthPage/AuthPage'
import ProductDetailPage from './components/ProductDetailPage/ProductDetailPage'
import AboutPage from './components/AboutPage/AboutPage'
import ContactPage from './components/ContactPage/ContactPage'
import CartPage from './components/CartPage/CartPage'
import FarmGallery from './components/FarmGallery/FarmGallery'
import EducationPage from './components/EducationPage/EducationPage'
import FamilyPackPage from './components/FamilyPackPage/FamilyPackPage'
import AccountPage from './components/AccountPage/AccountPage'
import LegalPage from './components/LegalPage/LegalPage'

import { api, getStoredUser } from './services/api'
import { CartProvider, useCart } from './context/CartContext'

function AppShell() {
  const shouldReduceMotion = useReducedMotion()
  const [isLoading, setIsLoading] = useState(true)
  const [currentPage, setCurrentPage] = useState('home')
  const [selectedCategoryFilter, setSelectedCategoryFilter] = useState(null)
  const [selectedProductId, setSelectedProductId] = useState(null)
  const [globalSearchQuery, setGlobalSearchQuery] = useState(null)
  const [currentUser, setCurrentUser] = useState(getStoredUser())
  const { itemCount, notice, refreshCart } = useCart()

  useEffect(() => {
    const timer = window.setTimeout(() => setIsLoading(false), 800)

    api.getCurrentUser().then(user => {
      if (user) setCurrentUser(user);
      refreshCart();
    });

    return () => window.clearTimeout(timer)
  }, [refreshCart])

  const handleLogout = async () => {
    await api.logout();
    setCurrentUser(null);
    refreshCart();
    setCurrentPage('home');
  };

  useEffect(() => {
    const handleHashChange = () => {
      const hash = window.location.hash;
      if (hash.startsWith('#categories')) {
        setCurrentPage('categories');
      } else if (hash === '#login') {
        setCurrentPage('login');
      } else if (hash === '#register') {
        setCurrentPage('register');
      } else if (hash === '#about') {
        setCurrentPage('about');
      } else if (hash === '#contact') {
        setCurrentPage('contact');
      } else if (hash === '#cart') {
        setCurrentPage('cart');
      } else if (hash === '#account') {
        setCurrentPage('account');
      } else if (hash === '#farm-gallery') {
        setCurrentPage('farm-gallery');
      } else if (hash === '#education') {
        setCurrentPage('education');
      } else if (hash === '#family-pack') {
        setCurrentPage('family-pack');
      } else if (hash === '#privacy') {
        setCurrentPage('privacy');
      } else if (hash === '#terms') {
        setCurrentPage('terms');
      } else if (hash.startsWith('#product-')) {
        const id = hash.replace('#product-', '');
        setSelectedProductId(id);
        setCurrentPage('product-detail');
      } else if (hash === '#home' || hash === '') {
        setCurrentPage('home');
      }
    };
    window.addEventListener('hashchange', handleHashChange);
    handleHashChange();
    return () => window.removeEventListener('hashchange', handleHashChange);
  }, []);

  const handleNavigate = (page) => {
    setCurrentPage(page);
    window.location.hash = page === 'home' ? '' : page;
  };

  const handleViewProduct = (id) => {
    setSelectedProductId(id);
    setCurrentPage('product-detail');
    window.location.hash = `product-${id}`;
  };

  const openCategory = (catName) => {
    setSelectedCategoryFilter(catName);
    handleNavigate('categories');
  };

  const homeRevealVariants = {
    hidden: (direction) => {
      const offsets = {
        left: { x: -56, y: 0 },
        right: { x: 56, y: 0 },
        up: { x: 0, y: 44 },
        down: { x: 0, y: -36 },
        scale: { x: 0, y: 24, scale: 0.96 },
      }
      const offset = offsets[direction] || offsets.up
      return {
        opacity: 0,
        x: shouldReduceMotion ? 0 : offset.x,
        y: shouldReduceMotion ? 0 : offset.y,
        scale: shouldReduceMotion ? 1 : offset.scale || 1,
      }
    },
    visible: {
      opacity: 1,
      x: 0,
      y: 0,
      scale: 1,
      transition: {
        duration: 0.75,
        ease: [0.25, 1, 0.5, 1],
      },
    },
  }

  const revealHomeSection = (children, direction = 'up') => (
    <motion.div
      className="home-reveal-section"
      custom={direction}
      initial="hidden"
      whileInView="visible"
      viewport={{ once: true, amount: 0.18 }}
      variants={homeRevealVariants}
    >
      {children}
    </motion.div>
  )

  return (
    <div className="app">
      {isLoading && <LoadingScreen />}
      {notice && (
        <div className={`app-toast ${notice.type === 'error' ? 'error' : 'success'}`}>
          {notice.message}
        </div>
      )}
      <AnnouncementBar />
      <Navbar 
        currentPage={currentPage}
        setCurrentPage={handleNavigate}
        onAccountClick={() => handleNavigate(currentUser ? 'account' : 'login')}
        onCartClick={() => handleNavigate('cart')}
        cartCount={itemCount}
        currentUser={currentUser}
        onLogout={handleLogout}
        onSearch={(query) => {
          setGlobalSearchQuery(query);
          handleNavigate('categories');
        }}
      />
      <main>
        {currentPage === 'home' && (
          <>
            <HeroBanner />
            {revealHomeSection(<ShopByCategory onCategoryClick={openCategory} />, 'left')}
            {revealHomeSection(
              <BestSellers
                onViewProduct={handleViewProduct}
                onViewAll={() => {
                  setSelectedCategoryFilter(null);
                  handleNavigate('categories');
                }}
              />,
              'right'
            )}
            {revealHomeSection(<Benefits />, 'up')}
            {revealHomeSection(<AboutSection />, 'left')}
            {revealHomeSection(<PrakrutiPromise />, 'scale')}
            {revealHomeSection(<Testimonials />, 'right')}
            {revealHomeSection(<PromoBanner />, 'up')}
            {revealHomeSection(<InstagramFeed />, 'left')}
            {revealHomeSection(<FaqSection />, 'down')}
          </>
        )}

        {currentPage === 'categories' && (
          <CategoryPage 
            initialCategory={selectedCategoryFilter}
            clearInitialCategory={() => setSelectedCategoryFilter(null)}
            initialSearchQuery={globalSearchQuery}
            clearInitialSearchQuery={() => setGlobalSearchQuery(null)}
            onShopClick={() => {
              handleNavigate('home');
              setTimeout(() => {
                const el = document.getElementById('bestsellers') || document.querySelector('.best-sellers');
                if (el) el.scrollIntoView({ behavior: 'smooth' });
              }, 150);
            }}
            onViewProduct={handleViewProduct}
          />
        )}

        {currentPage === 'product-detail' && (
          <ProductDetailPage 
            productId={selectedProductId}
            onBack={() => handleNavigate('categories')}
            onNavigateProduct={handleViewProduct}
            onBuyNow={() => handleNavigate('cart')}
          />
        )}

        {currentPage === 'about' && (
          <AboutPage 
            onShopClick={() => handleNavigate('categories')}
          />
        )}

        {currentPage === 'contact' && (
          <ContactPage 
            onShopClick={() => handleNavigate('categories')}
          />
        )}

        {currentPage === 'cart' && (
          <CartPage 
            onShopClick={() => handleNavigate('categories')}
            currentUser={currentUser}
            onLoginRequired={() => handleNavigate('login')}
          />
        )}

        {currentPage === 'account' && (
          <AccountPage
            currentUser={currentUser}
            onLoginClick={() => handleNavigate('login')}
            onShopClick={() => handleNavigate('categories')}
            onFamilyPackClick={() => handleNavigate('family-pack')}
          />
        )}

        {currentPage === 'farm-gallery' && (
          <FarmGallery 
            onShopClick={() => handleNavigate('categories')}
          />
        )}

        {currentPage === 'education' && (
          <EducationPage 
            onShopClick={() => handleNavigate('categories')}
          />
        )}

        {currentPage === 'family-pack' && (
          <FamilyPackPage 
            onGoToCart={() => handleNavigate('cart')}
          />
        )}

        {currentPage === 'privacy' && (
          <LegalPage type="privacy" />
        )}

        {currentPage === 'terms' && (
          <LegalPage type="terms" />
        )}

        {(currentPage === 'login' || currentPage === 'register') && (
          <AuthPage 
            mode={currentPage}
            setMode={handleNavigate}
            onSuccess={(user) => {
              if (user) setCurrentUser(user);
              refreshCart();
              handleNavigate('home');
            }}
          />
        )}
      </main>
      <Footer 
        onHomeClick={() => handleNavigate('home')}
        onCategoriesClick={() => {
          setSelectedCategoryFilter(null);
          handleNavigate('categories');
        }}
        onPrivacyClick={() => handleNavigate('privacy')}
        onTermsClick={() => handleNavigate('terms')}
      />
    </div>
  )
}

function App() {
  return (
    <CartProvider>
      <AppShell />
    </CartProvider>
  )
}

export default App
