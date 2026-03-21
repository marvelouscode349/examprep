/* ============================================================
   subjects.js — Load and render subjects
   ============================================================ */

async function loadSubjects() {
  try {
    const res  = await fetch(`${API.BASE_URL}/user/subjects`, { headers: API.headers() });
    const data = await res.json();
    if (!data.success) return;

    const subjects = data.subjects;

    // Dashboard stream grids
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
          <div class="subj-card"
               data-sid="${s.id}"
               data-sname="${s.name.replace(/"/g, '&quot;')}"
               onclick="loadTopics(parseInt(this.dataset.sid), this.dataset.sname)">
            <div class="subj-icon">${s.icon || '📚'}</div>
            <div style="font-weight:600;font-size:.88rem">${s.name}</div>
            <div style="font-size:.72rem;color:var(--text2);margin-top:2px">Tap to study</div>
          </div>
        </div>
      `).join('');
    });

    // Subject select screen
    const subjList = document.getElementById('subj-list');
    if (subjList) {
      subjList.innerHTML = subjects.map(s => `
        <div class="col-6">
          <div class="subj-card"
               data-sid="${s.id}"
               data-sname="${s.name.replace(/"/g, '&quot;')}"
               onclick="loadTopics(parseInt(this.dataset.sid), this.dataset.sname)">
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