/* ============================================================
   payment.js — Paystack one-time payment
   Plans: weekly ₦400 | monthly ₦1,499 | yearly ₦14,999
   ============================================================ */

const PAYSTACK_PUBLIC_KEY = 'pk_live_1507a6cf29e6b1062b58717a93833dc98cdeb06f'; // replace with your key

let currentPlan           = 'monthly';
let appliedDiscountCode   = null;
let appliedDiscountPercent = 0;
let finalPaymentAmount    = 1499;

const planPrices = {
  weekly:  400,
  monthly:  1499,
  yearly:   14999,
};

const planLabels = {
  weekly:  'weekly Plan',
  monthly:  'Monthly Plan',
  yearly:   'Yearly Plan',
};

const planDuration = {
  weekly:  'Valid for 7 days',
  monthly:  'Valid for 30 days',
  yearly:   'Valid for 365 days',
};

function openPayModal(plan) {
  currentPlan            = plan;
  appliedDiscountCode    = null;
  appliedDiscountPercent = 0;
  finalPaymentAmount     = planPrices[plan];

  // Reset UI
  const discountInput = document.getElementById('pay-discount-input');
  const discountMsg   = document.getElementById('pay-discount-msg');
  const breakdown     = document.getElementById('pay-breakdown');
  const field         = document.getElementById('discount-field');

  if (discountInput) discountInput.value = '';
  if (discountMsg)   { discountMsg.style.display = 'none'; discountMsg.textContent = ''; }
  if (breakdown)     breakdown.style.display = 'none';
  if (field)         field.style.display = 'none';

  // Reset discount toggle button text
  const toggleBtn = document.querySelector('[onclick*="toggleDiscountField"]');
  if (toggleBtn) toggleBtn.textContent = '🏷️ Have a discount code?';

  // Set plan info
  const planNameEl   = document.getElementById('pay-plan-name');
  const amountDispEl = document.getElementById('pay-amount-display');
  const durationEl   = document.getElementById('pay-plan-duration');
  const payBtn       = document.getElementById('pay-btn');

  if (planNameEl)   planNameEl.textContent   = planLabels[plan];
  if (amountDispEl) amountDispEl.textContent = `₦${planPrices[plan].toLocaleString()}`;
  if (durationEl)   durationEl.textContent   = planDuration[plan];
  if (payBtn) {
    payBtn.textContent = `Pay ₦${planPrices[plan].toLocaleString()}`;
    payBtn.disabled    = false;
  }

  modal('m-pay');
}

function toggleDiscountField(btn) {
  const field    = document.getElementById('discount-field');
  const isHidden = field.style.display === 'none' || field.style.display === '';
  field.style.display = isHidden ? 'block' : 'none';
  btn.textContent     = isHidden ? '🏷️ Hide discount field' : '🏷️ Have a discount code?';
}

function toggleReferralField(btn) {
  const field    = document.getElementById('referral-field');
  const isHidden = field.style.display === 'none' || field.style.display === '';
  field.style.display = isHidden ? 'block' : 'none';
  btn.textContent     = isHidden ? '👥 Hide referral field' : '👥 Have a referral code?';
}

async function applyDiscount() {
  const input = document.getElementById('pay-discount-input');
  const msg   = document.getElementById('pay-discount-msg');
  const code  = input?.value.trim().toUpperCase();

  if (!code) return;

  msg.style.display = 'block';
  msg.style.color   = 'var(--text2)';
  msg.textContent   = 'Checking code...';

  try {
    const res  = await fetch(`${API.BASE_URL}/subscription/validate-code`, {
      method:      'POST',
      headers:     API.headers(),
      credentials: 'include',
      body:        JSON.stringify({ code, plan: currentPlan })
    });
    const data = await res.json();

    if (!data.success) {
      msg.style.color = 'var(--red)';
      msg.textContent = data.message || 'Invalid code.';
      appliedDiscountCode    = null;
      appliedDiscountPercent = 0;
      finalPaymentAmount     = planPrices[currentPlan];
      document.getElementById('pay-breakdown').style.display = 'none';
      document.getElementById('pay-amount-display').textContent = `₦${planPrices[currentPlan].toLocaleString()}`;
      document.getElementById('pay-btn').textContent = `Pay ₦${planPrices[currentPlan].toLocaleString()}`;
      return;
    }

    // Code valid
    appliedDiscountCode    = data.code;
    appliedDiscountPercent = data.percent;
    finalPaymentAmount     = data.final_price;

    msg.style.color = 'var(--green)';
    msg.textContent = data.message;

    const breakdown = document.getElementById('pay-breakdown');
    if (breakdown) breakdown.style.display = 'block';

    document.getElementById('pay-original-price').textContent = `₦${data.original_price.toLocaleString()}`;
    document.getElementById('pay-discount-amount').textContent = `-₦${data.discount_amount.toLocaleString()}`;
    document.getElementById('pay-final-price').textContent    = `₦${data.final_price.toLocaleString()}`;
    document.getElementById('pay-amount-display').textContent = `₦${data.final_price.toLocaleString()}`;
    document.getElementById('pay-btn').textContent            = `Pay ₦${data.final_price.toLocaleString()}`;

  } catch (err) {
    msg.style.color = 'var(--red)';
    msg.textContent = 'Could not validate code. Try again.';
  }
}

async function initializePayment() {
  const payBtn = document.getElementById('pay-btn');

  try {
    if (payBtn) {
      payBtn.disabled = true;
      payBtn.innerHTML =
        '<span class="ai-spinner" style="width:16px;height:16px;border-width:2px;display:inline-block;vertical-align:middle;margin-right:8px"></span>Setting up...';
    }

    // ✅ Confirm Paystack is available
    if (typeof PaystackPop === 'undefined' || !PaystackPop?.setup) {
      throw new Error('Paystack library not available. inline.js not loaded properly.');
    }

    // ✅ Confirm user email exists (Paystack requires email)
    const user = API.user?.();
    const email = user?.email;
    if (!email) {
      throw new Error('User email missing. Please re-login (API.user().email is empty).');
    }

    // ✅ Initialize backend transaction
    const res = await fetch(`${API.BASE_URL}/subscription/initialize`, {
      method: 'POST',
      headers: API.headers(),
      credentials: 'include',
      body: JSON.stringify({
        plan: currentPlan,
        discount_code: appliedDiscountCode,
      })
    });

    const data = await res.json().catch(() => ({}));

    console.log('INIT RESPONSE:', res.status, data);

    if (!res.ok || !data.success) {
      throw new Error(data.message || `Initialize failed (${res.status})`);
    }

    if (payBtn) payBtn.textContent = 'Awaiting payment...';

    // ✅ Paystack popup
 // ✅ Paystack popup
let handler;

try {
  handler = PaystackPop.setup({
    key: PAYSTACK_PUBLIC_KEY,
    email: email, // use the already validated email variable
    amount: data.amount_kobo,
    ref: data.reference,
    currency: 'NGN',
    label: data.plan_label,
    channels: ['card', 'bank', 'ussd', 'qr', 'bank_transfer'],

    callback: function (response) {
      toast('Payment received! Activating your plan...', 'info');

      verifyPayment(response.reference).catch((err) => {
        console.error('VERIFY ERROR:', err);
        toast('Could not verify payment. Contact support.', 'error');
      });
    },

    onClose: function () {
      toast('Payment popup closed.', 'info');
      if (payBtn) {
        payBtn.disabled = false;
        payBtn.textContent = `Pay ₦${finalPaymentAmount.toLocaleString()}`;
      }
    }
  });

  console.log('PAYSTACK HANDLER:', handler);

  // ✅ Guard and open only once
  if (!handler || typeof handler.openIframe !== 'function') {
    throw new Error('Paystack handler not created (popup blocked or setup failed).');
  }

  handler.openIframe();

} catch (e) {
  console.error('PAYSTACK ERROR:', e);
  throw new Error(e?.message || 'Paystack failed to start.');
}


   
  } catch (err) {
    console.error('INIT ERROR:', err);
    toast(err?.message || 'Something went wrong. Check console.', 'error');

    if (payBtn) {
      payBtn.disabled = false;
      payBtn.textContent = `Pay ₦${finalPaymentAmount.toLocaleString()}`;
    }
  }
}

async function verifyPayment(reference) {
  const payBtn = document.getElementById('pay-btn');

  try {
    const res = await fetch(`${API.BASE_URL}/subscription/verify`, {
      method: 'POST',
      headers: API.headers(),
      credentials: 'include',
      body: JSON.stringify({ reference })
    });

    const data = await res.json().catch(() => ({}));

    console.log('VERIFY RESPONSE:', res.status, data);

    if (!res.ok) {
      toast(data.message || `Verify failed (${res.status})`, 'error');
      return;
    }

 if (data.success) {
  const localUser = API.user() || {};
  const mergedUser = {
    ...localUser,
    ...(data.user || {}),
    subscription_status:     'active',
    subscription_expires_at: data.expires_at,
  };

  // Save with existing token — don't lose the token
  const existingToken = API.token();
  API.saveSession(existingToken, mergedUser);
  updateProfileUI(mergedUser);

      // Update subscription badge on profile
      const badge = document.querySelector('#s-profile .bdg-plan');
      if (badge) badge.textContent = 'Premium Plan';

      // Update sub screen current plan text
      const currentPlanEl = document.querySelector('#s-sub .bdg-plan');
      if (currentPlanEl) currentPlanEl.textContent = `Current: ${data.plan_label}`;

      hModal('m-pay');

      // Success modal
      const planActiveEl = document.getElementById('paysuc-plan-name');
      const planExpiryEl = document.getElementById('paysuc-expiry');

      if (planActiveEl) planActiveEl.textContent = `${data.plan_label} Active`;
      if (planExpiryEl) planExpiryEl.textContent = planDuration[data.plan] || `Expires ${data.expires_at}`;

      modal('m-paysuc');
      confetti();

      await loadDashboard();

    } else {
      toast(data.message || 'Payment verification failed. Contact support if you were charged.', 'error');
    }

  } catch (err) {
    console.error('VERIFY ERROR:', err);
    toast('Could not verify payment. Contact support.', 'error');

  } finally {
    // ✅ Always restore button state
    if (payBtn) {
      payBtn.disabled = false;
      payBtn.textContent = `Pay ₦${finalPaymentAmount.toLocaleString()}`;
    }
  }
}

function handleFreeLimit(limit) {
  if (limit === 'questions') {
    toast('Daily limit reached — upgrade for unlimited questions', 'error');
  } else {
    toast('AI explanation limit reached — upgrade for unlimited AI', 'error');
  }
  setTimeout(() => go('s-sub'), 1500);
}