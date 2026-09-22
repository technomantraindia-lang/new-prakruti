import { apiClient, setAuthToken, setStoredUser, getStoredUser } from './client';

export const authApi = {
  login: async (email, password, remember = true) => {
    const res = await apiClient('/login', {
      method: 'POST',
      body: JSON.stringify({ email, password }),
    });

    if (res.success && res.data?.access_token) {
      setAuthToken(res.data.access_token, remember);
      setStoredUser(res.data.user, remember);
    }

    return res;
  },

  register: async ({ name, email, password, phone }) => {
    const res = await apiClient('/register', {
      method: 'POST',
      body: JSON.stringify({
        name,
        email,
        password,
        password_confirmation: password,
        phone,
      }),
    });

    if (res.success && res.data?.access_token) {
      setAuthToken(res.data.access_token);
      setStoredUser(res.data.user);
    }

    return res;
  },

  logout: async () => {
    const res = await apiClient('/logout', { method: 'POST' });
    setAuthToken(null);
    setStoredUser(null);
    return res;
  },

  getMe: async () => {
    const res = await apiClient('/me');
    if (res.success && res.data) {
      setStoredUser(res.data);
    }
    return res;
  },

  updateProfile: async (profileData) => {
    const res = await apiClient('/me', {
      method: 'PUT',
      body: JSON.stringify(profileData),
    });
    if (res.success && res.data) {
      setStoredUser(res.data);
    }
    return res;
  },
};
