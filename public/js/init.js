/* ============================================================
   init.js — App initialisation on page load
   ============================================================ */

document.addEventListener('DOMContentLoaded', async () => {
  // Inject confetti keyframe
  const style = document.createElement('style');
  style.textContent = `
    @keyframes cfetti {
      0%   { transform: translate(0,0) scale(1); opacity: 1; }
      100% { transform: translate(var(--dx), var(--dy)) scale(0); opacity: 0; }
    }
    .notes-header {
      font-weight: 700;
      font-size: .92rem;
      color: var(--blue2);
      margin: 16px 0 8px;
    }
    .notes-bullet {
      padding: 4px 0 4px 8px;
      border-left: 2px solid var(--blue-dim);
      margin-bottom: 6px;
      color: var(--text1);
      font-size: .86rem;
    }
    .ai-spinner {
      width: 28px;
      height: 28px;
      border: 3px solid var(--blue-dim);
      border-top-color: var(--blue);
      border-radius: 50%;
      animation: spin 0.7s linear infinite;
      margin: 0 auto;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .opt-selected {
      border-color: var(--blue) !important;
      background: var(--blue-dim) !important;
      transform: scale(0.98);
    }
  `;
  document.head.appendChild(style);

  // Returning user — skip landing, restore session
  const savedUser = API.user();
  if (savedUser && API.token()) {
    updateProfileUI(savedUser);
    await loadSubjects();
    await loadDashboard();
    go('s-dash');
  }
});
