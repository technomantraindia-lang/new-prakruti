import { apiClient } from './client';

export const reviewsApi = {
  getTestimonials: async () => {
    return await apiClient('/testimonials');
  },

  addTestimonial: async ({ name, location, rating, review }) => {
    return await apiClient('/testimonials', {
      method: 'POST',
      body: JSON.stringify({ name, location, rating, review }),
    });
  },

  getProductReviews: async (productIdOrSlug) => {
    return await apiClient(`/products/${productIdOrSlug}/reviews`);
  },

  addProductReview: async (productIdOrSlug, { name, location, rating, comment }) => {
    return await apiClient(`/products/${productIdOrSlug}/reviews`, {
      method: 'POST',
      body: JSON.stringify({ name, location, rating, comment }),
    });
  },
};
