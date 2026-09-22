import { apiClient, getAuthToken } from './client';

const LOCAL_KEY = 'prakruti_family_pack';

export const getLocalFamilyPack = () => {
  try {
    const saved = localStorage.getItem(LOCAL_KEY);
    return saved ? JSON.parse(saved) : null;
  } catch (error) {
    return null;
  }
};

export const saveLocalFamilyPack = (pack) => {
  localStorage.setItem(LOCAL_KEY, JSON.stringify({
    ...pack,
    saved_at: new Date().toISOString(),
  }));
};

export const familyPacksApi = {
  getLatest: async () => {
    if (!getAuthToken()) {
      return { success: true, data: getLocalFamilyPack(), fromLocal: true };
    }

    const res = await apiClient('/family-packs/latest');
    if (res.success && res.data) saveLocalFamilyPack(res.data);
    return res;
  },

  save: async (payload) => {
    saveLocalFamilyPack(payload);

    if (!getAuthToken()) {
      return { success: true, data: payload, fromLocal: true };
    }

    return await apiClient('/family-packs', {
      method: 'POST',
      body: JSON.stringify(payload),
    });
  },
};
