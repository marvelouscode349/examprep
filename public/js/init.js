/* ============================================================
   init.js — App init, token validation on load
   ============================================================ */

document.addEventListener('DOMContentLoaded', async () => {
  // Inject styles
  const style = document.createElement('style');
  style.textContent = `
    @keyframes cfetti {
      0%   { transform: translate(0,0) scale(1); opacity: 1; }
      100% { transform: translate(var(--dx), var(--dy)) scale(0); opacity: 0; }
    }
    @keyframes spin { to { transform: rotate(360deg); } }
  `;
  document.head.appendChild(style);

  const savedUser = API.user();
  const token     = API.token();

  if (!savedUser || !token) {
    // No session — show landing
    go('s-land');
    return;
  }

  // Show dashboard immediately with loading state
  updateProfileUI(savedUser);
  go('s-dash');

  // Validate token with server
  try {
    const res = await fetch(`${API.BASE_URL}/auth/me`, {
      headers: API.headers(),
    });

    if (res.status === 401) {
      // Token expired or invalid
      API.clearSession();
      go('s-login');
      toast('Session expired. Please login again.', 'info');
      return;
    }

    const data = await res.json();

    if (data.success && data.user) {
      // Update stored user with fresh data from server
      API.saveSession(token, data.user);
      updateProfileUI(data.user);
    }

    // Load data
    await Promise.all([loadSubjects(), loadDashboard()]);

  } catch (err) {
    // Network error — still show dashboard with cached data
    // Don't log out on network error — they may just be offline
    await Promise.all([loadSubjects(), loadDashboard()]);
  }
});