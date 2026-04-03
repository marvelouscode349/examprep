/* ============================================================
   topics.js — Topic list, lesson notes, practice by topic
   ============================================================ */

let currentTopicId   = null;
let currentSubjectId = null;
let currentTopicName = '';

async function loadTopics(subjectId, subjectName) {
  subjectId = parseInt(subjectId); // always ensure integer

  if (!subjectId || isNaN(subjectId)) {
    console.error('loadTopics called with invalid subjectId:', subjectId);
    return;
  }

  currentSubjectId       = subjectId;
  Quiz.selectedSubjectId = subjectId;

  const nameEl = document.getElementById('topic-subj-name');
  if (nameEl) nameEl.textContent = subjectName;

  go('s-topic');

  const container = document.getElementById('topic-list');
  if (!container) {
    console.error('topic-list element missing from s-topic screen');
    return;
  }

  container.innerHTML = `
    <div class="text-center py-4">
      <div class="ai-spinner" style="margin:0 auto 12px"></div>
      <p style="color:var(--text2);font-size:.85rem">Loading topics...</p>
    </div>`;

  try {
    const res  = await API.fetch(`${API.BASE_URL}/subjects/${subjectId}/topics`);
    if (!res) return;

    const data = await res.json();

    if (!data.success || data.topics.length === 0) {
      container.innerHTML = `
        <div class="card-d2 text-center py-4 mb-3">
          <p style="color:var(--text2);font-size:.85rem;margin:0">No topics yet for this subject.</p>
        </div>
        <button class="btn-p" onclick="Quiz.selectedSubjectId=${subjectId};modal('m-setup')">
          Practice All Questions
        </button>`;
      return;
    }

    const practiced = data.topics.filter(t => t.accuracy !== null).length;
    const weak      = data.topics.filter(t => t.accuracy !== null && t.accuracy < 50).length;

    container.innerHTML = `
      <div class="row g-2 mb-3">
        <div class="col-4">
          <div class="stat-card text-center">
            <div class="fd" style="font-size:1.2rem;font-weight:800">${data.topics.length}</div>
            <div style="font-size:.7rem;color:var(--text2)">Topics</div>
          </div>
        </div>
        <div class="col-4">
          <div class="stat-card text-center">
            <div class="fd" style="font-size:1.2rem;font-weight:800">${practiced}</div>
            <div style="font-size:.7rem;color:var(--text2)">Practiced</div>
          </div>
        </div>
        <div class="col-4">
          <div class="stat-card text-center">
            <div class="fd" style="font-size:1.2rem;font-weight:800;color:var(--red)">${weak}</div>
            <div style="font-size:.7rem;color:var(--text2)">Weak</div>
          </div>
        </div>
      </div>

      <button class="btn-o mb-3 w-100"
        onclick="Quiz.selectedSubjectId=${subjectId};Quiz.selectedMode='practice';modal('m-setup')">
        Practice All Topics
      </button>

      ${data.topics.map(t => `
        <div class="card-d2 mb-2" style="cursor:pointer"
             data-tid="${t.id}"
             data-sid="${subjectId}"
             data-tname="${t.name.replace(/"/g, '&quot;')}"
             onclick="openTopic(parseInt(this.dataset.tid), parseInt(this.dataset.sid), this.dataset.tname)">
          <div class="d-flex justify-content-between align-items-center">
            <div style="flex:1;min-width:0;padding-right:12px">
              <div style="font-weight:600;font-size:.88rem;margin-bottom:3px">${t.name}</div>
              <div style="font-size:.72rem;color:var(--text2)">
                ${t.question_count} questions
                ${t.has_notes ? ' · <span style="color:var(--green)">Notes ready</span>' : ''}
              </div>
            </div>
            <div style="text-align:right;flex-shrink:0">
              ${t.accuracy !== null
                ? `<div style="font-weight:700;font-size:.9rem;color:${
                    t.accuracy >= 70 ? 'var(--green)' :
                    t.accuracy >= 50 ? 'var(--yellow)' : 'var(--red)'
                  }">${t.accuracy}%</div>
                  <div style="font-size:.68rem;color:var(--text2)">
                    ${t.accuracy >= 70 ? '✅ Strong' : t.accuracy >= 50 ? '📈 Average' : '⚠️ Weak'}
                  </div>`
                : `<div style="font-size:.75rem;color:var(--text2)">Not started</div>`
              }
            </div>
          </div>
          ${t.accuracy !== null ? `
            <div style="background:var(--card2);border-radius:20px;height:4px;overflow:hidden;margin-top:8px">
              <div style="height:100%;width:${t.accuracy}%;background:${
                t.accuracy >= 70 ? 'var(--green)' :
                t.accuracy >= 50 ? 'var(--yellow)' : 'var(--red)'
              };border-radius:20px;transition:width .6s ease"></div>
            </div>` : ''}
        </div>
      `).join('')}
    `;

  } catch (err) {
    container.innerHTML = '<p style="color:var(--red);font-size:.85rem">Failed to load topics.</p>';
  }
}


async function openTopic(topicId, subjectId, topicName) {
  topicId   = parseInt(topicId);
  subjectId = parseInt(subjectId);

  currentTopicId   = topicId;
  currentSubjectId = subjectId;
  currentTopicName = topicName;

  const titleEl   = document.getElementById('notes-title');
  const actionsEl = document.getElementById('notes-actions');
  const contentEl = document.getElementById('notes-content');

  if (titleEl)   titleEl.textContent    = topicName;
  if (actionsEl) actionsEl.style.display = 'none';
  if (contentEl) contentEl.innerHTML    = `
    <div class="text-center py-4">
      <div class="ai-spinner" style="margin:0 auto 12px"></div>
      <p style="color:var(--text2);font-size:.85rem">Loading notes...</p>
    </div>`;

  go('s-notes');

  try {
    const res  = await API.fetch(`${API.BASE_URL}/topics/${topicId}/notes`);
    if (!res) return;
    
    const data = await res.json();

    if (!data.success && data.message === 'premium_required') {
      if (contentEl) contentEl.innerHTML = `
        <div class="text-center py-4">
          <div style="font-size:3rem;margin-bottom:12px">⭐</div>
          <h5 class="fd mb-2">Premium Feature</h5>
          <p class="text-m" style="font-size:.88rem">
            Lesson notes are available on Premium.<br>
            Upgrade to unlock AI-generated notes for all topics.
          </p>
          <button class="btn-p mt-3" onclick="go('s-sub')">Upgrade to Premium</button>
        </div>`;

      if (actionsEl) {
        actionsEl.style.display = 'block';
        actionsEl.innerHTML = `
          <button class="btn-o mt-4 mb-2"
            onclick="startTopicQuiz(${topicId}, ${subjectId})">
            Practice Without Notes →
          </button>
          <button class="btn-o" onclick="go('s-topic')">← Back to Topics</button>`;
      }
      return;
    }

    if (!data.success || !data.notes) {
      if (contentEl) contentEl.innerHTML = '<p style="color:var(--red)">Could not load notes. Try again.</p>';
      return;
    }

   // Convert markdown to HTML
let html = marked.parse(data.notes || '');

// Optional: sanitize if you allow user-generated content anywhere
// Use DOMPurify if needed:
// <script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.7/dist/purify.min.js"></script>
// html = DOMPurify.sanitize(html);

// Apply your custom headers/bullets styles after markdown conversion
const formatted = html
  .replace(/📌\s*SUMMARY/gi,               '<div class="notes-header">📌 Summary</div>')
  .replace(/🔑\s*KEY POINTS/gi,            '<div class="notes-header">🔑 Key Points</div>')
  .replace(/⚡\s*JAMB\/WAEC EXAM TRICKS/gi, '<div class="notes-header">⚡ Exam Tricks</div>')
  .replace(/⚡\s*EXAM TRICKS/gi,            '<div class="notes-header">⚡ Exam Tricks</div>')
  // Turn <li> into your custom bullet style if you prefer:
  .replace(/<li>(.*?)<\/li>/g, '<div class="notes-bullet">• $1</div>');

if (contentEl) contentEl.innerHTML = `
  <div class="card-d mb-3" style="line-height:1.8;font-size:.88rem">
    ${formatted}
  </div>`;

    if (actionsEl) {
      actionsEl.style.display = 'block';
      actionsEl.innerHTML = `
        <button class="btn-p mb-2" onclick="startTopicQuiz(${topicId}, ${subjectId})">
          Practice This Topic →
        </button>
        <button class="btn-o" onclick="go('s-topic')">← Back to Topics</button>`;
    }

  } catch (err) {
    if (contentEl) contentEl.innerHTML = '<p style="color:var(--red)">Failed to load notes. Check your connection.</p>';
  }
}


async function startTopicQuiz(topicId, subjectId) {
  topicId   = parseInt(topicId);
  subjectId = parseInt(subjectId);
  await startQuiz(subjectId, 'topic', 10, topicId);
}