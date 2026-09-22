import { apiClient } from './client';
import { categoriesData as fallbackCategories } from '../data/products';
import { normalizeCategory, normalizeCategoryList } from './mappers';

export const categoriesApi = {
  getCategories: async () => {
    const res = await apiClient('/categories');
    if (res.success && Array.isArray(res.data) && res.data.length > 0) {
      return { ...res, data: normalizeCategoryList(res.data) };
    }
    return { success: true, fromFallback: true, data: fallbackCategories };
  },

  getCategoryBySlug: async (slug) => {
    const res = await apiClient(`/categories/${slug}`);
    if (res.success && res.data) {
      return { ...res, data: normalizeCategory(res.data) };
    }
    return res;
  },

  getBrands: async () => {
    return await apiClient('/brands');
  },

  getAttributes: async () => {
    return await apiClient('/attributes');
  },
};
