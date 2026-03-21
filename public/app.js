/* ============================================================
   API CONFIG
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
      'Accept': 'application/json',
      'Authorization': `Bearer ${this.token()}`
    };
  }
};


/* ============================================================
   UPDATE PROFILE UI
   ============================================================ */
function updateProfileUI(user) {
  const firstName = user.name.split(' ')[0];

  const greetEl = document.querySelector('#s-dash h2');
  if (greetEl) greetEl.textContent = `Good morning, ${firstName}`;

  const profileName = document.querySelector('#s-profile h3');
  if (profileName) profileName.textContent = user.name;

  const profileEmail = document.querySelector('#s-profile .card-d');
  if (profileEmail) {
    const rows = profileEmail.querySelectorAll('.d-flex');
    rows.forEach(row => {
      const label = row.querySelector('span:first-child');
      const value = row.querySelector('span:last-child');
      if (!label || !value) return;
      if (label.textContent.trim() === 'Email')       value.textContent = user.email;
      if (label.textContent.trim() === 'Phone')       value.textContent = user.phone || '--';
      if (label.textContent.trim() === 'Target Exam') value.textContent = user.target_exam || '--';
      if (label.textContent.trim() === 'State')       value.textContent = user.state || '--';
    });
  }

  const badge = document.querySelector('#s-profile .bdg-plan');
  if (badge) badge.textContent = user.subscription_status === 'active' ? 'Premium Plan' : 'Free Plan';
}


/* ============================================================
   REGISTER
   ============================================================ */
async function handleRegister() {
  const name     = document.getElementById('reg-name').value.trim();
  const email    = document.getElementById('reg-email').value.trim();
  const phone    = document.getElementById('reg-phone').value.trim();
  const password = document.getElementById('reg-password').value;
  const exam     = document.getElementById('reg-exam').value;
  const stream   = document.getElementById('reg-stream').value;
  const year     = document.getElementById('reg-year').value;
  const state    = document.getElementById('reg-state').value;
  const btn      = document.getElementById('reg-btn');

  document.getElementById('reg-error').style.display = 'none';

  if (!name)                            return showRegError('Please enter your full name');
  if (!email)                           return showRegError('Please enter your email address');
  if (!password || password.length < 6) return showRegError('Password must be at least 6 characters');
  if (!exam)                            return showRegError('Please select your target exam');
  if (!stream)                          return showRegError('Please select your subject stream');
  if (!state)                           return showRegError('Please select your state');

  btn.textContent = 'Creating account...';
  btn.disabled    = true;

  try {
    const res = await fetch(`${API.BASE_URL}/auth/register`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ name, email, phone: phone || null, password, target_exam: exam, stream, exam_year: year, state })
    });

    const data = await res.json();

    if (data.success) {
      API.saveSession(data.token, data.user);
      updateProfileUI(data.user);
      toast('Account created! Welcome', 'success');
      go('s-onboard');
    } else {
      if (data.errors) {
        showRegError(Object.values(data.errors)[0][0]);
      } else {
        showRegError(data.message || 'Registration failed. Try again.');
      }
    }
  } catch (err) {
    showRegError('Network error. Check your connection and try again.');
  } finally {
    btn.textContent = 'Create My Account';
    btn.disabled    = false;
  }
}

function showRegError(msg) {
  const box = document.getElementById('reg-error');
  box.textContent = msg;
  box.style.display = 'block';
  box.scrollIntoView({ behavior: 'smooth', block: 'center' });
}


/* ============================================================
   LOGIN
   ============================================================ */
async function handleLogin() {
  const email    = document.getElementById('login-email').value.trim();
  const password = document.getElementById('login-password').value;
  const btn      = document.getElementById('login-btn');

  document.getElementById('login-error').style.display = 'none';

  if (!email)    return showLoginError('Please enter your email');
  if (!password) return showLoginError('Please enter your password');

  btn.textContent = 'Logging in...';
  btn.disabled    = true;

  try {
    const res = await fetch(`${API.BASE_URL}/auth/login`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({ email, password })
    });

    const data = await res.json();

    if (data.success) {
      API.saveSession(data.token, data.user);
      updateProfileUI(data.user);
      toast(`Welcome back, ${data.user.name.split(' ')[0]}!`, 'success');
      await loadSubjects();
      await loadDashboard();
      go('s-dash');
    } else {
      showLoginError(data.message || 'Incorrect email or password.');
    }
  } catch (err) {
    showLoginError('Network error. Check your connection and try again.');
  } finally {
    btn.textContent = 'Login to My Account';
    btn.disabled    = false;
  }
}

function showLoginError(msg) {
  const box = document.getElementById('login-error');
  box.textContent   = msg;
  box.style.display = 'block';
}


/* ============================================================
   LOGOUT
   ============================================================ */
async function handleLogout() {
  try {
    await fetch(`${API.BASE_URL}/auth/logout`, {
      method: 'POST',
      headers: API.headers()
    });
  } catch (err) {}
  finally {
    API.clearSession();
    hModal('m-logout');
    go('s-land');
    toast('Logged out successfully', 'info');
  }
}


/* ============================================================
   SCREEN NAVIGATION
   ============================================================ */
function go(id) {
  document.querySelectorAll('.screen').forEach(s => s.classList.remove('active'));
  document.getElementById(id).classList.add('active');
  window.scrollTo(0, 0);
  if (id === 's-perf') loadPerformance(); 
  if (id === 's-dash') loadDashboard();
}


/* ============================================================
   DASHBOARD
   ============================================================ */
async function loadDashboard() {
  try {
    const res  = await fetch(`${API.BASE_URL}/dashboard`, { headers: API.headers() });
    const data = await res.json();
    if (!data.success) return;

    const hour      = new Date().getHours();
    const greeting  = hour < 12 ? 'Good morning' : hour < 17 ? 'Good afternoon' : 'Good evening';
    const user      = API.user();
    const firstName = user?.name?.split(' ')[0] || 'Student';
    const greetEl   = document.querySelector('#s-dash h2');
    if (greetEl) greetEl.textContent = `${greeting}, ${firstName}`;

    const statCards = document.querySelectorAll('#s-dash .stat-card');

    const accuracyEl = statCards[0]?.querySelector('.stat-val');
    if (accuracyEl) {
      accuracyEl.textContent = data.overall_accuracy !== null ? `${data.overall_accuracy}%` : '--';
      accuracyEl.className   = `stat-val ${data.overall_accuracy >= 70 ? 'text-g' : data.overall_accuracy >= 50 ? 'text-y' : 'text-r'}`;
    }

    const answeredEl = statCards[1]?.querySelector('.stat-val');
    if (answeredEl) answeredEl.textContent = data.total_answered.toLocaleString();

    const streakEl = statCards[2]?.querySelector('.stat-val');
    if (streakEl) streakEl.innerHTML = `<i class="bi bi-fire" style="font-size:1.2rem"></i> ${data.streak}`;
// Update profile stats
const profileQ  = document.getElementById('profile-questions');
const profileA  = document.getElementById('profile-accuracy');
const profileS  = document.getElementById('profile-streak');

if (profileQ) profileQ.textContent = data.total_answered.toLocaleString();
if (profileA) profileA.textContent = data.overall_accuracy !== null ? `${data.overall_accuracy}%` : '--';
if (profileS) profileS.innerHTML   = `<i class="bi bi-fire" style="font-size:1rem"></i> ${data.streak}`;

    const scoreEl = statCards[3]?.querySelector('.stat-val');
    if (scoreEl) {
      scoreEl.textContent = data.estimated_score !== null ? data.estimated_score : '--';
      scoreEl.className   = `stat-val ${data.estimated_score >= 250 ? 'text-g' : data.estimated_score >= 180 ? 'text-y' : 'text-r'}`;
    }

    const tipEl = document.querySelector('.ai-bubble p');
    if (tipEl && data.ai_tip) tipEl.textContent = data.ai_tip;

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
              <div class="fd" style="font-size:1.1rem;font-weight:700;color:${s.score_percentage >= 70 ? 'var(--green)' : s.score_percentage >= 50 ? 'var(--yellow)' : 'var(--red)'}">${s.score_percentage}%</div>
              <div style="font-size:.72rem;color:var(--text2)">${s.correct}/${s.total}</div>
            </div>
          </div>
        `).join('');
      }
    }

    const weakBtn = document.getElementById('weak-areas-btn');
    if (weakBtn && data.weakest_subject_id) {
      weakBtn.style.display = 'block';
      weakBtn.onclick = () => loadWeakAreas(data.weakest_subject_id);
    }

  } catch (err) {}
}


/* ============================================================
   MODAL CONTROL
   ============================================================ */
function modal(id) {
  document.getElementById(id).classList.add('active');
}

function hModal(id) {
  document.getElementById(id).classList.remove('active');
}

document.querySelectorAll('.modal-ov').forEach(o => {
  o.addEventListener('click', function (e) {
    if (e.target === this) hModal(this.id);
  });
});


/* ============================================================
   TOAST NOTIFICATIONS
   ============================================================ */
function toast(msg, type = 'info') {
  const container = document.getElementById('tc');
  const t         = document.createElement('div');
  t.className     = `toast-m toast-${type}`;

  const icons = { success: 'bi-check-circle-fill', error: 'bi-x-circle-fill', info: 'bi-info-circle-fill' };
  t.innerHTML = `<i class="bi ${icons[type]}"></i>${msg}`;
  container.appendChild(t);

  setTimeout(() => {
    t.style.opacity    = '0';
    t.style.transition = 'opacity .4s';
    setTimeout(() => t.remove(), 400);
  }, 3000);
}


/* ============================================================
   FORM HELPERS
   ============================================================ */
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


/* ============================================================
   QUIZ ENGINE
   ============================================================ */
const Quiz = {
  sessionId:         null,
  questions:         [],
  answers:           {},
  currentIdx:        0,
  answered:          0,
  startedAt:         null,
  timerInt:          null,      // per-question 45s timer (practice only) — cleared by ans()
  mockTimerInt:      null,      // full 1hr mock timer — NEVER cleared by ans()
  timerSec:          45,
  isMock:            false,
  mockTimeLeft:      3600,
  selectedSubjectId: null,
  selectedMode:      'practice',

  reset() {
    clearInterval(this.timerInt);
    clearInterval(this.mockTimerInt);
    this.timerInt         = null;
    this.mockTimerInt     = null;
    this.sessionId        = null;
    this.questions        = [];
    this.answers          = {};
    this.currentIdx       = 0;
    this.answered         = 0;
    this.startedAt        = null;
    this.timerSec         = 45;
    this.isMock           = false;
    this.mockTimeLeft     = 3600;
  }
};


/* ============================================================
   MOCK EXAM
   ============================================================ */
async function loadMockSubjects() {
  go('s-mock');
  const container = document.getElementById('mock-subject-list');
  if (!container) return;

  try {
    const res  = await fetch(`${API.BASE_URL}/user/subjects`, { headers: API.headers() });
    const data = await res.json();
    if (!data.success || !data.subjects) return;

    container.innerHTML = data.subjects.map(s => `
      <div class="col-6">
        <div class="subj-card" onclick="startMockExam(${s.id})">
          <div class="subj-icon">${s.icon || '📚'}</div>
          <div style="font-weight:600;font-size:.88rem">${s.name}</div>
          <div style="font-size:.72rem;color:var(--text2);margin-top:2px">60 questions · 1hr</div>
        </div>
      </div>
    `).join('');
  } catch (err) {
    container.innerHTML = '<p style="color:var(--red);font-size:.85rem;text-align:center">Failed to load subjects.</p>';
  }
}

async function startMockExam(subjectId) {
  Quiz.reset();
  Quiz.selectedSubjectId = subjectId;
  Quiz.selectedMode      = 'mock';
  Quiz.isMock            = true;

  go('s-quiz');
  document.getElementById('qtext').textContent = 'Loading mock exam...';

  try {
    const res = await fetch(`${API.BASE_URL}/quiz/start`, {
      method: 'POST',
      headers: API.headers(),
      body: JSON.stringify({ subject_id: subjectId, mode: 'mock', exam_type: 'JAMB', total_questions: 60 })
    });

    const data = await res.json();

    if (!data.success || !data.questions || data.questions.length === 0) {
      toast('Not enough questions for mock exam. Try practice mode.', 'error');
      go('s-dash');
      return;
    }

    Quiz.sessionId = data.session_id;
    Quiz.questions = data.questions;
    Quiz.startedAt = Date.now();

    Quiz.answers = {};
    data.questions.forEach(q => { Quiz.answers[q.id] = q.correct_answer?.toUpperCase(); });

    const totalEl = document.getElementById('qtotal');
    if (totalEl) totalEl.textContent = Quiz.questions.length;

    loadQ(0);
    startMockTimer();

  } catch (err) {
    toast('Failed to load mock exam. Check your connection.', 'error');
    go('s-dash');
  }
}


/* ============================================================
   MOCK TIMER — uses mockTimerInt, never touched by ans()
   ============================================================ */
function startMockTimer() {
  clearInterval(Quiz.mockTimerInt);
  Quiz.mockTimerInt = null;
  Quiz.mockTimeLeft = 3600;

  const timerEl = document.getElementById('qtimer');

  function formatTime(secs) {
    const h = Math.floor(secs / 3600);
    const m = Math.floor((secs % 3600) / 60);
    const s = secs % 60;
    return h > 0
      ? `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
      : `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
  }

  if (timerEl) {
    timerEl.textContent = `⏱ ${formatTime(Quiz.mockTimeLeft)}`;
    timerEl.style.color = 'var(--yellow)';
  }

  Quiz.mockTimerInt = setInterval(() => {
    Quiz.mockTimeLeft--;

    if (timerEl) {
      timerEl.textContent = `⏱ ${formatTime(Quiz.mockTimeLeft)}`;
      timerEl.style.color = Quiz.mockTimeLeft <= 300 ? 'var(--red)' : 'var(--yellow)';
    }

    if (Quiz.mockTimeLeft === 300) {
      toast('⚠️ 5 minutes remaining! Tap Submit when done.', 'error');
    }

    if (Quiz.mockTimeLeft <= 0) {
      clearInterval(Quiz.mockTimerInt);
      Quiz.mockTimerInt = null;
      toast('⏰ Time is up! Tap Submit Quiz to finish.', 'error');
      // Force show submit button
      const nextBtn = document.getElementById('nextbtn');
      if (nextBtn) {
        nextBtn.style.display = 'block';
        nextBtn.disabled      = false;
        nextBtn.textContent   = 'Submit Quiz ✓';
        nextBtn.onclick       = () => {
          nextBtn.disabled  = true;
          nextBtn.innerHTML = '<span class="ai-spinner" style="width:16px;height:16px;border-width:2px;display:inline-block;vertical-align:middle;margin-right:8px"></span>Submitting...';
          finishQuiz();
        };
      }
    }
  }, 1000);
}


/* ============================================================
   WEAK AREAS
   ============================================================ */
async function loadWeakAreas(subjectId) {
  Quiz.selectedSubjectId = subjectId;
  Quiz.selectedMode      = 'weak_areas';
  Quiz.isMock            = false;
  await startQuiz(subjectId, 'weak_areas', 20);
}


/* ============================================================
   START QUIZ
   ============================================================ */
async function startQuiz(subjectId, mode = 'practice', total = 20) {
  Quiz.reset();
  Quiz.selectedSubjectId = subjectId;
  Quiz.selectedMode      = mode;

  go('s-quiz');
  document.getElementById('qtext').textContent = 'Loading questions...';

  try {
    const res = await fetch(`${API.BASE_URL}/quiz/start`, {
      method: 'POST',
      headers: API.headers(),
      body: JSON.stringify({ subject_id: subjectId, mode, exam_type: 'JAMB', total_questions: total })
    });

    const data = await res.json();

    if (!data.success || !data.questions || data.questions.length === 0) {
      toast('No questions found for this subject. Try another.', 'error');
      go('s-dash');
      return;
    }

    Quiz.sessionId = data.session_id;
    Quiz.questions = data.questions;
    Quiz.startedAt = Date.now();

    Quiz.answers = {};
    data.questions.forEach(q => { Quiz.answers[q.id] = q.correct_answer?.toUpperCase(); });

    const totalEl = document.getElementById('qtotal');
    if (totalEl) totalEl.textContent = Quiz.questions.length;

    loadQ(0);
    if (mode !== 'mock') startTimer();

  } catch (err) {
    toast('Failed to load questions. Check your connection.', 'error');
    go('s-dash');
  }
}


/* ============================================================
   LOAD QUESTION
   ============================================================ */
function loadQ(index) {
  // Scroll to top on every question change
  window.scrollTo({ top: 0, behavior: 'smooth' });
  const quizScreen = document.getElementById('s-quiz');
  if (quizScreen) quizScreen.scrollTop = 0;



  // Only finish if questions are actually loaded and we've run out
  if (Quiz.questions.length > 0 && index >= Quiz.questions.length) {
    finishQuiz();
    return;
  }

  // Safety — if no questions loaded yet, don't render
  if (Quiz.questions.length === 0) return;

  const q = Quiz.questions[index];

  // Question counter and progress bar
  const curEl = document.getElementById('qcur');
  if (curEl) curEl.textContent = index + 1;

  const progEl = document.getElementById('qprog');
  if (progEl) progEl.style.width = `${((index + 1) / Quiz.questions.length) * 100}%`;

  // Subject label — exam type + year
  const subjEl = document.getElementById('qsubj');
  if (subjEl) subjEl.textContent = `${q.exam_type} ${q.year}`;

  // Render question text as HTML (supports <sub>, <sup>, <b> etc)
  document.getElementById('qtext').innerHTML = q.question_text;

  // Show/hide question image
  const qimg = document.getElementById('qimg');
  if (qimg) {
    if (q.image_url) {
      qimg.src           = q.image_url;
      qimg.style.display = 'block';
    } else {
      qimg.src           = '';
      qimg.style.display = 'none';
    }
  }

  // Render options as HTML
  const opts = { A: q.option_a, B: q.option_b, C: q.option_c, D: q.option_d };
  ['A', 'B', 'C', 'D'].forEach(letter => {
    const textEl = document.getElementById(`o${letter}t`);
    const optEl  = document.getElementById(`o${letter}`);
    if (textEl) textEl.innerHTML = opts[letter];
    if (optEl) {
      optEl.className = 'quiz-opt';
      optEl.onclick   = () => ans(optEl, letter, q.id);
    }
  });

  // Next / Submit button
  const nextBtn = document.getElementById('nextbtn');
  if (nextBtn) {
    nextBtn.style.display = 'none';
    nextBtn.disabled      = false;
    const isLast          = index === Quiz.questions.length - 1;
    nextBtn.textContent   = isLast ? 'Submit Quiz ✓' : 'Next Question →';
    nextBtn.onclick       = isLast
      ? () => {
          nextBtn.disabled  = true;
          nextBtn.innerHTML = '<span class="ai-spinner" style="width:16px;height:16px;border-width:2px;display:inline-block;vertical-align:middle;margin-right:8px"></span>Submitting...';
          finishQuiz();
        }
      : () => nxtQ();
  }

  // Clear previous AI explanation
  const expBox = document.getElementById('m-aiexp-text');
  if (expBox) expBox.textContent = '';
}


/* ============================================================
   ANSWER A QUESTION
   ============================================================ */
async function ans(el, chosen, questionId) {
  ['A', 'B', 'C', 'D'].forEach(l => {
    const o = document.getElementById(`o${l}`);
    if (o) o.onclick = null;
  });

  el.classList.add('opt-selected');

  // Only clear per-question timer — NEVER touch mockTimerInt
  clearInterval(Quiz.timerInt);
  Quiz.timerInt = null;

  const timeSpent     = Math.round((Date.now() - Quiz.startedAt) / 1000);
  const correctLetter = Quiz.answers[questionId];
  const isCorrect     = correctLetter?.toUpperCase() === chosen?.toUpperCase();

  el.classList.remove('opt-selected');

  if (isCorrect) {
    el.classList.add('opt-correct');
    toast('Correct! 🎉', 'success');
    setTimeout(() => nxtQ(), Quiz.isMock ? 600 : 1000);
  } else {
    el.classList.add('opt-wrong');
    const correctEl = document.getElementById(`o${correctLetter?.toUpperCase()}`);
    if (correctEl) correctEl.classList.add('opt-correct');

    if (Quiz.isMock) {
      // Mock — no AI, auto advance
      toast('Wrong — correct answer highlighted', 'error');
      setTimeout(() => nxtQ(), 1200);
    } else {
      // Practice — show AI explanation
      toast('Wrong answer', 'error');
      const expBox = document.getElementById('m-aiexp-text');
      if (expBox) {
        expBox.innerHTML = `
          <div style="text-align:center;padding:1rem 0">
            <div class="ai-spinner"></div>
            <p style="color:var(--text2);font-size:.83rem;margin-top:12px">AI is generating explanation...</p>
          </div>`;
      }
      modal('m-aiexp');
      fetchExplanation(questionId, expBox);

      // Show next button so student can proceed after reading
      const nextBtn = document.getElementById('nextbtn');
      if (nextBtn) nextBtn.style.display = 'block';
    }
  }

  Quiz.answered++;

  // Log to server in background
  fetch(`${API.BASE_URL}/quiz/submit`, {
    method: 'POST',
    headers: API.headers(),
    body: JSON.stringify({
      session_id:    Quiz.sessionId,
      question_id:   questionId,
      chosen_answer: chosen,
      time_spent:    timeSpent
    })
  }).catch(() => {});
}


/* ============================================================
   FETCH AI EXPLANATION
   ============================================================ */
async function fetchExplanation(questionId, expBox) {
  try {
    const res  = await fetch(`${API.BASE_URL}/quiz/explanation/${questionId}/stream`, { headers: API.headers() });
    const data = await res.json();

    if (!data.explanation) {
      if (expBox) expBox.innerHTML = 'Explanation not available for this question yet.';
      return;
    }

    typeWriter(expBox, data.explanation);
  } catch (err) {
    if (expBox) expBox.innerHTML = 'Could not load explanation. Check your connection.';
  }
}

function typeWriter(el, text, speed = 5) {
  if (!el) return;
  el.innerHTML = '';
  let i = 0;

  function tick() {
    if (i < text.length) {
      el.innerHTML = text.slice(0, i + 1);
      i++;
      setTimeout(tick, speed);
    }
  }

  tick();
}


/* ============================================================
   NEXT QUESTION
   ============================================================ */
function nxtQ() {
  hModal('m-aiexp');
  Quiz.currentIdx++;
  loadQ(Quiz.currentIdx);
  if (!Quiz.isMock) startTimer();
}


/* ============================================================
   FINISH QUIZ
   ============================================================ */
async function finishQuiz() {
  // Guard — prevent duplicate calls
  if (!Quiz.sessionId) return;
  const sessionId   = Quiz.sessionId;
  Quiz.sessionId    = null; // clear immediately so double-calls are ignored

  clearInterval(Quiz.timerInt);
  clearInterval(Quiz.mockTimerInt);
  Quiz.timerInt     = null;
  Quiz.mockTimerInt = null;

  const timeTaken = Math.round((Date.now() - Quiz.startedAt) / 1000);


 // Show submitting overlay — separate div, never touches qcard
const existing = document.getElementById('submit-overlay');
if (existing) existing.remove();

const overlay = document.createElement('div');
overlay.id = 'submit-overlay';
overlay.style.cssText = `
  position:fixed;top:0;left:0;width:100%;height:100%;
  background:var(--bg, #06080f);z-index:9999;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  padding:2rem;text-align:center;
`;
overlay.innerHTML = `
  <div class="ai-spinner" style="width:48px;height:48px;border-width:4px;margin-bottom:24px"></div>
  <div class="fd" style="font-size:1.2rem;margin-bottom:8px">Submitting your quiz...</div>
  <p style="color:var(--text2);font-size:.88rem;margin:0">Calculating your score and updating your stats</p>
`;
document.body.appendChild(overlay);

  try {
    const res = await fetch(`${API.BASE_URL}/quiz/session/${sessionId}/finish`, {
      method: 'POST',
      headers: API.headers(),
      body: JSON.stringify({ time_taken: timeTaken })
    });

    const data = await res.json();

    if (data.success) {
      const scoreEl = document.getElementById('res-score');
      if (scoreEl) scoreEl.textContent = `${data.score_percentage}%`;

      const correctEl = document.getElementById('res-correct');
      if (correctEl) correctEl.textContent = data.correct;

      const wrongEl = document.getElementById('res-wrong');
      if (wrongEl) wrongEl.textContent = data.wrong;

      const skippedEl = document.getElementById('res-skipped');
      if (skippedEl) skippedEl.textContent = data.skipped;

      const timeEl = document.getElementById('res-time');
      if (timeEl) {
        const mins = Math.floor(data.time_taken / 60);
        const secs = data.time_taken % 60;
        timeEl.textContent = `${mins}m ${secs}s`;
      }

      const fractionEl = document.getElementById('res-fraction');
      if (fractionEl) fractionEl.textContent = `${data.correct}/${data.total}`;

      const emoji     = document.querySelector('#s-res .text-center div:first-child');
      const heading   = document.querySelector('#s-res h3');
      const subtext   = document.querySelector('#s-res .text-center p');
      const reviewBtn = document.getElementById('res-review-btn');
      const weakBtn   = document.getElementById('res-weak-btn');

      if (Quiz.isMock) {
        if (emoji)   emoji.textContent   = data.score_percentage >= 70 ? '🏆' : data.score_percentage >= 50 ? '📊' : '📉';
        if (heading) heading.textContent = data.score_percentage >= 70 ? 'Excellent Mock Score!' : data.score_percentage >= 50 ? 'Good Effort!' : 'Keep Practicing!';
        if (subtext) subtext.textContent = data.score_percentage >= 70
          ? `You scored ${data.score_percentage}% — you're on track for JAMB 2026!`
          : data.score_percentage >= 50
          ? `You scored ${data.score_percentage}% — focus on your weak areas to improve.`
          : `You scored ${data.score_percentage}% — don't give up, practice daily to improve.`;

        const scoreCircle = document.querySelector('.res-circle .fd');
        if (scoreCircle) {
          const jamb = Math.round((data.score_percentage / 100) * 100);
          scoreCircle.innerHTML = `${data.score_percentage}%<div style="font-size:.65rem;color:var(--blue2);margin-top:2px">~${jamb}/100 JAMB</div>`;
        }

        if (reviewBtn) reviewBtn.style.display = 'none';
        if (weakBtn)   weakBtn.style.display   = data.score_percentage < 70 ? 'block' : 'none';

      } else {
        if (emoji)   emoji.textContent   = data.score_percentage >= 80 ? '🎉' : data.score_percentage >= 60 ? '👍' : '💪';
        if (heading) heading.textContent = data.score_percentage >= 80 ? 'Outstanding!' : data.score_percentage >= 60 ? 'Well done!' : 'Keep going!';
        if (subtext) subtext.textContent = data.score_percentage >= 80
          ? 'You\'re mastering this subject. Keep the streak alive!'
          : data.score_percentage >= 60
          ? 'Good session. Review wrong answers to push higher.'
          : 'Review your wrong answers — the AI explanations will help you understand.';

        if (reviewBtn) reviewBtn.style.display = 'block';
        if (weakBtn)   weakBtn.style.display   = data.score_percentage < 70 ? 'block' : 'none';
      }
const overlay = document.getElementById('submit-overlay');
if (overlay) overlay.remove();
      Quiz.isMock = false;
      go('s-res');
      if (data.score_percentage >= 70) confetti();
    }

  } catch (err) {
    toast('Could not save results. Try again.', 'error');
      const overlay = document.getElementById('submit-overlay');
  if (overlay) overlay.remove();
    // Restore quiz screen on failure
    if (optsEl)  optsEl.style.display  = 'block';
    
  }
}


/* ============================================================
   REVIEW SESSION
   ============================================================ */
async function reviewSession() {
  if (!Quiz.sessionId) return;

  go('s-review');

  const container = document.getElementById('review-list');
  if (!container) return;
  container.innerHTML = '<p style="color:var(--text2)">Loading review...</p>';

  try {
    const res  = await fetch(`${API.BASE_URL}/quiz/session/${Quiz.sessionId}/review`, { headers: API.headers() });
    const data = await res.json();

    if (!data.success || data.wrong_answers.length === 0) {
      container.innerHTML = '<p style="color:var(--green)">🎉 You got everything correct!</p>';
      return;
    }

    container.innerHTML = data.wrong_answers.map((item, i) => `
      <div class="card-d" style="margin-bottom:1rem">
        <p style="font-weight:600;margin-bottom:.5rem">${i + 1}. ${item.question_text}</p>
        <div style="margin-bottom:.75rem">
          ${['A','B','C','D'].map(l => `
            <div class="quiz-opt ${l === item.correct_answer ? 'opt-correct' : (l === item.chosen_answer ? 'opt-wrong' : '')}"
                 style="margin-bottom:.4rem;pointer-events:none">
              <strong>${l}.</strong> ${item[`option_${l.toLowerCase()}`]}
            </div>
          `).join('')}
        </div>
        ${item.explanation ? `
          <div style="background:var(--card2);border-radius:8px;padding:.75rem;font-size:.85rem;color:var(--text2)">
            <strong style="color:var(--blue)">💡 AI Explanation</strong><br/>${item.explanation}
          </div>
        ` : ''}
      </div>
    `).join('');

  } catch (err) {
    container.innerHTML = '<p style="color:var(--red)">Failed to load review.</p>';
  }
}


/* ============================================================
   PRACTICE TIMER (45s per question)
   ============================================================ */
function startTimer() {
  clearInterval(Quiz.timerInt);
  Quiz.timerInt = null;
  Quiz.timerSec = 45;

  const timerEl = document.getElementById('qtimer');
  if (timerEl) {
    timerEl.textContent = `⏱ 00:45`;
    timerEl.style.color = 'var(--yellow)';
  }

  Quiz.timerInt = setInterval(() => {
    if (Quiz.timerSec <= 0) {
      clearInterval(Quiz.timerInt);
      Quiz.timerInt = null;

      // Log as skipped
      fetch(`${API.BASE_URL}/quiz/submit`, {
        method: 'POST',
        headers: API.headers(),
        body: JSON.stringify({
          session_id:    Quiz.sessionId,
          question_id:   Quiz.questions[Quiz.currentIdx]?.id,
          chosen_answer: null,
          time_spent:    45
        })
      }).catch(() => {});

      Quiz.currentIdx++;
      loadQ(Quiz.currentIdx);
      if (!Quiz.isMock) startTimer();
      return;
    }

    Quiz.timerSec--;

    if (timerEl) {
      timerEl.textContent = `⏱ 00:${String(Quiz.timerSec).padStart(2, '0')}`;
      timerEl.style.color = Quiz.timerSec <= 10 ? 'var(--red)' : 'var(--yellow)';
    }
  }, 1000);
}


/* ============================================================
   SELECT MODE
   ============================================================ */
function selMode(el) {
  document.querySelectorAll('#m-setup .card-d2').forEach(c => {
    c.style.borderColor = 'var(--border)';
  });
  el.style.borderColor = 'var(--blue)';
  Quiz.selectedMode    = el.dataset.mode || 'practice';
}


/* ============================================================
   SUBJECTS
   ============================================================ */
async function loadSubjects() {
  try {
    const res  = await fetch(`${API.BASE_URL}/user/subjects`, { headers: API.headers() });
    const data = await res.json();
    if (!data.success) return;

    const subjects = data.subjects;

    ['science', 'arts', 'commercial'].forEach(stream => {
      const grid     = document.getElementById(`stream-${stream}`);
      if (!grid) return;

      const filtered = subjects.filter(s => s.stream === stream || s.stream === 'all');

      if (filtered.length === 0) {
        grid.innerHTML = '<div class="col-12"><p style="color:var(--text2);font-size:.85rem;text-align:center;padding:1rem 0">No subjects found.</p></div>';
        return;
      }

      grid.innerHTML = filtered.map(s => `
        <div class="col-6">
          <div class="subj-card" onclick="Quiz.selectedSubjectId=${s.id};modal('m-setup')">
            <div class="subj-icon">${s.icon || '📚'}</div>
            <div style="font-weight:600;font-size:.88rem">${s.name}</div>
            <div style="font-size:.72rem;color:var(--text2);margin-top:2px">Tap to practice</div>
          </div>
        </div>
      `).join('');
    });

    const subjList = document.getElementById('subj-list');
    if (subjList) {
      subjList.innerHTML = subjects.map(s => `
        <div class="col-6">
          <div class="subj-card" onclick="Quiz.selectedSubjectId=${s.id};modal('m-setup')">
            <div class="subj-icon">${s.icon || '📚'}</div>
            <div style="font-weight:600;font-size:.88rem">${s.name}</div>
            <div style="font-size:.72rem;color:var(--text2);margin-top:2px">${s.stream}</div>
          </div>
        </div>
      `).join('');
    }

  } catch (err) {
    ['stream-science', 'stream-arts', 'stream-commercial', 'subj-list'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.innerHTML = '<div class="col-12"><p style="color:var(--red);font-size:.85rem;text-align:center;padding:1rem 0">Failed to load subjects. Check your connection.</p></div>';
    });
  }
}


/* ============================================================
   ONBOARDING SLIDER
   ============================================================ */
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


/* ============================================================
   STREAM SWITCHER
   ============================================================ */
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


/* ============================================================
   FILTER BUTTONS
   ============================================================ */
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


/* ============================================================
   FAQ ACCORDION
   ============================================================ */
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


/* ============================================================
   CHARTS
   ============================================================ */
/* ============================================================
   PERFORMANCE SCREEN
   ============================================================ */
async function loadPerformance() {
  try {
    const res  = await fetch(`${API.BASE_URL}/performance`, { headers: API.headers() });
    const data = await res.json();
    if (!data.success) return;

    // Overall accuracy
const overallEl = document.getElementById('perf-overall-accuracy');
    if (overallEl) {
      overallEl.textContent = data.overall_accuracy !== null ? `${data.overall_accuracy}%` : '--';
      overallEl.style.color = data.overall_accuracy >= 70 ? 'var(--green)' : data.overall_accuracy >= 50 ? 'var(--yellow)' : 'var(--red)';
    }

    // Sub stats under accuracy card
const subStats = document.getElementById('perf-summary-text');
    if (subStats) subStats.textContent = data.total_answered > 0
      ? `${data.total_answered.toLocaleString()} questions answered across ${data.total_sessions} sessions`
      : 'Start practicing to see your stats';

    // Estimated score
    const estEl = document.getElementById('perf-est-score');
    if (estEl) estEl.textContent = data.estimated_score !== null ? `${data.estimated_score}/400` : '--';

    // Score trend chart
    buildScoreChart(data.score_trend);

    // Subject breakdown
    const subjEl = document.getElementById('perf-subjects');
    if (subjEl) {
      if (!data.subject_breakdown || data.subject_breakdown.length === 0) {
        subjEl.innerHTML = '<p style="color:var(--text2);font-size:.85rem;text-align:center;padding:1rem 0;margin:0">No data yet — start practicing!</p>';
      } else {
        subjEl.innerHTML = data.subject_breakdown.map(s => `
          <div style="margin-bottom:14px">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <div class="d-flex align-items-center gap-2">
                <span style="font-size:1.1rem">${s.subject_icon}</span>
                <span style="font-size:.85rem;font-weight:600">${s.subject_name}</span>
                ${s.status === 'weak' ? '<span style="background:var(--red-dim);color:var(--red);font-size:.65rem;border-radius:20px;padding:2px 7px;border:1px solid var(--red)">Weak</span>' : ''}
                ${s.status === 'strong' ? '<span style="background:var(--green-dim);color:var(--green);font-size:.65rem;border-radius:20px;padding:2px 7px;border:1px solid var(--green)">Strong</span>' : ''}
              </div>
              <span style="font-size:.85rem;font-weight:700;color:${s.accuracy >= 70 ? 'var(--green)' : s.accuracy >= 50 ? 'var(--yellow)' : 'var(--red)'}">${s.accuracy}%</span>
            </div>
            <div style="background:var(--card2);border-radius:20px;height:6px;overflow:hidden">
              <div style="height:100%;width:${s.accuracy}%;background:${s.accuracy >= 70 ? 'var(--green)' : s.accuracy >= 50 ? 'var(--yellow)' : 'var(--red)'};border-radius:20px;transition:width .6s ease"></div>
            </div>
            <div style="font-size:.72rem;color:var(--text2);margin-top:3px">${s.total_answered} questions · Last practiced ${s.last_practiced}</div>
          </div>
        `).join('');
      }
    }

    // Streak grid
    buildStreakGrid(data.streak_grid);

    // Streak count
  const streakCountEl = document.getElementById('perf-streak-label');
if (streakCountEl) streakCountEl.textContent = `${data.streak_days} day streak`;

const totalSessionsEl = document.getElementById('perf-total-sessions');
if (totalSessionsEl) totalSessionsEl.textContent = data.total_sessions ?? '--';

const estScoreEl = document.getElementById('perf-est-score');
if (estScoreEl) estScoreEl.textContent = data.estimated_score !== null ? data.estimated_score : '--';
  } catch (err) {
    // fails silently
  }
}

function buildCharts() {
  // Charts are now loaded via loadPerformance() when data arrives
  // This is kept for backward compat
}

function buildScoreChart(trendData) {
  const el = document.getElementById('sch');
  if (!el) return;
  el.removeAttribute('data-built'); // allow rebuild with fresh data

  // Use real data if available, fallback to placeholder
  const scores = trendData && trendData.length > 0
    ? trendData.map(t => t.score)
    : [0];

  const labels = trendData && trendData.length > 0
    ? trendData.map(t => t.label)
    : ['--'];

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
    <polyline points="${pts}" fill="none" stroke="#4f6ef7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>`;

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
    el.innerHTML = '';
    // Render empty grid
    for (let i = 0; i < 35; i++) {
      el.innerHTML += `<div style="width:14px;height:14px;border-radius:3px;background:var(--card2);border:1px solid var(--border)" title="No sessions"></div>`;
    }
    return;
  }

  const colors = ['var(--card2)', 'var(--blue-dim)', 'var(--blue)', '#4f6ef7'];
  el.innerHTML  = gridData.map(d =>
    `<div style="width:14px;height:14px;border-radius:3px;background:${colors[d.level]};border:1px solid var(--border)" title="${d.date}: ${d.sessions} session(s)"></div>`
  ).join('');
}


/* ============================================================
   REVIEW TOGGLE
   ============================================================ */
function rvtgl(id, btn) {
  const el       = document.getElementById(id);
  const isHidden = el.style.display === 'none';
  el.style.display = isHidden ? 'block' : 'none';
  btn.textContent  = isHidden ? 'Hide Explanation' : 'Show AI Explanation';
}


/* ============================================================
   CONFETTI
   ============================================================ */
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


/* ============================================================
   INIT
   ============================================================ */
document.addEventListener('DOMContentLoaded', async () => {
  const style = document.createElement('style');
  style.textContent = `
    @keyframes cfetti {
      0%   { transform: translate(0,0) scale(1); opacity: 1; }
      100% { transform: translate(var(--dx), var(--dy)) scale(0); opacity: 0; }
    }
  `;
  document.head.appendChild(style);

  const savedUser = API.user();
  if (savedUser && API.token()) {
    updateProfileUI(savedUser);
    await loadSubjects();
    await loadDashboard();
    go('s-dash');
  }
});