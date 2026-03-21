/* ============================================================
   dashboard.js — Dashboard stats, recent sessions, AI tip
   ============================================================ */

async function loadDashboard() {
  try {
    const res  = await fetch(`${API.BASE_URL}/dashboard`, { headers: API.headers() });
    const data = await res.json();
    if (!data.success) return;

    // Greeting
    const hour      = new Date().getHours();
    const greeting  = hour < 12 ? 'Good morning' : hour < 17 ? 'Good afternoon' : 'Good evening';
    const user      = API.user();
    const firstName = user?.name?.split(' ')[0] || 'Student';
    const greetEl   = document.querySelector('#s-dash h2');
    if (greetEl) greetEl.textContent = `${greeting}, ${firstName}`;

    // Stat cards
    const statCards = document.querySelectorAll('#s-dash .stat-card');

    const accuracyEl = statCards[0]?.querySelector('.stat-val');
    if (accuracyEl) {
      accuracyEl.textContent = data.overall_accuracy !== null ? `${data.overall_accuracy}%` : '--';
      accuracyEl.className   = `stat-val ${
        data.overall_accuracy >= 70 ? 'text-g' :
        data.overall_accuracy >= 50 ? 'text-y' : 'text-r'
      }`;
    }

    const answeredEl = statCards[1]?.querySelector('.stat-val');
    if (answeredEl) answeredEl.textContent = data.total_answered.toLocaleString();

    const streakEl = statCards[2]?.querySelector('.stat-val');
    if (streakEl) streakEl.innerHTML = `<i class="bi bi-fire" style="font-size:1.2rem"></i> ${data.streak}`;

    const scoreEl = statCards[3]?.querySelector('.stat-val');
    if (scoreEl) {
      scoreEl.textContent = data.estimated_score !== null ? data.estimated_score : '--';
      scoreEl.className   = `stat-val ${
        data.estimated_score >= 250 ? 'text-g' :
        data.estimated_score >= 180 ? 'text-y' : 'text-r'
      }`;
    }
initAiPlanCard();
    // Profile stats (reuse same data — no extra API call)
    const profileQ = document.getElementById('profile-questions');
    const profileA = document.getElementById('profile-accuracy');
    const profileS = document.getElementById('profile-streak');
    if (profileQ) profileQ.textContent = data.total_answered.toLocaleString();
    if (profileA) profileA.textContent = data.overall_accuracy !== null ? `${data.overall_accuracy}%` : '--';
    if (profileS) profileS.innerHTML   = `<i class="bi bi-fire" style="font-size:1rem"></i> ${data.streak}`;

    // AI tip
    const tipEl = document.querySelector('.ai-bubble p');
    if (tipEl && data.ai_tip) tipEl.textContent = data.ai_tip;

    // Recent sessions
    const recentEl = document.getElementById('recent-sessions');
    if (recentEl) {
      if (!data.recent_sessions || data.recent_sessions.length === 0) {
        recentEl.innerHTML = '<div class="card-d2 mb-2 text-center py-3"><p style="color:var(--text2);font-size:.85rem;margin:0">No sessions yet. Start practicing!</p></div>';
      } else {
        recentEl.innerHTML = data.recent_sessions.map(s => `
          <div class="card-d2 mb-2 d-flex justify-content-between align-items-center"
               style="cursor:pointer" onclick="Quiz.sessionId=${s.id};reviewSession()">
            <div class="d-flex align-items-center gap-3">
              <div style="font-size:1.5rem">${s.subject_icon}</div>
              <div>
                <div style="font-weight:600;font-size:.88rem">${s.subject_name}</div>
                <div style="font-size:.72rem;color:var(--text2)">${s.mode} · ${s.completed_at}</div>
              </div>
            </div>
            <div style="text-align:right">
              <div class="fd" style="font-size:1.1rem;font-weight:700;color:${
                s.score_percentage >= 70 ? 'var(--green)' :
                s.score_percentage >= 50 ? 'var(--yellow)' : 'var(--red)'
              }">${s.score_percentage}%</div>
              <div style="font-size:.72rem;color:var(--text2)">${s.correct}/${s.total}</div>
            </div>
          </div>
        `).join('');
      }
    }

    // Weak areas button
    const weakBtn = document.getElementById('weak-areas-btn');
    if (weakBtn && data.weakest_subject_id) {
      weakBtn.style.display = 'block';
      weakBtn.onclick = () => loadWeakAreas(data.weakest_subject_id);
    }

  } catch (err) {
    // Dashboard fails silently
  }
}
