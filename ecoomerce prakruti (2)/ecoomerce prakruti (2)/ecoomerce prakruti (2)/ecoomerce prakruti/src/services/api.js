/**
 * Compatibility wrapper around modular src/api architecture
 */

import {
  authApi,
  productsApi,
  categoriesApi,
  cartApi,
  checkoutApi,
  ordersApi,
  returnsApi,
  accountApi,
  getAuthToken,
  setAuthToken,
  getStoredUser,
  setStoredUser,
} from '../api';
import { apiClient } from '../api/client';

export {
  authApi,
  productsApi,
  categoriesApi,
  cartApi,
  checkoutApi,
  ordersApi,
  returnsApi,
  accountApi,
  getAuthToken,
  setAuthToken,
  getStoredUser,
  setStoredUser,
};

function withUser(res) {
  return {
    ...res,
    user: res.data?.user || (res.success ? res.data : null),
    token: res.data?.access_token,
  };
}

export const api = {
  login: async (email, password, remember = true) => withUser(await authApi.login(email, password, remember)),
  register: async (data) => withUser(await authApi.register(data)),
  logout: () => authApi.logout(),
  getCurrentUser: async () => {
    if (!getAuthToken()) return getStoredUser();
    const res = await authApi.getMe();
    if (res.success) return res.data;
    if (res.status === 401) {
      setAuthToken(null);
      setStoredUser(null);
      return null;
    }
    return getStoredUser();
  },
  forgotPassword: async (email) => authApi.forgotPassword(email),
  resetPassword: async (payload) => authApi.resetPassword(payload),
  getProducts: async (params) => {
    const res = await productsApi.getProducts(params);
    return res.data || [];
  },
  getProductBySlugOrId: async (idOrSlug) => {
    const res = await productsApi.getProductBySlug(idOrSlug);
    return res.data;
  },
  getCategories: async () => {
    const res = await categoriesApi.getCategories();
    return res.data || [];
  },
  createOrder: async (payload) => {
    const res = await checkoutApi.processCheckout(payload);
    if (res.success) {
      return { success: true, order: res.data, message: res.message };
    }
    return {
      success: false,
      status: res.status,
      message: res.message || 'Could not place the order. Please try again.',
      errors: res.errors,
    };
  },
  sendInquiry: async (data) => {
    const res = await apiClient('/inquiries', {
      method: 'POST',
      body: JSON.stringify(data),
    });

    return {
      success: res.success,
      message: res.message || (res.success
        ? 'Thank you for reaching out! We will get back to you shortly.'
        : 'Could not send your message. Please try again.'),
    };
  },
};
