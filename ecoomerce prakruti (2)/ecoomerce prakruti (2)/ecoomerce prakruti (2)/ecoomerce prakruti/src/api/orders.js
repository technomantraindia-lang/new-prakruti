import { apiClient } from './client';

export const ordersApi = {
  getCustomerOrders: async () => {
    return await apiClient('/orders');
  },

  getOrderById: async (orderId) => {
    return await apiClient(`/orders/${orderId}`);
  },

  cancelOrder: async (orderId, reason = '') => {
    return await apiClient(`/orders/${orderId}/cancel`, {
      method: 'POST',
      body: JSON.stringify({ reason }),
    });
  },
};
