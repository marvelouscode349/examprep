/* ============================================================
   ui.js — Screen navigation, modals, toasts, form helpers
   ============================================================ */

function go(id) {
  document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  window.scrollTo(0, 0);
  if (id === 's-perf') loadPerformance();
  if (id === 's-dash') loadDashboard();
}

function modal(id) {
  document.getElementById(id).classList.add('active');
}

function hModal(id) {
  document.getElementById(id).classList.remove('active');
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.modal-ov').forEach(o => {
    o.addEventListener('click', function (e) {
      if (e.target === this) hModal(this.id);
    });
  });
});

function toast(msg, type = 'info') {
  const container = document.getElementById('tc');
  const t         = document.createElement('div');
  t.className     = `toast-m toast-${type}`;

  const icons = {
    success: 'bi-check-circle-fill',
    error:   'bi-x-circle-fill',
    info:    'bi-info-circle-fill'
  };

  t.innerHTML = `<i class="bi ${icons[type]}"></i>${msg}`;
  container.appendChild(t);

  setTimeout(() => {
    t.style.opacity    = '0';
    t.style.transition = 'opacity .4s';
    setTimeout(() => t.remove(), 400);
  }, 3000);
}

function tp(id, icon) {
  const input = document.getElementById(id);
  if (input.type === 'password') {
    input.type     = 'text';
    icon.className = 'bi bi-eye-slash';
  } else {
    input.type     = 'password';
    icon.className = 'bi bi-eye';
  }
}

function tsw(el) {
  const thumb = el.querySelector('div');
  const isOn  = el.style.background.includes('59') || el.style.background === 'var(--blue)';
  if (isOn) {
    el.style.background    = 'var(--card2)';
    el.style.border        = '1px solid var(--border)';
    thumb.style.right      = '';
    thumb.style.left       = '3px';
    thumb.style.background = 'var(--text2)';
  } else {
    el.style.background    = 'var(--blue)';
    el.style.border        = 'none';
    thumb.style.left       = '';
    thumb.style.right      = '3px';
    thumb.style.background = '#fff';
  }
}

function updateStream() {
  const exam = document.getElementById('reg-exam').value;
  document.getElementById('streamDiv').style.display = exam ? 'block' : 'none';
}

function confetti() {
  const colors = ['#4f6ef7', '#10d9a0', '#a855f7', '#f59e0b', '#ef4444'];
  for (let i = 0; i < 60; i++) {
    const dot = document.createElement('div');
    dot.style.cssText = `
      position:fixed;top:50%;left:50%;
      width:8px;height:8px;border-radius:50%;
      background:${colors[i % colors.length]};
      pointer-events:none;z-index:99999;
      animation:cfetti 1s ease-out forwards;
      --dx:${(Math.random() - 0.5) * 400}px;
      --dy:${(Math.random() - 1.2) * 400}px;
    `;
    document.body.appendChild(dot);
    setTimeout(() => dot.remove(), 1100);
  }
}

function rvtgl(id, btn) {
  const el       = document.getElementById(id);
  const isHidden = el.style.display === 'none';
  el.style.display = isHidden ? 'block' : 'none';
  btn.textContent  = isHidden ? 'Hide Explanation' : 'Show AI Explanation';
}

function faq(el) {
  const isOpen = el.classList.contains('open');
  document.querySelectorAll('.faq-q').forEach(q => {
    q.classList.remove('open');
    q.nextElementSibling.style.display = 'none';
    q.querySelector('i').style.transform = '';
  });
  if (!isOpen) {
    el.classList.add('open');
    el.nextElementSibling.style.display   = 'block';
    el.querySelector('i').style.transform = 'rotate(180deg)';
  }
}

function setF(el) {
  document.querySelectorAll('#s-subj button').forEach(b => {
    b.style.background = 'var(--card)';
    b.style.color      = 'var(--text2)';
    b.style.border     = '1px solid var(--border)';
  });
  el.style.background = 'var(--blue)';
  el.style.color      = '#fff';
  el.style.border     = 'none';
}

function switchStream(btn, stream) {
  ['science', 'arts', 'commercial'].forEach(s => {
    const el = document.getElementById(`stream-${s}`);
    if (el) el.style.display = 'none';
  });
  const target = document.getElementById(`stream-${stream}`);
  if (target) target.style.display = '';

  document.querySelectorAll('.stream-btn').forEach(b => {
    b.style.background = 'var(--card)';
    b.style.color      = 'var(--text2)';
    b.style.border     = '1px solid var(--border)';
  });
  btn.style.background = 'var(--blue)';
  btn.style.color      = '#fff';
  btn.style.border     = 'none';
}

let obCurrent = 1;
function nextOb() {
  document.getElementById(`ob${obCurrent}`).classList.remove('active');
  document.getElementById(`d${obCurrent}`).classList.remove('active');
  obCurrent++;
  if (obCurrent > 3) { go('s-dash'); return; }
  document.getElementById(`ob${obCurrent}`).classList.add('active');
  document.getElementById(`d${obCurrent}`).classList.add('active');
  if (obCurrent === 3) document.getElementById('ob-btn').textContent = 'Get Started →';
}

function selMode(el) {
  document.querySelectorAll('#m-setup .card-d2').forEach(c => {
    c.style.borderColor = 'var(--border)';
  });
  el.style.borderColor = 'var(--blue)';
  Quiz.selectedMode    = el.dataset.mode || 'practice';
}
