/* ============================================================
   auth.js — Register, login, logout, profile UI
   ============================================================ */

function updateProfileUI(user) {
  const firstName = user.name.split(' ')[0];

  const greetEl = document.querySelector('#s-dash h2');
  if (greetEl) greetEl.textContent = `Good morning, ${firstName}`;

  const profileName = document.querySelector('#s-profile h3');
  if (profileName) profileName.textContent = user.name;

  const profileCard = document.querySelector('#s-profile .card-d');
  if (profileCard) {
    profileCard.querySelectorAll('.d-flex').forEach(row => {
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
  const referral = document.getElementById('reg-referral')?.value?.trim() || null;

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
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify({ name, email, phone: phone || null, password, target_exam: exam, stream, exam_year: year, state, referral_code: referral })
    });

    const data = await res.json();

   if (data.success) {
  toast('Account created! Please login to continue.', 'success');
  go('s-login');
} else {
      showRegError(data.errors
        ? Object.values(data.errors)[0][0]
        : data.message || 'Registration failed. Try again.');
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
  box.textContent   = msg;
  box.style.display = 'block';
  box.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

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
      method:  'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body:    JSON.stringify({ email, password })
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

async function handleLogout() {
  try {
    await fetch(`${API.BASE_URL}/auth/logout`, {
      method:  'POST',
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
