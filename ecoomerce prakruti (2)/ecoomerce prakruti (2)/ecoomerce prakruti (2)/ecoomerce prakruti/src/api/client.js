/**
 * Centralized API HTTP Client for Prakruti E-Commerce Frontend
 * References VITE_API_BASE_URL (defaults to /api/v1)
 * Handles Auth Headers, guest cart session, JSON payload serialization, and HTTP error statuses.
 */

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api/v1';
const TOKEN_KEY = 'prakruti_customer_token';
const USER_KEY = 'prakruti_customer_user';
const SESSION_KEY = 'prakruti_cart_session';

export const getAuthToken = () => localStorage.getItem(TOKEN_KEY) || sessionStorage.getItem(TOKEN_KEY);

export const setAuthToken = (token, remember = true) => {
  localStorage.removeItem(TOKEN_KEY);
  sessionStorage.removeItem(TOKEN_KEY);

  if (token) {
    const storage = remember ? localStorage : sessionStorage;
    storage.setItem(TOKEN_KEY, token);
  }
};

export const getStoredUser = () => {
  try {
    const user = localStorage.getItem(USER_KEY) || sessionStorage.getItem(USER_KEY);
    return user ? JSON.parse(user) : null;
  } catch (e) {
    return null;
  }
};

export const setStoredUser = (user, remember = true) => {
  localStorage.removeItem(USER_KEY);
  sessionStorage.removeItem(USER_KEY);

  if (user) {
    const storage = remember ? localStorage : sessionStorage;
    storage.setItem(USER_KEY, JSON.stringify(user));
  }
};

export const getCartSessionId = () => {
  let sessionId = localStorage.getItem(SESSION_KEY);
  if (!sessionId) {
    sessionId = (typeof crypto !== 'undefined' && crypto.randomUUID)
      ? crypto.randomUUID()
      : `sess_${Date.now()}_${Math.random().toString(16).slice(2)}`;
    localStorage.setItem(SESSION_KEY, sessionId);
  }
  return sessionId;
};

export const setCartSessionId = (sessionId) => {
  if (sessionId) {
    localStorage.setItem(SESSION_KEY, sessionId);
  }
};

export async function apiClient(endpoint, options = {}) {
  const token = getAuthToken();
  const headers = {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Session-ID': getCartSessionId(),
    ...(token ? { 'Authorization': `Bearer ${token}` } : {}),
    ...options.headers,
  };

  const config = {
    ...options,
    headers,
  };

  try {
    const url = endpoint.startsWith('http') ? endpoint : `${API_BASE_URL}${endpoint}`;
    const response = await fetch(url, config);
    const data = await response.json().catch(() => ({}));

    if (response.status === 401 && !String(endpoint).includes('/login')) {
      setAuthToken(null);
      setStoredUser(null);
    }

    if (!response.ok) {
      return {
        success: false,
        status: response.status,
        message: data.message || `Request failed with status ${response.status}`,
        errors: data.errors || null,
        data: data.data || null,
      };
    }

    if (data?.data?.session_id) {
      setCartSessionId(data.data.session_id);
    }

    return {
      success: true,
      status: response.status,
      message: data.message || 'Success',
      data: data.data !== undefined ? data.data : data,
      pagination: data.pagination || null,
      settings: data.settings || null,
    };
  } catch (error) {
    console.warn(`[API Client Network Error] ${endpoint}:`, error.message);
    return {
      success: false,
      status: 0,
      networkError: true,
      message: error.message || 'Network connection unavailable',
    };
  }
}
