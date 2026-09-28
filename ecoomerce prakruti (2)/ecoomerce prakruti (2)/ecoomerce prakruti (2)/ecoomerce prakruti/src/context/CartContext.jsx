import React, { createContext, useCallback, useContext, useEffect, useMemo, useState } from 'react';
import { cartApi } from '../api/cart';
import { getAuthToken, setCartSessionId } from '../api/client';
import packgringImg from '../assets/images/new packing .png';

const CartContext = createContext(null);

function normalizeCart(data) {
  const items = (data?.items || []).map((item) => ({
    id: item.id,
    product_id: item.product_id,
    var_id: item.var_id,
    name: item.name || item.product_name || 'Product',
    subtitle: item.subtitle || '',
    desc: item.desc || item.package || item.weight || '',
    package: item.package || item.weight || '',
    price: Number(item.price || 0),
    quantity: Number(item.qty ?? item.quantity ?? 1),
    image: item.image || packgringImg,
    stock_qty: item.stock_qty,
    line_total: Number(item.line_total || 0),
  }));

  return {
    items,
    subtotal: Number(data?.subtotal || 0),
    discount: Number(data?.discount || 0),
    discountRule: data?.discount_rule || null,
    taxableSubtotal: Number(data?.taxable_subtotal || 0),
    gstAmt: Number(data?.gst_amt || 0),
    shipCharge: Number(data?.ship_charge || 0),
    shippingRule: data?.shipping_rule || null,
    freeShippingThreshold: Number(data?.free_shipping_min_amount || data?.shipping_rule?.free_shipping_min_amount || 0),
    freeShippingRemaining: Number(data?.free_shipping_remaining || data?.shipping_rule?.free_shipping_remaining || 0),
    freeShippingUnlocked: Boolean(data?.free_shipping_unlocked || data?.shipping_rule?.free_shipping_unlocked),
    total: Number(data?.total || 0),
  };
}

export function CartProvider({ children }) {
  const [items, setItems] = useState([]);
  const [subtotal, setSubtotal] = useState(0);
  const [discount, setDiscount] = useState(0);
  const [discountRule, setDiscountRule] = useState(null);
  const [taxableSubtotal, setTaxableSubtotal] = useState(0);
  const [gstAmt, setGstAmt] = useState(0);
  const [shipCharge, setShipCharge] = useState(0);
  const [shippingRule, setShippingRule] = useState(null);
  const [freeShippingThreshold, setFreeShippingThreshold] = useState(0);
  const [freeShippingRemaining, setFreeShippingRemaining] = useState(0);
  const [freeShippingUnlocked, setFreeShippingUnlocked] = useState(false);
  const [total, setTotal] = useState(0);
  const [loading, setLoading] = useState(true);
  const [notice, setNotice] = useState(null);

  const flash = useCallback((message, type = 'success') => {
    setNotice({ message, type, id: Date.now() });
  }, []);

  const apply = useCallback((data) => {
    if (!data) return;
    if (data.session_id) setCartSessionId(data.session_id);
    const next = normalizeCart(data);
    setItems(next.items);
    setSubtotal(next.subtotal);
    setDiscount(next.discount);
    setDiscountRule(next.discountRule);
    setTaxableSubtotal(next.taxableSubtotal);
    setGstAmt(next.gstAmt);
    setShipCharge(next.shipCharge);
    setShippingRule(next.shippingRule);
    setFreeShippingThreshold(next.freeShippingThreshold);
    setFreeShippingRemaining(next.freeShippingRemaining);
    setFreeShippingUnlocked(next.freeShippingUnlocked);
    setTotal(next.total);
  }, []);

  const refreshCart = useCallback(async () => {
    if (!getAuthToken()) {
      apply({ items: [], subtotal: 0 });
      setLoading(false);
      return { success: true, data: { items: [] } };
    }

    const res = await cartApi.getCart();
    if (res.success) apply(res.data);
    setLoading(false);
    return res;
  }, [apply]);

  useEffect(() => {
    refreshCart();
  }, [refreshCart]);

  useEffect(() => {
    if (!notice) return undefined;
    const timer = window.setTimeout(() => setNotice(null), 2800);
    return () => window.clearTimeout(timer);
  }, [notice]);

  const addToCart = useCallback(async (productId, qty = 1, varId = null) => {
    if (!getAuthToken()) {
      flash('Please login or register to add products to cart.', 'error');
      return { success: false, status: 401, requiresLogin: true };
    }

    if (!productId) {
      flash('This product is not available from the store yet.', 'error');
      return { success: false };
    }

    const res = await cartApi.addToCart(productId, varId, qty);
    if (res.success) {
      apply(res.data);
      flash('Added to cart');
    } else {
      flash(res.message || 'Could not add to cart', 'error');
    }
    return res;
  }, [apply, flash]);

  const updateQuantity = useCallback(async (cartItemId, qty) => {
    if (!getAuthToken()) {
      flash('Please login to update your cart.', 'error');
      return { success: false, status: 401, requiresLogin: true };
    }

    const nextQty = Math.max(1, qty);
    const res = await cartApi.updateCartItem(cartItemId, nextQty);
    if (res.success) {
      apply(res.data);
    } else {
      flash(res.message || 'Could not update quantity', 'error');
      refreshCart();
    }
    return res;
  }, [apply, flash, refreshCart]);

  const removeItem = useCallback(async (cartItemId) => {
    if (!getAuthToken()) {
      flash('Please login to update your cart.', 'error');
      return { success: false, status: 401, requiresLogin: true };
    }

    const res = await cartApi.removeCartItem(cartItemId);
    if (res.success) {
      apply(res.data);
    } else {
      flash(res.message || 'Could not remove item', 'error');
    }
    return res;
  }, [apply, flash]);

  const clearCart = useCallback(async () => {
    if (!getAuthToken()) {
      apply({ items: [], subtotal: 0 });
      return { success: true, data: { items: [] } };
    }

    const res = await cartApi.clearCart();
    if (res.success) {
      apply(res.data || { items: [], subtotal: 0 });
    }
    return res;
  }, [apply]);

  const value = useMemo(() => ({
    items,
    subtotal,
    discount,
    discountRule,
    taxableSubtotal,
    gstAmt,
    shipCharge,
    shippingRule,
    freeShippingThreshold,
    freeShippingRemaining,
    freeShippingUnlocked,
    total,
    loading,
    notice,
    flash,
    itemCount: items.reduce((sum, item) => sum + (item.quantity || 1), 0),
    refreshCart,
    addToCart,
    updateQuantity,
    removeItem,
    clearCart,
  }), [items, subtotal, discount, discountRule, taxableSubtotal, gstAmt, shipCharge, shippingRule, freeShippingThreshold, freeShippingRemaining, freeShippingUnlocked, total, loading, notice, flash, refreshCart, addToCart, updateQuantity, removeItem, clearCart]);

  return (
    <CartContext.Provider value={value}>
      {children}
    </CartContext.Provider>
  );
}

export function useCart() {
  const context = useContext(CartContext);
  if (!context) {
    throw new Error('useCart must be used within CartProvider');
  }
  return context;
}
