import { $, $$, el } from './utils.js';
import { categories, personas } from './data.js';

const pillbar = $('#pillbar');
let activeCat = 'all';
function renderPills() {
  pillbar.innerHTML = '';
  categories.forEach(cat => {
    const p = el('button', { class: 'pill', role: 'tab', 'aria-selected': String(cat.id === activeCat), 'data-active': String(cat.id === activeCat), 'data-id': cat.id });
    p.append(el('span', {}, cat.label));
    if (cat.soon) p.append(el('span', { class: 'badge', title: 'Coming soon' }, 'Soon'));
    p.addEventListener('click', () => { activeCat = cat.id; renderPills(); renderCards(); });
    pillbar.append(p);
  });
}

const cards = $('#cards');
function personaDetails(p) {
  if (p.details) return p.details;
  const cat = categories.find(c => c.id === p.category)?.label || p.category;
  return `<div class="subtle">Category: <strong>${cat}</strong></div>
          <p>${p.desc}</p>
          <ul>${(p.tags||[]).map(t => `<li>• ${t}</li>`).join('')}</ul>
          <p class="subtle">This is a placeholder. Connect this card to your backend to launch a full chat experience.</p>`;
}
function renderCards() {
  const q = $('#searchInput').value.trim().toLowerCase();
  const list = personas.filter(p => {
    const matchesCat = activeCat === 'all' || p.category === activeCat;
    const matchesQ = !q || p.title.toLowerCase().includes(q) || p.desc.toLowerCase().includes(q) || (p.tags||[]).join(' ').toLowerCase().includes(q);
    return matchesCat && matchesQ;
  });

  cards.innerHTML = '';
  list.forEach(p => {
    const soonRibbon = p.soon ? el('div', { class: 'soon' }, 'Coming soon') : null;
    const title = el('h3', {}, p.title);
    const meta = el('div', { class: 'meta' }, [ el('small', {}, p.category.replace('-', ' ')) ]);
    const id = el('div', { class: 'id' }, [ el('div', { class: 'avatar' }, p.initials), el('div', {}, [title, meta]) ]);

    const desc = el('p', {}, p.desc);
    const tags = el('div', { class: 'chips' }, (p.tags||[]).map(t => el('span', { class: 'chip' }, t)));

    const btns = el('div', { class: 'actions' });
    if (p.actions.includes('try')) btns.append(el('a', { class: 'btn btn-primary', href: `pages/${p.id}-signup.html` }, 'Try now'));
    if (p.actions.includes('notify')) btns.append(el('button', { class: 'btn btn-outline', onclick: () => openInfo(p.title, `Join the waitlist to get notified when <strong>${p.title}</strong> goes live.`) }, 'Notify me'));
    btns.append(el('button', { class: 'btn btn-ghost', onclick: () => openInfo(p.title, personaDetails(p)) }, 'Details'));

    const card = el('article', { class: 'card col-4' }, [ soonRibbon, el('div', { class: 'top' }, [id, p.status ? el('span', { class: 'chip' }, p.status) : null ]), desc, tags, btns ]);
    cards.append(card);
  });

  if (!list.length) {
    cards.append(el('div', { class: 'subtle', style: 'grid-column:1/-1; padding:20px; border:1px dashed var(--border); border-radius:12px; text-align:center' }, 'No personas match your filters.'));
  }
}

$('#searchInput')?.addEventListener('input', renderCards);
$('#clearSearch')?.addEventListener('click', () => { $('#searchInput').value = ''; renderCards(); });

const demoModal = $('#demoModal');
const demoTitle = $('#demoTitle');
const demoChat = $('#demoChat');
const demoInput = $('#demoInput');
const demoSend = $('#demoSend');

let activeDemo = null;
function addDemoLine(who, text) {
  const bubble = el('div', { style: `justify-self:${who==='bot'?'start':'end'}; max-width:78%` }, [
    el('div', { style: `padding:10px 12px; border:1px solid var(--border); border-radius:12px; background:${who==='bot'?'rgba(255,255,255,.05)':'rgba(122,107,255,.25)'};` }, text)
  ]);
  demoChat.append(bubble);
  demoChat.scrollTop = demoChat.scrollHeight;
}
function openDemo(id) {
  activeDemo = personas.find(p => p.id === id);
  demoTitle.textContent = `${activeDemo.title} — demo`;
  demoChat.innerHTML = '';
  addDemoLine('bot', `Hi! I’m ${activeDemo.title}. ${activeDemo.category === 'html' ? 'Paste HTML and I’ll check semantics & accessibility.' : activeDemo.category === 'css' ? 'Describe a layout and I’ll sketch the CSS.' : 'Paste an error message to get a fix with explanation.'}`);
  demoModal.showModal();
  demoInput.focus();
}
window.openDemo = openDemo;

demoSend?.addEventListener('click', () => {
  const val = demoInput.value.trim();
  if (!val) return; demoInput.value = '';
  addDemoLine('user', val);
  setTimeout(() => addDemoLine('bot', '⏳ (Placeholder) An actual response would appear here once connected to your API.'), 500);
});

$$('dialog [data-close]').forEach(btn => btn.addEventListener('click', e => e.target.closest('dialog').close()));

const infoModal = $('#infoModal');
const infoTitle = $('#infoTitle');
const infoBody = $('#infoBody');
function openInfo(title, html) {
  infoTitle.textContent = title;
  infoBody.innerHTML = html;
  infoModal.showModal();
}
window.openInfo = openInfo;

  const buyModal = $('#buyModal');
  $$('.price-card [data-buy]').forEach(btn => btn.addEventListener('click', () => {
    const plan = btn.getAttribute('data-buy');
    $('#buyPlan').value = plan;
    buyModal.showModal();
  }));
  $('#buyNow')?.addEventListener('click', () => {
    const plan = $('#buyPlan').value; const email = $('#buyEmail').value.trim();
    if (!email || !/^[^\@\s]+@[^\@\s]+\.[^\@\s]+$/.test(email)) { alert('Please enter a valid email'); return; }
    alert(`(Placeholder) Proceeding to checkout for ${plan.toUpperCase()}. Connect to Stripe later.`);
    buyModal.close();
  });

document.querySelectorAll('[data-details]').forEach(b => b.addEventListener('click', () => {
  const id = b.getAttribute('data-details');
  const p = personas.find(x => x.id === id);
  openInfo(p.title, personaDetails(p));
}));

$('#openSignIn')?.addEventListener('click', () => $('#signinModal').showModal());
$('#siGo')?.addEventListener('click', () => { alert('Signed in (placeholder). Hook up real auth.'); $('#signinModal').close(); });

const yearEl = document.getElementById('year'); if (yearEl) yearEl.textContent = new Date().getFullYear();

  if (pillbar && cards) {
    renderPills();
    renderCards();
  }
window.addEventListener('keydown', (e) => { if (e.key === 'Escape') document.querySelectorAll('dialog').forEach(d => d.open && d.close()); });