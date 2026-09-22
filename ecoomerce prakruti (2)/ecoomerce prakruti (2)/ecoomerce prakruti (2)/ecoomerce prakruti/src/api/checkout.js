import { apiClient } from './client';

export const checkoutApi = {
  getShippingMethods: async () => {
    return await apiClient('/shipping-methods');
  },

  getTaxes: async () => {
    return await apiClient('/taxes');
  },

  processCheckout: async (payload) => {
    return await apiClient('/checkout', {
      method: 'POST',
      body: JSON.stringify(payload),
    });
  },
};
