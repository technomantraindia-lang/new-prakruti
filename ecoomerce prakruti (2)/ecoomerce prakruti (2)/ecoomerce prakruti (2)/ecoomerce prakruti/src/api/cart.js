import { apiClient, setCartSessionId } from './client';

function persistCartSession(res) {
  if (res?.data?.session_id) {
    setCartSessionId(res.data.session_id);
  }
  return res;
}

export const cartApi = {
  getCart: async () => persistCartSession(await apiClient('/cart')),

  addToCart: async (productId, varId = null, qty = 1) => {
    return persistCartSession(await apiClient('/cart/items', {
      method: 'POST',
      body: JSON.stringify({
        product_id: productId,
        var_id: varId,
        qty,
      }),
    }));
  },

  updateCartItem: async (cartItemId, qty) => {
    return persistCartSession(await apiClient(`/cart/items/${cartItemId}`, {
      method: 'PUT',
      body: JSON.stringify({ qty }),
    }));
  },

  removeCartItem: async (cartItemId) => {
    return persistCartSession(await apiClient(`/cart/items/${cartItemId}`, {
      method: 'DELETE',
    }));
  },

  clearCart: async () => {
    return persistCartSession(await apiClient('/cart', {
      method: 'DELETE',
    }));
  },
};
