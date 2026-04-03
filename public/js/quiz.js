/* ============================================================
   quiz.js — Quiz engine: state, start, load, answer, timers, finish, review
   ============================================================ */

const Quiz = {
  sessionId:         null,
  finishedSessionId: null,     // <-- For review mode
  questions:         [],
  answers:           {},
  currentIdx:        0,
  answered:          0,
  startedAt:         null,
  timerInt:          null,     
  mockTimerInt:      null,     
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
    this.finishedSessionId = null;
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
   START QUIZ
   ============================================================ */
async function startQuiz(subjectId, mode = 'practice', total = 20, topicId = null) {
  Quiz.reset();
  Quiz.selectedSubjectId = subjectId;
  Quiz.selectedMode      = mode;

  go('s-quiz');
  document.getElementById('qtext').textContent = 'Loading questions...';

  try {
    const body = { subject_id: subjectId, mode, exam_type: 'JAMB', total_questions: total };
    if (topicId) body.topic_id = topicId;

    const res = await API.fetch(`${API.BASE_URL}/quiz/start`, {
      method:  'POST',
      body:    JSON.stringify(body)
    });
    if (!res) return;

    const data = await res.json();

    if (!data.success || !data.questions || data.questions.length === 0) {
      toast('No questions found for this selection. Try another.', 'error');
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
  window.scrollTo({ top: 0, behavior: 'smooth' });

  if (Quiz.questions.length > 0 && index >= Quiz.questions.length) {
    finishQuiz();
    return;
  }
  
  if (Quiz.questions.length === 0) return;

  const q = Quiz.questions[index];

  const curEl = document.getElementById('qcur');
  if (curEl) curEl.textContent = index + 1;

  const progEl = document.getElementById('qprog');
  if (progEl) progEl.style.width = `${((index + 1) / Quiz.questions.length) * 100}%`;

  const subjEl = document.getElementById('qsubj');
  if (subjEl) subjEl.textContent = `${q.exam_type} ${q.year}`;

  document.getElementById('qtext').innerHTML = q.question_text;

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

  const nextBtn = document.getElementById('nextbtn');
  if (nextBtn) {
    nextBtn.style.display = 'none';
    nextBtn.disabled      = false;
    const isLast          = index === Quiz.questions.length - 1;
    nextBtn.textContent   = isLast ? 'Submit Quiz ✓' : 'Next Question →';

    nextBtn.onclick = isLast
      ? () => {
          nextBtn.disabled  = true;
          nextBtn.innerHTML =
            '<span class="ai-spinner" style="width:16px;height:16px;border-width:2px;display:inline-block;vertical-align:middle;margin-right:8px"></span>Submitting...';
          finishQuiz();
        }
      : () => nxtQ();
  }

  const expBox = document.getElementById('m-aiexp-text');
  if (expBox) expBox.textContent = '';
}



/* ============================================================
   ANSWER
   ============================================================ */
async function ans(el, chosen, questionId) {

  if (!Quiz.sessionId) return;   // <-- prevents ghost submits

  ['A', 'B', 'C', 'D'].forEach(l => {
    const o = document.getElementById(`o${l}`);
    if (o) o.onclick = null;
  });

  el.classList.add('opt-selected');

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
      toast('Wrong — correct answer highlighted', 'error');
      setTimeout(() => nxtQ(), 1200);
    } else {
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
fetchExplanation(questionId);
      const nextBtn = document.getElementById('nextbtn');
      if (nextBtn) nextBtn.style.display = 'block';
    }
  }

  Quiz.answered++;

  if (!Quiz.sessionId) return; // <-- guard

  fetch(`${API.BASE_URL}/quiz/submit`, {
    method:  'POST',
    headers: API.headers(),
    body:    JSON.stringify({
      session_id:    Quiz.sessionId,
      question_id:   questionId,
      chosen_answer: chosen,
      time_spent:    timeSpent
    })
  }).catch(() => {});
}



/* ============================================================
   AI EXPLANATION
   ============================================================ */
async function fetchExplanation(questionId) {
  try {
    const res  = await API.fetch(`${API.BASE_URL}/quiz/explanation/${questionId}/stream`);
if (!res) return;

    const data = await res.json();

    // re-query the DOM every time (modal re-renders)
    const expBox = document.getElementById('m-aiexp-text');

    if (!expBox) return;

    if (!data || !data.explanation) {
      expBox.innerHTML = 'Explanation not available for this question yet.';
      return;
    }

    // No typewriter, just direct insert
    expBox.innerHTML = data.explanation;

  } catch (err) {
    const expBox = document.getElementById('m-aiexp-text');
    if (expBox) expBox.innerHTML = 'Could not load explanation. Check your connection.';
  }
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

    if (!Quiz.sessionId) { 
      clearInterval(Quiz.timerInt);
      return;
    }

    if (Quiz.timerSec <= 0) {
      clearInterval(Quiz.timerInt);
      Quiz.timerInt = null;

      if (!Quiz.sessionId) return;

      fetch(`${API.BASE_URL}/quiz/submit`, {
        method:  'POST',
        headers: API.headers(),
        body:    JSON.stringify({
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
   FINISH QUIZ
   ============================================================ */
async function finishQuiz() {
  if (!Quiz.sessionId) return;

  const sessionId = Quiz.sessionId;

  Quiz.finishedSessionId = sessionId;  // <-- keep for review
  Quiz.sessionId         = null;       // <-- disable all submits
  
  clearInterval(Quiz.timerInt);
  clearInterval(Quiz.mockTimerInt);

  Quiz.timerInt     = null;
  Quiz.mockTimerInt = null;

  const timeTaken = Math.round((Date.now() - Quiz.startedAt) / 1000);

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
    const res = await API.fetch(`${API.BASE_URL}/quiz/session/${sessionId}/finish`, {
      method:  'POST',
      body:    JSON.stringify({ time_taken: timeTaken })
    });
    if (!res) return;

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
        if (subtext) subtext.textContent =
          data.score_percentage >= 80 ? 'You\'re mastering this subject. Keep the streak alive!' :
          data.score_percentage >= 60 ? 'Good session. Review wrong answers to push higher.' :
          'Review your wrong answers — the AI explanations will help you understand.';

        if (reviewBtn) reviewBtn.style.display = 'block';
        if (weakBtn)   weakBtn.style.display   = data.score_percentage < 70 ? 'block' : 'none';
      }

      document.getElementById('submit-overlay')?.remove();
      Quiz.isMock = false;
      go('s-res');
      if (data.score_percentage >= 70) confetti();
    }

  } catch (err) {
    document.getElementById('submit-overlay')?.remove();
    toast('Could not save results. Try again.', 'error');
  }
}



/* ============================================================
   REVIEW SESSION — Now Works 100%
   ============================================================ */
async function reviewSession() {

  const sid = Quiz.finishedSessionId;   // <-- Use stored final ID
  if (!sid) {
    toast('No session to review.', 'error');
    return;
  }

  go('s-review');

  const container = document.getElementById('review-list');
  if (!container) return;
  
  container.innerHTML = '<p style="color:var(--text2)">Loading review...</p>';

  try {
    const res  = await API.fetch(`${API.BASE_URL}/quiz/session/${sid}/review`);
    if (!res) return;
    
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