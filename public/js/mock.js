/* ============================================================
   mock.js — Mock exam, mock timer, weak areas
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
      method:  'POST',
      headers: API.headers(),
      body:    JSON.stringify({ subject_id: subjectId, mode: 'mock', exam_type: 'JAMB', total_questions: 60 })
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
      toast('⏰ Time is up! Submit your quiz now.', 'error');

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

async function loadWeakAreas(subjectId) {
  Quiz.selectedSubjectId = subjectId;
  Quiz.selectedMode      = 'weak_areas';
  Quiz.isMock            = false;
  await startQuiz(subjectId, 'weak_areas', 20);
}
