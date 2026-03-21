/* ============================================================
   api.js — API config, headers, session management
   ============================================================ */
const API = {
  BASE_URL: 'http://127.0.0.1:8000/api',

  saveSession(token, user) {
    localStorage.setItem('ep_token', token);
    localStorage.setItem('ep_user', JSON.stringify(user));
  },

  token() {
    return localStorage.getItem('ep_token');
  },

  user() {
    const u = localStorage.getItem('ep_user');
    return u ? JSON.parse(u) : null;
  },

  clearSession() {
    localStorage.removeItem('ep_token');
    localStorage.removeItem('ep_user');
  },

  headers() {
    return {
      'Content-Type': 'application/json',
      'Accept':       'application/json',
      'Authorization': `Bearer ${this.token()}`
    };
  }
};
