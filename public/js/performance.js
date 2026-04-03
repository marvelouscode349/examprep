/* ============================================================
   performance.js — Performance screen, charts, streak grid, topic breakdown
   ============================================================ */

async function loadPerformance() {
  try {
    const res  = await API.fetch(`${API.BASE_URL}/performance`);
    if (!res) return;

    const data = await res.json();
    if (!data.success) return;

    // Overall accuracy
    const overallEl = document.getElementById('perf-overall-accuracy');
    if (overallEl) {
      overallEl.textContent = data.overall_accuracy !== null ? `${data.overall_accuracy}%` : '--';
      overallEl.style.color = data.overall_accuracy >= 70 ? 'var(--green)' :
                              data.overall_accuracy >= 50 ? 'var(--yellow)' : 'var(--red)';
    }

    // Summary text
    const subStats = document.getElementById('perf-summary-text');
    if (subStats) subStats.textContent = data.total_answered > 0
      ? `${data.total_answered.toLocaleString()} questions answered across ${data.total_sessions} sessions`
      : 'Start practicing to see your stats';

    // Estimated score
    const estEl = document.getElementById('perf-est-score');
    if (estEl) estEl.textContent = data.estimated_score !== null ? `${data.estimated_score}` : '--';

    // Total sessions
    const totalSessionsEl = document.getElementById('perf-total-sessions');
    if (totalSessionsEl) totalSessionsEl.textContent = data.total_sessions ?? '--';

    // Score trend chart
    buildScoreChart(data.score_trend);

    // Subject breakdown — expandable with topic drill-down
    const subjEl = document.getElementById('perf-subjects');
    if (subjEl) {
      if (!data.subject_breakdown || data.subject_breakdown.length === 0) {
        subjEl.innerHTML = '<p style="color:var(--text2);font-size:.85rem;text-align:center;padding:1rem 0;margin:0">No data yet — start practicing!</p>';
      } else {
        subjEl.innerHTML = data.subject_breakdown.map((s, i) => {
          // Resolve subject id safely (keep as string; don't cast with Number())
          const sidRaw = s.id ?? s.subject_id ?? s.subjectId;
          const sid    = sidRaw != null ? String(sidRaw).trim() : '';

          if (!sid) {
            // Diagnostic to help if payload is missing id
            console.warn('Subject item missing id:', s);
          }

          const sname = s.subject_name ?? s.subjectName ?? s.name ?? '';

          return `
            <div class="perf-subject" style="margin-bottom:16px">
              <div class="perf-row" style="cursor:pointer" data-subject-id="${sid}">
                <div class="d-flex justify-content-between align-items-center mb-1">
                  <div class="d-flex align-items-center gap-2">
                    <span style="font-size:1.1rem">${s.subject_icon ?? ''}</span>
                    <span style="font-size:.88rem;font-weight:600">${sname}</span>
                    ${s.status === 'weak'
                      ? '<span style="background:var(--red-dim);color:var(--red);font-size:.65rem;border-radius:20px;padding:2px 7px;border:1px solid var(--red)">Weak</span>'
                      : ''}
                    ${s.status === 'strong'
                      ? '<span style="background:var(--green-dim);color:var(--green);font-size:.65rem;border-radius:20px;padding:2px 7px;border:1px solid var(--green)">Strong</span>'
                      : ''}
                  </div>
                  <div class="d-flex align-items-center gap-2">
                    <span style="font-size:.85rem;font-weight:700;color:${
                      s.accuracy >= 70 ? 'var(--green)' :
                      s.accuracy >= 50 ? 'var(--yellow)' : 'var(--red)'
                    }">${s.accuracy}%</span>
                    <i class="bi bi-chevron-down" style="color:var(--text2);font-size:.75rem;transition:transform .2s"></i>
                  </div>
                </div>
                <div style="background:var(--card2);border-radius:20px;height:6px;overflow:hidden">
                  <div style="height:100%;width:${s.accuracy}%;background:${
                    s.accuracy >= 70 ? 'var(--green)' :
                    s.accuracy >= 50 ? 'var(--yellow)' : 'var(--red)'
                  };border-radius:20px;transition:width .6s ease"></div>
                </div>
                <div style="font-size:.72rem;color:var(--text2);margin-top:3px">
                  ${s.total_answered} questions · Last practiced ${s.last_practiced}
                </div>
              </div>

              <!-- Topic breakdown for this subject -->
              <div id="topic-breakdown-${sid}"
                   style="display:none;margin-top:10px;padding-left:12px;border-left:2px solid var(--border)">
                <div class="text-center py-2">
                  <div class="ai-spinner" style="width:16px;height:16px;margin:0 auto"></div>
                </div>
              </div>

              ${i < data.subject_breakdown.length - 1
                ? '<div style="height:1px;background:var(--border);margin-top:14px"></div>'
                : ''}
            </div>
          `;
        }).join('');

        // Attach one delegated click handler (only once)
        if (!subjEl.dataset.handlerAttached) {
          subjEl.addEventListener('click', (e) => {
            const rowEl = e.target.closest('.perf-row');
            if (!rowEl) return;

            const sid = rowEl.getAttribute('data-subject-id'); // keep as string
            if (!sid) {
              console.warn('Clicked row missing data-subject-id', rowEl);
              return;
            }

            toggleTopicBreakdown(sid, rowEl);
          }, { passive: true });
          subjEl.dataset.handlerAttached = '1';
        }
      }
    }

    // Streak grid
    buildStreakGrid(data.streak_grid);

    // Streak label
    const streakLbl = document.getElementById('perf-streak-label');
    if (streakLbl) streakLbl.textContent = `${data.streak_days} day streak`;

  } catch (err) {
    // fails silently (you can add a toast or console.error here if needed)
  }
}


async function toggleTopicBreakdown(subjectId, rowEl) {
  // subjectId remains a string; we use it consistently for DOM ids and API path
  const container = document.getElementById(`topic-breakdown-${subjectId}`);
  if (!container) {
    console.warn('No topic container for subjectId:', subjectId);
    return;
  }

  const chevron = rowEl.querySelector('i');
  const isOpen  = container.style.display !== 'none';

  // Close ALL other open dropdowns first
  document.querySelectorAll('[id^="topic-breakdown-"]').forEach(el => {
    if (el.id !== `topic-breakdown-${subjectId}`) {
      el.style.display = 'none';
      // Reset their chevrons by finding the corresponding row via data-subject-id
      const otherId = el.id.replace('topic-breakdown-', '');
      const otherRow = document.querySelector(`.perf-row[data-subject-id="${otherId}"]`);
      if (otherRow) {
        const ch = otherRow.querySelector('i');
        if (ch) ch.className = 'bi bi-chevron-down';
      }
    }
  });

  // Toggle current
  if (isOpen) {
    container.style.display = 'none';
    if (chevron) chevron.className = 'bi bi-chevron-down';
    return;
  }

  container.style.display = 'block';
  if (chevron) chevron.className = 'bi bi-chevron-up';

  // Already loaded
  if (container.dataset.loaded) return;

  try {
    const res  = await API.fetch(`${API.BASE_URL}/subjects/${subjectId}/topics`);
    if (!res) return;

    const data = await res.json();

    if (!data.success || data.topics.length === 0) {
      container.innerHTML = '<p style="font-size:.78rem;color:var(--text2);padding:4px 0">No topic data yet</p>';
      container.dataset.loaded = '1';
      return;
    }

    const practiced = data.topics.filter(t => t.accuracy !== null);

    if (practiced.length === 0) {
      container.innerHTML = '<p style="font-size:.78rem;color:var(--text2);padding:4px 0">Practice this subject to see topic breakdown</p>';
      container.dataset.loaded = '1';
      return;
    }

    container.innerHTML = practiced.map(t => `
      <div style="margin-bottom:8px;cursor:pointer"
           onclick="openTopic(${t.id}, ${JSON.stringify(subjectId)}, '${t.name.replace(/'/g, "\\'")}')">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <span style="font-size:.8rem">${t.name}</span>
          <span style="font-size:.8rem;font-weight:700;color:${
            t.accuracy >= 70 ? 'var(--green)' :
            t.accuracy >= 50 ? 'var(--yellow)' : 'var(--red)'
          }">${t.accuracy}%</span>
        </div>
        <div style="background:var(--card2);border-radius:20px;height:4px;overflow:hidden">
          <div style="height:100%;width:${t.accuracy}%;background:${
            t.accuracy >= 70 ? 'var(--green)' :
            t.accuracy >= 50 ? 'var(--yellow)' : 'var(--red)'
          };border-radius:20px"></div>
        </div>
      </div>
    `).join('');

    container.dataset.loaded = '1';

  } catch (err) {
    console.error('Failed to load topics for subjectId:', subjectId, err);
    container.innerHTML = '<p style="font-size:.78rem;color:var(--red)">Failed to load topics</p>';
  }
}


function buildScoreChart(trendData) {
  const el = document.getElementById('sch');
  if (!el) return;
  el.removeAttribute('data-built');

  const scores = trendData && trendData.length > 0 ? trendData.map(t => t.score) : [0];
  const labels = trendData && trendData.length > 0 ? trendData.map(t => t.label) : ['--'];

  if (scores.length === 1) {
    el.innerHTML = '<p style="color:var(--text2);font-size:.85rem;text-align:center;padding:2rem 0;margin:0">Complete more sessions to see your score trend</p>';
    return;
  }

  const W      = el.offsetWidth || 300;
  const H      = 160;
  const pad    = { top: 20, right: 20, bottom: 30, left: 40 };
  const chartW = W - pad.left - pad.right;
  const chartH = H - pad.top - pad.bottom;
  const min    = Math.max(0, Math.min(...scores) - 10);
  const max    = Math.min(100, Math.max(...scores) + 10);
  const xStep  = scores.length > 1 ? chartW / (scores.length - 1) : chartW;
  const yScale = (v) => chartH - ((v - min) / (max - min)) * chartH;

  const pts     = scores.map((v, i) => `${pad.left + i * xStep},${pad.top + yScale(v)}`).join(' ');
  const areapts = `${pad.left},${pad.top + chartH} ` + pts + ` ${pad.left + (scores.length - 1) * xStep},${pad.top + chartH}`;

  let svg = `<svg width="${W}" height="${H}" style="overflow:visible">
    <defs>
      <linearGradient id="cg" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="#4f6ef7" stop-opacity="0.4"/>
        <stop offset="100%" stop-color="#4f6ef7" stop-opacity="0"/>
      </linearGradient>
    </defs>
    <polygon points="${areapts}" fill="url(#cg)"/>
    <polyline points="${pts}" fill="none" stroke="#4f6ef7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">`;

  scores.forEach((v, i) => {
    const x = pad.left + i * xStep;
    const y = pad.top + yScale(v);
    svg += `<circle cx="${x}" cy="${y}" r="4" fill="#4f6ef7" stroke="#06080f" stroke-width="2"/>`;
    svg += `<text x="${x}" y="${y - 10}" text-anchor="middle" font-size="10" fill="#94a3b8">${v}%</text>`;
    svg += `<text x="${x}" y="${H - 5}" text-anchor="middle" font-size="9" fill="#64748b">${labels[i]}</text>`;
  });

  svg += '</svg>';
  el.innerHTML = svg;
}

function buildStreakGrid(gridData) {
  const el = document.getElementById('sgrid');
  if (!el) return;

  if (!gridData || gridData.length === 0) {
    el.innerHTML = Array(35).fill(0).map(() =>
      `<div style="width:14px;height:14px;border-radius:3px;background:var(--card2);border:1px solid var(--border)"></div>`
    ).join('');
    return;
  }

  const colors = ['var(--card2)', 'var(--blue-dim)', 'var(--blue)', '#4f6ef7'];
  el.innerHTML  = gridData.map(d =>
    `<div style="width:14px;height:14px;border-radius:3px;background:${colors[d.level]};border:1px solid var(--border)" title="${d.date}: ${d.sessions} session(s)"></div>`
  ).join('');
}


document.querySelector('#s-perf .btn-o')?.addEventListener('click', loadStudyPlan);

async function loadStudyPlan() {
  modal('m-aiplan'); // open modal

  const bubble = document.querySelector('#m-aiplan .ai-bubble p');
  if (!bubble) return;

  // Loading state
  bubble.innerHTML = `
    <div style="text-align:center;padding:1rem 0">
      <div class="ai-spinner"></div>
      <p style="color:var(--text2);font-size:.85rem;margin-top:12px">
        Generating your study plan...
      </p>
    </div>
  `;

  try {
    const res = await API.fetch(`${API.BASE_URL}/performance/study-plan`);
    if (!res) return;

    const data = await res.json();

    if (!data.success) {
      if (data.reason === 'not_enough_data') {
        bubble.innerHTML = `
          You need at least 20 answered questions to unlock your personalised AI study plan.
          <br><br>You have answered <strong>${data.answered}</strong>.
        `;
      } else {
        bubble.innerHTML = "Could not generate your study plan. Please try again later.";
      }
      return;
    }

    // If cached
    if (data.cached) {
      bubble.innerHTML = `${data.plan}<br><br><small style="color:var(--text2)">Cached — refresh in ${data.days_left} day(s)</small>`;
      return;
    }

    // Fresh AI plan
    bubble.innerHTML = data.plan.replace(/\n/g, '<br>');

  } catch (err) {
    bubble.innerHTML = "Failed to load study plan. Check your connection.";
  }
}

// Call this once when dashboard loads
async function initAiPlanCard() {
  const statusEl = document.getElementById('ai-plan-status');
  if (!statusEl) return;

  try {
    const res = await API.fetch(`${API.BASE_URL}/performance/study-plan/status`);
    if (!res) return;

    const data = await res.json();

    if (!data.success) return;

    const hasPlan   = data.has_plan;
    const cached    = data.cached;

    // force clean integers
    const daysSince = data.days_since != null ? Math.round(Number(data.days_since)) : null;
    const daysLeft  = data.days_left  != null ? Math.round(Number(data.days_left))  : null;

    // No plan at all
    if (!hasPlan) {
      statusEl.textContent = "Tap to generate your AI study plan";
      return;
    }

    // Expired (>= 7 days)
    if (!cached) {
      statusEl.textContent = "Tap to refresh your study plan";
      return;
    }

    // Generated today
    if (daysSince === 0) {
      statusEl.textContent = "Generated today • Refresh in 7d";
      return;
    }

    // Valid, more than 1 day left
    if (daysLeft > 1) {
      statusEl.textContent = `Will refresh in ${daysLeft}d`;
      return;
    }

    // Will refresh tomorrow
    if (daysLeft === 1) {
      statusEl.textContent = "Will refresh tomorrow";
      return;
    }

  } catch (err) {
    // silent fail
  }
}

// Opens modal and fetches/returns cached/generates on demand
async function openAiPlanQuick() {
  modal('m-aiplan');
  await fetchAndShowStudyPlan();
}

// Reusable fetcher for the modal (same behavior as before, but last-7-days rules)
async function fetchAndShowStudyPlan() {
  const content   = document.getElementById('aiplan-content');
  const actionBtn = document.getElementById('aiplan-action-btn');
  if (!content) return;

  // Loading state
  content.innerHTML = `
    <div class="ai-bubble">
      <div style="text-align:center;padding:1rem 0">
        <div class="ai-spinner" style="margin:0 auto"></div>
        <p style="color:var(--text2);font-size:.85rem;margin-top:12px">
          Preparing your study plan...
        </p>
      </div>
    </div>`;

  try {
    const res  = await API.fetch(`${API.BASE_URL}/performance/study-plan`);
    if (!res) return;
    
    const data = await res.json();
if (!data.success) {
  let icon    = '📚';
  let message = '';

  if (data.reason === 'not_enough_data') {
    const remaining = 20 - (data.answered ?? 0);
    icon    = '🎯';
    message = `You've answered <strong style="color:#fff">${data.answered ?? 0} questions</strong> so far.<br><br>
               Answer <strong style="color:var(--blue2)">${remaining} more question${remaining !== 1 ? 's' : ''}</strong> to unlock your personalised AI study plan.`;
  } else if (data.reason === 'not_enough_recent_data') {
    icon    = '📅';
    message = `Not enough practice in the last 7 days.<br><br>
               Complete at least <strong style="color:#fff">3 sessions this week</strong> to unlock your personalised study plan.`;
  } else {
    icon    = '⚠️';
    message = data.message || 'Could not generate your study plan. Please try again later.';
  }

  content.innerHTML = `
    <div class="ai-bubble text-center">
      <div style="font-size:2.5rem;margin-bottom:12px">${icon}</div>
      <p style="font-size:.88rem;color:var(--text2);margin:0;line-height:1.7">${message}</p>
    </div>`;

  if (actionBtn) {
    actionBtn.textContent = 'Start Practicing Now';
    actionBtn.onclick     = () => { hModal('m-aiplan'); go('s-subj'); };
  }
  return;
}

    // Format the plan — split by lines and style each one
    const lines     = (data.plan || '').split('\n').filter(l => l.trim() !== '');
    const formatted = lines.map(line => {
      // Day headers e.g. "Day 1:" or "**Day 1**"
      if (/^(day\s*\d+|week\s*\d+|\*\*day|\*\*week)/i.test(line.trim())) {
        const clean = line.replace(/\*\*/g, '').trim();
        return `<div style="font-weight:700;font-size:.92rem;color:var(--blue2);margin:14px 0 6px;padding-top:10px;border-top:1px solid var(--border)">${clean}</div>`;
      }
      // Bullet points
      if (/^[-•*]\s/.test(line.trim())) {
        const clean = line.replace(/^[-•*]\s/, '').replace(/\*\*/g, '');
        return `<div class="notes-bullet" style="margin-bottom:6px">• ${clean}</div>`;
      }
      // Subject/topic lines with percentage e.g. "Chemistry — Organic Chemistry (32%)"
      if (line.includes('%') || line.includes('→') || line.includes('—')) {
        const clean = line.replace(/\*\*/g, '').trim();
        return `<div style="font-size:.85rem;color:var(--text);margin-bottom:6px;padding:8px 10px;background:var(--card2);border-radius:8px;border-left:3px solid var(--blue)">${clean}</div>`;
      }
      // Regular line
      const clean = line.replace(/\*\*/g, '').trim();
      return `<div style="font-size:.85rem;color:var(--text2);margin-bottom:6px;line-height:1.6">${clean}</div>`;
    }).join('');

    // Footer
    const daysLeft  = Math.round(Number(data.days_left  ?? 0));
    const daysSince = Math.round(Number(data.days_since ?? 0));
    let footerText  = '';

    if (!data.cached) {
      footerText = 'Generated now · Refreshes in 7 days';
    } else if (daysSince === 0) {
      footerText = 'Generated today · Refreshes in 7 days';
    } else if (daysLeft > 1) {
      footerText = `Refreshes in ${daysLeft} day${daysLeft !== 1 ? 's' : ''}`;
    } else if (daysLeft === 1) {
      footerText = 'Refreshes tomorrow';
    } else {
      footerText = 'Refresh soon';
    }

    content.innerHTML = `
      <div class="ai-bubble" style="max-height:55vh;overflow-y:auto">
        ${formatted}
        <div style="margin-top:16px;padding-top:10px;border-top:1px solid var(--border);font-size:.72rem;color:var(--text2);text-align:center">
          <i class="bi bi-stars" style="color:var(--blue2)"></i> ${footerText}
        </div>
      </div>`;

    // Change button to practice weak areas
    if (actionBtn) {
      actionBtn.textContent = 'Start Practicing Now →';
      actionBtn.onclick     = () => { hModal('m-aiplan'); go('s-subj'); };
    }

    initAiPlanCard?.();

  } catch (err) {
    content.innerHTML = `
      <div class="ai-bubble text-center">
        <p style="color:var(--red);font-size:.85rem;margin:0">
          Failed to load study plan. Check your connection.
        </p>
      </div>`;
  }
}

// Kept for backward compat — no-op now
function buildCharts() {}