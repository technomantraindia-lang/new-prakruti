import { apiClient } from './client';

export const accountApi = {
  getProfile: async () => {
    return await apiClient('/me');
  },

  updateProfile: async (profileData) => {
    return await apiClient('/me', {
      method: 'PUT',
      body: JSON.stringify(profileData),
    });
  },

  getAddresses: async () => {
    return await apiClient('/addresses');
  },

  addAddress: async (addressData) => {
    return await apiClient('/addresses', {
      method: 'POST',
      body: JSON.stringify(addressData),
    });
  },
};
