import React from 'react'
import { motion } from 'framer-motion'
import './AuthModal.css'

const AuthModal = ({ mode, onModeChange, onClose }) => {
  const isRegister = mode === 'register'

  return (
    <div className="auth-overlay" role="dialog" aria-modal="true" aria-label={isRegister ? 'Create account' : 'Login'}>
      <motion.div
        className="auth-panel"
        initial={{ opacity: 0, y: 26, scale: 0.96 }}
        animate={{ opacity: 1, y: 0, scale: 1 }}
        transition={{ duration: 0.35, ease: [0.25, 1, 0.5, 1] }}
      >
        <button className="auth-close" type="button" onClick={onClose} aria-label="Close account form">
          ×
        </button>

        <div className="auth-visual">
          <span className="auth-badge">Prakruti Customer</span>
          <h2>{isRegister ? 'Create your wellness account' : 'Welcome back to pure goodness'}</h2>
          <p>
            Track orders, save addresses, manage favourites and receive thoughtful offers on natural staples.
          </p>
        </div>

        <form className="auth-form" onSubmit={(event) => event.preventDefault()}>
          <div className="auth-tabs">
            <button type="button" className={!isRegister ? 'active' : ''} onClick={() => onModeChange('login')}>
              Login
            </button>
            <button type="button" className={isRegister ? 'active' : ''} onClick={() => onModeChange('register')}>
              Register
            </button>
          </div>

          {isRegister && (
            <label>
              Full Name
              <input type="text" placeholder="Enter your name" required />
            </label>
          )}

          <label>
            Email Address
            <input type="email" placeholder="you@example.com" required />
          </label>

          <label>
            Password
            <input type="password" placeholder="Enter password" required />
          </label>

          {isRegister && (
            <label>
              Mobile Number
              <input type="tel" placeholder="Mobile number" required />
            </label>
          )}

          <button className="auth-submit" type="submit">
            {isRegister ? 'Create Account' : 'Login Securely'}
          </button>

          <button className="auth-switch" type="button" onClick={() => onModeChange(isRegister ? 'login' : 'register')}>
            {isRegister ? 'Already have an account? Login' : 'New customer? Create account'}
          </button>
        </form>
      </motion.div>
    </div>
  )
}

export default AuthModal
