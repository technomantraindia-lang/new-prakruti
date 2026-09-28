import React, { useEffect, useState } from 'react';
import './AuthPage.css';
import { motion } from 'framer-motion';
import { api } from '../../services/api';

// Import background images/assets
import authBgImg from '../../assets/images/first banner.png';

const REMEMBERED_EMAIL_KEY = 'prakruti_remembered_customer_email';

const AuthPage = ({ mode, setMode, onSuccess, resetToken = '', resetEmail = '' }) => {
  const [showPassword, setShowPassword] = useState(false);
  const [showPasswordConfirmation, setShowPasswordConfirmation] = useState(false);
  const [email, setEmail] = useState(() => resetEmail || localStorage.getItem(REMEMBERED_EMAIL_KEY) || '');
  const [password, setPassword] = useState('');
  const [passwordConfirmation, setPasswordConfirmation] = useState('');
  const [fullName, setFullName] = useState('');
  const [phone, setPhone] = useState('');
  const [termsAccepted, setTermsAccepted] = useState(false);
  const [rememberMe, setRememberMe] = useState(true);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMsg, setErrorMsg] = useState('');
  const [successMsg, setSuccessMsg] = useState('');

  const isRegister = mode === 'register';
  const isForgot = mode === 'forgot-password';
  const isReset = mode === 'reset-password';

  useEffect(() => {
    if (resetEmail) {
      setEmail(resetEmail);
    }
  }, [resetEmail]);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setIsSubmitting(true);
    setErrorMsg('');
    setSuccessMsg('');

    try {
      let res;
      if (isForgot) {
        res = await api.forgotPassword(email);
      } else if (isReset) {
        res = await api.resetPassword({
          email,
          token: resetToken,
          password,
          password_confirmation: passwordConfirmation,
        });
      } else if (isRegister) {
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
        if (!isRegister && !isForgot && !isReset) {
          if (rememberMe) {
            localStorage.setItem(REMEMBERED_EMAIL_KEY, email);
          } else {
            localStorage.removeItem(REMEMBERED_EMAIL_KEY);
          }
        }

        setSuccessMsg(res.message || (isRegister ? 'Account created successfully! Welcome to Prakruti.' : isForgot ? 'Password reset link sent if this email exists.' : isReset ? 'Password reset successfully.' : 'Logged in successfully!'));
        setTimeout(() => {
          setSuccessMsg('');
          if (isForgot) {
            setMode('login');
          } else if (isReset) {
            setMode('login');
          } else if (onSuccess) {
            onSuccess(res.user || res.data?.user);
          }
        }, isForgot || isReset ? 1800 : 1200);
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
              Crafted Traditionally. Tested Scientifically. Join our community for a healthier, chemical-free lifestyle.
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
                {isForgot ? 'Forgot Password' : isReset ? 'Reset Password' : isRegister ? 'Create Account' : 'Welcome Back'}
              </h1>
              <p className="form-sub-title">
                {isForgot
                  ? 'Enter your customer email and we will send a reset link.'
                  : isReset
                    ? 'Create a new password for your customer account.'
                    : isRegister ? 'Join us and start eating clean today' : 'Sign in to access your wellness dashboard'}
              </p>
              {!isRegister && !isForgot && !isReset && (
                <p className="form-sub-title" style={{ marginTop: 8, fontSize: '0.82rem' }}>
                  Demo customer: customer@example.com / password
                </p>
              )}
              
              {!isForgot && !isReset && (
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
              )}
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

              {!isForgot && (
                <div className="input-group">
                <div className="label-row">
                  <label htmlFor="password">Password</label>
                  {!isRegister && !isReset && (
                    <button type="button" className="forgot-link" onClick={() => setMode('forgot-password')}>
                      Forgot Password?
                    </button>
                  )}
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
              )}

              {isReset && (
                <div className="input-group">
                  <label htmlFor="password_confirmation">Confirm Password</label>
                  <div className="input-wrapper">
                    <span className="input-icon">🔒</span>
                    <input
                      type={showPasswordConfirmation ? "text" : "password"}
                      id="password_confirmation"
                      placeholder="Confirm new password"
                      value={passwordConfirmation}
                      onChange={(e) => setPasswordConfirmation(e.target.value)}
                      required
                    />
                    <button
                      type="button"
                      className="password-toggle"
                      onClick={() => setShowPasswordConfirmation(!showPasswordConfirmation)}
                    >
                      {showPasswordConfirmation ? 'Hide' : 'Show'}
                    </button>
                  </div>
                </div>
              )}

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
              ) : !isForgot && !isReset ? (
                <label className="checkbox-container auth-terms">
                  <input
                    type="checkbox"
                    checked={rememberMe}
                    onChange={() => setRememberMe((prev) => !prev)}
                  />
                  <span className="checkbox-checkmark"></span>
                  Keep me logged in for 30 days
                </label>
              ) : null}

              {/* Submit Button */}
              <button 
                type="submit" 
                className="btn auth-btn-primary" 
                disabled={isSubmitting}
              >
                {isSubmitting ? (
                  <span className="loader-dots">Processing...</span>
                ) : (
                  isForgot ? 'Send Reset Link' : isReset ? 'Reset Password' : isRegister ? 'Create Account Securely' : 'Login Securely'
                )}
              </button>

              {/* Toggle switch text */}
              <p className="auth-switch-prompt">
                {isForgot || isReset ? 'Remembered your password?' : isRegister ? 'Already have an account?' : 'New to Prakruti?'}
                <button 
                  type="button" 
                  className="switch-btn"
                  onClick={() => setMode(isRegister || isForgot || isReset ? 'login' : 'register')}
                >
                  {isRegister || isForgot || isReset ? 'Login here' : 'Register here'}
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
