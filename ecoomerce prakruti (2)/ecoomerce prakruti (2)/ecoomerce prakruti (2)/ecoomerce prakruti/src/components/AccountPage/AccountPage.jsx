import React, { useEffect, useState } from 'react';
import './AccountPage.css';
import { accountApi, ordersApi } from '../../api';

const AccountPage = ({ currentUser, onLoginClick, onShopClick, onFamilyPackClick }) => {
  const [profile, setProfile] = useState(currentUser || null);
  const [orders, setOrders] = useState([]);
  const [loading, setLoading] = useState(true);
  const [message, setMessage] = useState('');

  useEffect(() => {
    let mounted = true;

    const loadAccount = async () => {
      setLoading(true);
      setMessage('');

      const [profileRes, ordersRes] = await Promise.all([
        accountApi.getProfile(),
        ordersApi.getCustomerOrders(),
      ]);

      if (!mounted) return;

      if (profileRes.success) {
        setProfile(profileRes.data);
      } else if (profileRes.status === 401) {
        setMessage('Please login to view your account details.');
      } else {
        setMessage(profileRes.message || 'Could not load account details.');
      }

      if (ordersRes.success) {
        setOrders(Array.isArray(ordersRes.data) ? ordersRes.data : []);
      }

      setLoading(false);
    };

    loadAccount();

    return () => {
      mounted = false;
    };
  }, []);

  if (!currentUser && !profile) {
    return (
      <div className="account-page">
        <div className="container account-container">
          <div className="account-login-card">
            <span className="account-card-icon">👤</span>
            <h1>Login Required</h1>
            <p>Please login to view your profile, orders, and family pack details.</p>
            <button type="button" className="btn btn-primary" onClick={onLoginClick}>
              Login to Continue
            </button>
          </div>
        </div>
      </div>
    );
  }

  const displayUser = profile || currentUser || {};
  const totalSpent = orders.reduce((sum, order) => sum + Number(order.total || 0), 0);
  const familyPackOrders = orders.filter((order) => order.order_type === 'family_pack').length;
  const memberSince = displayUser.created_at
    ? new Date(displayUser.created_at).toLocaleDateString('en-IN', { month: 'short', year: 'numeric' })
    : 'New customer';
  const initials = String(displayUser.name || 'Customer')
    .split(' ')
    .map((part) => part.charAt(0))
    .join('')
    .slice(0, 2)
    .toUpperCase();

  return (
    <div className="account-page">
      <div className="container account-container">
        <div className="account-breadcrumbs">Home &nbsp; &gt; &nbsp; <span>My Account</span></div>

        <section className="account-hero-card">
          <div className="account-avatar">{initials || 'CU'}</div>
          <div className="account-hero-copy">
            <p className="account-kicker">Customer Account</p>
            <h1>{displayUser.name || 'My Account'}</h1>
            <p>{displayUser.email || 'Manage your Prakruti profile and purchases.'}</p>
          </div>
          <div className="account-hero-badge">
            <span>{displayUser.status || 'Active'}</span>
            <small>Member since {memberSince}</small>
          </div>
        </section>

        {message && <div className="account-alert">{message}</div>}

        <section className="account-stats-grid">
          <div className="account-stat-card">
            <span>Total Orders</span>
            <strong>{orders.length}</strong>
          </div>
          <div className="account-stat-card">
            <span>Family Packs</span>
            <strong>{familyPackOrders}</strong>
          </div>
          <div className="account-stat-card">
            <span>Total Spend</span>
            <strong>₹{totalSpent.toFixed(0)}</strong>
          </div>
        </section>

        <div className="account-grid">
          <section className="account-card">
            <div className="account-card-heading">
              <span>Profile Details</span>
              {loading && <small>Loading...</small>}
            </div>

            <div className="account-detail-list">
              <div className="account-detail-row">
                <span>Name</span>
                <strong>{displayUser.name || '-'}</strong>
              </div>
              <div className="account-detail-row">
                <span>Email</span>
                <strong>{displayUser.email || '-'}</strong>
              </div>
              <div className="account-detail-row">
                <span>Mobile</span>
                <strong>{displayUser.phone || 'Not added'}</strong>
              </div>
              <div className="account-detail-row">
                <span>Status</span>
                <strong className="account-status">{displayUser.status || 'Active'}</strong>
              </div>
            </div>
          </section>

          <section className="account-card">
            <div className="account-card-heading">
              <span>Quick Actions</span>
            </div>

            <div className="account-actions">
              <button type="button" onClick={onShopClick}>
                <span>Shop Products</span>
                <small>Browse organic staples</small>
              </button>
              <button type="button" onClick={onFamilyPackClick}>
                <span>Open Family Pack</span>
                <small>Reorder your saved pack</small>
              </button>
            </div>
          </section>
        </div>

        <section className="account-card account-orders-card">
          <div className="account-card-heading">
            <span>Recent Orders</span>
            <small>{orders.length} order{orders.length === 1 ? '' : 's'}</small>
          </div>

          {orders.length > 0 ? (
            <div className="account-orders-list">
              {orders.slice(0, 5).map((order) => (
                <div className="account-order-row" key={order.id || order.order_num || order.order_number}>
                  <div>
                    <strong>{order.order_num || order.order_number || `Order #${order.id}`}</strong>
                    <span>{order.order_type === 'family_pack' ? 'Family Pack' : 'Standard Order'}</span>
                  </div>
                  <div>
                    <strong>₹{Number(order.total || 0).toFixed(2)}</strong>
                    <span>{order.status || 'Pending'}</span>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="account-empty-orders">
              <p>No orders found yet.</p>
              <button type="button" className="btn btn-primary" onClick={onShopClick}>
                Start Shopping
              </button>
            </div>
          )}
        </section>
      </div>
    </div>
  );
};

export default AccountPage;
