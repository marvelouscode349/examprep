/* ============================================================
   api.js — API config, session, global auth handling
   ============================================================ */
const API = {
  BASE_URL: 'https://myexamprep.online/api',

  saveSession(tokenOrUser, user) {
    // Support both saveSession(user) and saveSession(token, user)
    if (typeof tokenOrUser === 'string' && user) {
      // Old format: saveSession(token, user)
      localStorage.setItem('ep_token', tokenOrUser);
      localStorage.setItem('ep_user', JSON.stringify(user));
    } else if (typeof tokenOrUser === 'object') {
      // New format: saveSession(user)
      localStorage.setItem('ep_user', JSON.stringify(tokenOrUser));
    }
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
    const h = {
      'Content-Type': 'application/json',
      'Accept':       'application/json',
    };
    const token = this.token();
    if (token) h['Authorization'] = `Bearer ${token}`;
    return h;
  },

  // Global fetch wrapper — handles 401 automatically
  async fetch(url, options = {}) {
    const res = await fetch(url, {
      ...options,
      headers: { ...this.headers(), ...(options.headers || {}) },
    });

    if (res.status === 401) {
      this.clearSession();
      // Redirect to login
      document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
      const loginScreen = document.getElementById('s-login');
      if (loginScreen) loginScreen.classList.add('active');
      toast('Session expired. Please login again.', 'info');
      return null; // caller must check for null
    }

    return res;
  }
};

function handlePremiumBlock(res) {
    if (res.status === 403) {
        res.json().then(data => {
            if (data.code === "UPGRADE_REQUIRED") {
                document.getElementById("premium-msg").innerText = data.message;
                modal("m-premium");

                // ✅ QUIZ CLEANUP
                setTimeout(() => {
                    Quiz.reset();
                    go('s-dash');
                }, 10);
            }
        });

        return true;
    }

    return false;
}