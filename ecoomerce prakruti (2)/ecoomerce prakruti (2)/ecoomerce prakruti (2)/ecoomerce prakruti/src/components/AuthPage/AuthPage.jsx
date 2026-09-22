import React, { useState } from 'react';
import './AuthPage.css';
import { motion } from 'framer-motion';
import { api } from '../../services/api';

// Import background images/assets
import authBgImg from '../../assets/images/first banner.png';

const AuthPage = ({ mode, setMode, onSuccess }) => {
  const [showPassword, setShowPassword] = useState(false);
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [fullName, setFullName] = useState('');
  const [phone, setPhone] = useState('');
  const [termsAccepted, setTermsAccepted] = useState(false);
  const [rememberMe, setRememberMe] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMsg, setErrorMsg] = useState('');
  const [successMsg, setSuccessMsg] = useState('');

  const isRegister = mode === 'register';

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setErrorMsg('');
    setSuccessMsg('');

    try {
      let res;
      if (isRegister) {
        res = await api.register({
          name: fullName,
          email,
          password,
          phone,
        });
      } else {
        res = await api.login(email, password, rememberMe);
      }

      setIsSubmitting(false);

      if (res.success) {
        setSuccessMsg(res.message || (isRegister ? 'Account created successfully! Welcome to Prakruti.' : 'Logged in successfully!'));
        setTimeout(() => {
          setSuccessMsg('');
          if (onSuccess) onSuccess(res.user || res.data?.user);
        }, 1200);
      } else {
        setErrorMsg(res.message || 'Authentication failed. Please check your details.');
      }
    } catch (err) {
      setIsSubmitting(false);
      setErrorMsg('An unexpected error occurred. Please try again.');
    }
  };

  return (
    <div className="auth-page-wrapper">
      <div className="auth-page-container">
        
        {/* Left Side: Editorial botanical panel */}
        <div 
          className="auth-editorial-panel" 
          style={{ backgroundImage: `linear-gradient(to bottom, rgba(19, 44, 21, 0.93), rgba(36, 91, 42, 0.8)), url(${authBgImg})` }}
        >
          <div className="editorial-inner">
            <span className="editorial-badge">Pure & Trusted</span>
            <h2 className="editorial-title">Prakruti</h2>
            <p className="editorial-text">
              Rooted in Nature. Backed by Science. Join our community for a healthier, chemical-free lifestyle.
            </p>

            <div className="editorial-bullets">
              <div className="bullet-point">
                <span className="bullet-icon">📦</span>
                <div className="bullet-info">
                  <h4>Seamless Ordering</h4>
                  <p>Track your orders, reorder items with one click, and manage addresses.</p>
                </div>
              </div>
              <div className="bullet-point">
                <span className="bullet-icon">🎁</span>
                <div className="bullet-info">
                  <h4>Exclusive Rewards</h4>
                  <p>Earn points on every purchase and get early access to new seasonal harvests.</p>
                </div>
              </div>
            </div>
            
            <div className="editorial-footer">
              <p>Need support? <a href="#contact">Contact Support</a></p>
            </div>
          </div>
        </div>

        {/* Right Side: Form panel */}
        <div className="auth-form-panel">
          <div className="form-inner">
            
            {/* Header/Switch tabs */}
            <div className="form-header-box">
              <h1 className="form-main-title">
                {isRegister ? 'Create Account' : 'Welcome Back'}
              </h1>
              <p className="form-sub-title">
                {isRegister ? 'Join us and start eating clean today' : 'Sign in to access your wellness dashboard'}
              </p>
              {!isRegister && (
                <p className="form-sub-title" style={{ marginTop: 8, fontSize: '0.82rem' }}>
                  Demo customer: customer@example.com / password
                </p>
              )}
              
              <div className="form-tabs-switch">
                <button 
                  className={`tab-btn ${!isRegister ? 'active' : ''}`}
                  onClick={() => setMode('login')}
                  type="button"
                >
                  Login
                </button>
                <button 
                  className={`tab-btn ${isRegister ? 'active' : ''}`}
                  onClick={() => setMode('register')}
                  type="button"
                >
                  Register
                </button>
              </div>
            </div>

            {/* Social logins */}
            <div className="social-logins-box">
              <button className="social-btn google-btn" type="button">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M12.24 10.285V13.4h6.887c-.275 1.565-1.88 4.604-6.887 4.604-4.33 0-7.859-3.578-7.859-8s3.53-8 7.859-8c2.46 0 4.105 1.025 5.047 1.926l2.427-2.334C17.955 2.192 15.34 1 12.24 1 5.922 1 1 5.922 1 12.24s4.922 11.24 11.24 11.24c6.594 0 11.01-4.636 11.01-11.24 0-.756-.08-1.333-.178-1.955H12.24z"/>
                </svg>
                Sign in with Google
              </button>
              <button className="social-btn apple-btn" type="button">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                  <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 4.17c.66-.81 1.11-1.93.99-3.06-1 .04-2.2.67-2.92 1.49-.62.71-1.16 1.85-1.01 2.96 1.1.09 2.23-.55 2.94-1.39z"/>
                </svg>
                Apple
              </button>
            </div>

            <div className="form-divider">
              <span>Or sign in with email</span>
            </div>

            {/* Success / Error Prompts */}
            {errorMsg && (
              <div className="auth-success-toast" style={{ backgroundColor: '#fff0f0', borderColor: '#ffcdd2', color: '#c62828' }}>
                <span className="toast-icon">⚠️</span>
                <span className="toast-msg">{errorMsg}</span>
              </div>
            )}

            {successMsg && (
              <div className="auth-success-toast">
                <span className="toast-icon">✨</span>
                <span className="toast-msg">{successMsg}</span>
              </div>
            )}

            {/* Auth Form */}
            <form onSubmit={handleSubmit} className="auth-form-body">
              
              {isRegister && (
                <div className="input-group">
                  <label htmlFor="fullName">Full Name</label>
                  <div className="input-wrapper">
                    <span className="input-icon">👤</span>
                    <input 
                      type="text" 
                      id="fullName" 
                      placeholder="Arjun Sharma" 
                      value={fullName}
                      onChange={(e) => setFullName(e.target.value)}
                      required 
                    />
                  </div>
                </div>
              )}

              <div className="input-group">
                <label htmlFor="email">Email Address</label>
                <div className="input-wrapper">
                  <span className="input-icon">✉️</span>
                  <input 
                    type="email" 
                    id="email" 
                    placeholder="you@example.com" 
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    required 
                  />
                </div>
              </div>

              {isRegister && (
                <div className="input-group">
                  <label htmlFor="phone">Mobile Number</label>
                  <div className="input-wrapper">
                    <span className="input-icon">📞</span>
                    <input 
                      type="tel" 
                      id="phone" 
                      placeholder="Mobile number" 
                      value={phone}
                      onChange={(e) => setPhone(e.target.value)}
                      required 
                    />
                  </div>
                </div>
              )}

              <div className="input-group">
                <div className="label-row">
                  <label htmlFor="password">Password</label>
                  {!isRegister && <a href="#forgot" className="forgot-link">Forgot Password?</a>}
                </div>
                <div className="input-wrapper">
                  <span className="input-icon">🔒</span>
                  <input 
                    type={showPassword ? "text" : "password"} 
                    id="password" 
                    placeholder="••••••••" 
                    value={password}
                    onChange={(e) => setPassword(e.target.value)}
                    required 
                  />
                  <button 
                    type="button" 
                    className="password-toggle"
                    onClick={() => setShowPassword(!showPassword)}
                  >
                    {showPassword ? '👁️' : '👁️‍🗨️'}
                  </button>
                </div>
              </div>

              {/* Extra terms or checkbox */}
              {isRegister ? (
                <label className="checkbox-container auth-terms">
                  <input 
                    type="checkbox" 
                    checked={termsAccepted} 
                    onChange={() => setTermsAccepted(!termsAccepted)}
                    required
                  />
                  <span className="checkbox-checkmark"></span>
                  I agree to the <a href="#terms">Terms of Service</a> & <a href="#privacy">Privacy Policy</a>
                </label>
              ) : (
                <label className="checkbox-container auth-terms">
                  <input
                    type="checkbox"
                    checked={rememberMe}
                    onChange={() => setRememberMe((prev) => !prev)}
                  />
                  <span className="checkbox-checkmark"></span>
                  Keep me logged in for 30 days
                </label>
              )}

              {/* Submit Button */}
              <button 
                type="submit" 
                className="btn auth-btn-primary" 
                disabled={isSubmitting}
              >
                {isSubmitting ? (
                  <span className="loader-dots">Processing...</span>
                ) : (
                  isRegister ? 'Create Account Securely' : 'Login Securely'
                )}
              </button>

              {/* Toggle switch text */}
              <p className="auth-switch-prompt">
                {isRegister ? 'Already have an account?' : 'New to Prakruti?'}
                <button 
                  type="button" 
                  className="switch-btn"
                  onClick={() => setMode(isRegister ? 'login' : 'register')}
                >
                  {isRegister ? 'Login here' : 'Register here'}
                </button>
              </p>

            </form>
          </div>
        </div>

      </div>
    </div>
  );
};

export default AuthPage;
