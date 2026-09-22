import { apiClient } from './client';
import { productsData as fallbackProducts } from '../data/products';
import { normalizeProduct, normalizeProductList } from './mappers';

export const productsApi = {
  getProducts: async (params = {}) => {
    const query = new URLSearchParams();
    if (params.search) query.append('search', params.search);
    if (params.category) query.append('category', params.category);
    if (params.brand) query.append('brand', params.brand);
    if (params.featured) query.append('featured', 'true');
    if (params.min_price) query.append('min_price', params.min_price);
    if (params.max_price) query.append('max_price', params.max_price);
    if (params.sort) query.append('sort', params.sort);
    query.append('per_page', params.per_page || 100);

    const queryString = query.toString();
    const res = await apiClient(`/products${queryString ? `?${queryString}` : ''}`);

    if (res.success && Array.isArray(res.data)) {
      return {
        ...res,
        data: normalizeProductList(res.data),
      };
    }

    let items = [...fallbackProducts];
    if (params.category && params.category !== 'All') {
      items = items.filter((p) => p.category.toLowerCase() === params.category.toLowerCase());
    }
    if (params.search) {
      const s = params.search.toLowerCase();
      items = items.filter((p) => p.name.toLowerCase().includes(s) || p.category.toLowerCase().includes(s));
    }
    if (params.featured) {
      items = items.slice(0, 6);
    }
    return { success: true, fromFallback: true, data: items };
  },

  getProductBySlug: async (slugOrId) => {
    const res = await apiClient(`/products/${slugOrId}`);
    if (res.success && res.data) {
      return { ...res, data: normalizeProduct(res.data) };
    }

    const found = fallbackProducts.find((p) => p.id === parseInt(slugOrId, 10) || p.slug === slugOrId);
    return { success: Boolean(found), fromFallback: true, data: found || null };
  },

  getVariations: async (productId) => {
    return await apiClient(`/products/${productId}/variations`);
  },
};
