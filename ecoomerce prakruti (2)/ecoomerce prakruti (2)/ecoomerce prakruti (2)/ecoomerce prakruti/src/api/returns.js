import { apiClient } from './client';

export const returnsApi = {
  getCustomerReturns: async () => {
    return await apiClient('/returns');
  },

  requestReturn: async (payload) => {
    return await apiClient('/returns', {
      method: 'POST',
      body: JSON.stringify(payload),
    });
  },

  getReturnById: async (returnId) => {
    return await apiClient(`/returns/${returnId}`);
  },
};
