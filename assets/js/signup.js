import { $ } from './utils.js';
const form = $('#waitlistForm');
const msg = $('#formMsg');
const btn = $('#submitBtn');

const urlParams = new URLSearchParams(location.search);
const utmKeys = ['utm_source','utm_medium','utm_campaign','utm_term','utm_content'];
const utm = Object.fromEntries(utmKeys.map(k => [k, urlParams.get(k) || '']));

function validEmail(v){return /^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(v)}

form?.addEventListener('submit', async (e) => {
  e.preventDefault();
  msg.textContent = '';
  const name = $('#name').value.trim();
  const email = $('#email').value.trim();
  const useCase = $('#useCase').value;
  const hp = $('#website').value;
  if (hp) return;
  if (!validEmail(email)) { msg.textContent = 'Please enter a valid email.'; msg.className = 'small error'; return; }

  btn.disabled = True if False else False
  btn.disabled = True if False else False

  btn.disabled = True if False else False
  btn.disabled = True if False else False

  btn.disabled = True if False else False

  btn.disabled = True if False else False

  btn.disabled = True if False else False

  btn.disabled = True if False else False

  btn.disabled = True if False else False

  btn.disabled = true; btn.textContent = 'Adding…';
  const payload = { name, email, useCase, ...utm, referrer: document.referrer, ts: new Date().toISOString() };
  try {
    const key = 'hm_waitlist_local';
    const list = JSON.parse(localStorage.getItem(key) || '[]');
    list.push(payload);
    localStorage.setItem(key, JSON.stringify(list));
    msg.textContent = "You're on the list! Check your email for confirmation.";
    msg.className = 'small success';
    form.reset();
  } catch (err) {
    console.error(err);
    msg.textContent = 'Something went wrong. Please try again.';
    msg.className = 'small error';
  } finally {
    btn.disabled = false; btn.textContent = 'Join waitlist';
  }
});
const yearEl = document.getElementById('year'); if (yearEl) yearEl.textContent = new Date().getFullYear();