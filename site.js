const data = JSON.parse(document.getElementById('profile-data').textContent);
const $ = (selector, root = document) => root.querySelector(selector);
const $$ = (selector, root = document) => [...root.querySelectorAll(selector)];
const reducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
const sleep = (ms) => new Promise((resolve) => setTimeout(resolve, reducedMotion ? 0 : ms));

const store = {
  get(key) { try { return localStorage.getItem(key); } catch { return null; } },
  set(key, value) {
    try { value === null ? localStorage.removeItem(key) : localStorage.setItem(key, value); } catch { /* private mode */ }
  },
};

/* Theme */
const themes = ['auto', 'light', 'dark'];
function applyTheme(theme) {
  if (theme === 'auto') delete document.documentElement.dataset.theme;
  else document.documentElement.dataset.theme = theme;
  $$('[data-theme-set]').forEach((b) => b.setAttribute('aria-checked', String(b.dataset.themeSet === theme)));
  store.set('theme', theme === 'auto' ? null : theme);
}
const currentTheme = () => document.documentElement.dataset.theme ?? 'auto';
applyTheme(themes.includes(store.get('theme')) ? store.get('theme') : 'auto');
$$('[data-theme-set]').forEach((b) => b.addEventListener('click', () => applyTheme(b.dataset.themeSet)));

/* Drawing index follows the scroll */
const links = $$('.index a');
const spy = new IntersectionObserver((entries) => {
  for (const entry of entries) {
    if (entry.isIntersecting) links.forEach((a) => a.classList.toggle('on', a.hash === `#${entry.target.id}`));
  }
}, { rootMargin: '-45% 0px -50% 0px' });
$$('main > section').forEach((s) => spy.observe(s));

/* Drafting crosshair with CAD-style coordinates */
const cross = $('.cross');
const coords = $('.coords');
if (matchMedia('(pointer: fine)').matches && !reducedMotion) {
  const pad = (n) => String(Math.max(0, Math.round(n))).padStart(4, '0');
  addEventListener('pointermove', (e) => {
    cross.style.setProperty('--x', `${e.clientX}px`);
    cross.style.setProperty('--y', `${e.clientY}px`);
    cross.classList.add('on');
    coords.textContent = `X ${pad(e.pageX)} Y ${pad(e.pageY)}`;
  }, { passive: true });
  document.documentElement.addEventListener('pointerleave', () => cross.classList.remove('on'));
}

/* Spec cards for the crew and the crane */
const card = $('[data-card]');
let cardOrigin = null;
function openCard(key, origin) {
  const isCrane = key === 'crane';
  const agent = data.agents[key];
  const tag = $('[data-card-tag]', card);
  tag.textContent = isCrane ? '⚙' : key;
  tag.classList.toggle('human', Boolean(agent?.human));
  $('[data-card-name]', card).textContent = isCrane ? 'The crane' : agent.name;
  $('[data-card-role]', card).textContent = isCrane ? 'The crew’s tooling' : agent.role;
  $('[data-card-about]', card).textContent = isCrane
    ? 'Everything the crew builds with. I sit in the cab: the tools do the lifting, I decide where it goes.'
    : agent.about;
  $('[data-card-list-title]', card).textContent = isCrane ? 'In the toolbox' : 'Works at';
  const list = $('[data-card-list]', card);
  list.replaceChildren(...(isCrane
    ? [...data.crane.lines, data.crane.toolbox].map((line) => item(line, ''))
    : agent.steps.map((s) => item(`${s.stage}: ${s.label}`, s.skills))));
  card.hidden = false;
  $$('[data-agent]').forEach((el) => el.classList.toggle('sel', el.dataset.agent === key));
  cardOrigin = origin;
  $('[data-close]', card).focus({ preventScroll: true });
}
function item(text, aside) {
  const li = document.createElement('li');
  const a = document.createElement('span');
  const b = document.createElement('span');
  a.textContent = text;
  b.textContent = aside;
  li.append(a, b);
  return li;
}
function closeCard() {
  if (card.hidden) return;
  card.hidden = true;
  $$('[data-agent].sel').forEach((el) => el.classList.remove('sel'));
  cardOrigin?.focus?.({ preventScroll: true });
}
$('[data-close]', card).addEventListener('click', closeCard);

/* Rules on the hero's red note */
let ruleIndex = -1;
function nextRule() {
  ruleIndex = (ruleIndex + 1) % data.rules.length;
  $$('[data-rule]').forEach((el) => { el.textContent = `rule ${ruleIndex + 1} of ${data.rules.length}: ${data.rules[ruleIndex]}`; });
}

document.addEventListener('click', (e) => {
  const agent = e.target.closest('[data-agent]');
  if (agent) { openCard(agent.dataset.agent, agent); return; }
  if (e.target.closest('[data-rule]')) { nextRule(); return; }
  if (!card.hidden && !card.contains(e.target)) closeCard();
});

/* Highlighting lanes on the crew sheet and floors on the stack sheet */
function highlight(sheet, attribute, value) {
  sheet.classList.toggle('dim', value !== null);
  $$(`[data-${attribute}]${attribute === 'lane' ? ',[data-lanes]' : ''}`, sheet).forEach((el) => {
    const on = value !== null && (el.dataset[attribute] === value || (el.dataset.lanes ?? '').split(' ').includes(value));
    el.classList.toggle('on', on);
  });
}
function trace(e) {
  const sheet = e.target.closest?.('.sheet');
  if (!sheet) return;
  if (sheet.dataset.sheet === 'crew') highlight(sheet, 'lane', e.target.closest('[data-lane]')?.dataset.lane ?? null);
  if (sheet.dataset.sheet === 'stack') highlight(sheet, 'layer', e.target.closest('[data-layer]')?.dataset.layer ?? null);
}
document.addEventListener('pointerover', trace);
document.addEventListener('focusin', trace);
document.addEventListener('pointerout', (e) => {
  const sheet = e.target.closest?.('.sheet');
  if (sheet && !sheet.contains(e.relatedTarget)) {
    if (sheet.dataset.sheet === 'crew') highlight(sheet, 'lane', null);
    if (sheet.dataset.sheet === 'stack') highlight(sheet, 'layer', null);
  }
});

/* Skyline: the day under the pointer */
const tip = $('[data-tip]');
const dayFormat = new Intl.DateTimeFormat('en-GB', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric', timeZone: 'UTC' });
let hotBuilding = null;
document.addEventListener('pointermove', (e) => {
  const building = e.target.closest?.('.sheet[data-sheet="skyline"] [data-d]');
  if (building !== hotBuilding) {
    hotBuilding?.classList.remove('hot');
    hotBuilding = building;
    building?.classList.add('hot');
  }
  if (!building) { tip.hidden = true; return; }
  const count = Number(building.dataset.c);
  tip.innerHTML = '';
  const strong = document.createElement('b');
  strong.textContent = `${count} contribution${count === 1 ? '' : 's'}`;
  tip.append(strong, dayFormat.format(new Date(`${building.dataset.d}T00:00:00Z`)));
  tip.hidden = false;
  const x = Math.min(e.clientX + 14, innerWidth - tip.offsetWidth - 8);
  tip.style.left = `${x}px`;
  tip.style.top = `${Math.max(8, e.clientY - tip.offsetHeight - 12)}px`;
}, { passive: true });

/* Replays restart the CSS and SMIL animations by swapping in a fresh copy */
function replay(slug) {
  $$(`.sheet[data-sheet="${slug}"]`).forEach((svg) => svg.replaceWith(svg.cloneNode(true)));
}
$$('[data-replay]').forEach((b) => b.addEventListener('click', () => replay(b.dataset.replay)));

/* Send a feature through the crew: a simulation of the workflow */
const sim = $('[data-sim]');
const log = $('[data-log]');
function light(nodes, on) {
  nodes.forEach((node) => $$(`.sheet[data-sheet="hero"] [data-node="${node}"]`).forEach((el) => el.classList.toggle('live', on)));
}
function write(tag, message, startedAt, human = false) {
  const li = document.createElement('li');
  const time = document.createElement('time');
  const badge = document.createElement('span');
  const msg = document.createElement('span');
  time.textContent = `+${((performance.now() - startedAt) / 1000).toFixed(1)}s`;
  badge.className = `tag${human ? ' human' : ''}`;
  badge.textContent = tag;
  msg.className = 'msg';
  msg.textContent = message;
  li.className = human ? 'human' : '';
  li.append(time, badge, msg);
  log.append(li);
  log.scrollTop = log.scrollHeight;
  return li;
}
sim?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const button = $('button[type=submit]', sim);
  const feature = (sim.feature.value.trim() || 'Add subscription billing').slice(0, 60);
  button.disabled = true;
  log.replaceChildren();
  $('#sheet-hero').scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth', block: 'start' });
  const t0 = performance.now();
  const steps = [
    [['SM'], 'SM', `brief: “${feature}”`, true],
    [['EX'], 'EX', 'Mapped the parts of the codebase this touches'],
    [['PL'], 'PL', 'Split into three tracks: API, screens, UI kit'],
    [['SM'], 'SM', 'plan approved', true],
  ];
  for (const [nodes, tag, message, human] of steps) {
    light(nodes, true);
    const li = write(tag, message, t0, human);
    await sleep(850);
    light(nodes, false);
    li.classList.add('done');
  }
  light(['LA', 'FR', 'DS'], true);
  const tracks = [['LA', 'API track built, Pest tests green'], ['FR', 'Screens wired to the API'], ['DS', 'UI kit updated, dark mode included']];
  const lines = [];
  for (const [tag, message] of tracks) { lines.push(write(tag, message, t0)); await sleep(300); }
  await sleep(700);
  lines.forEach((li) => li.classList.add('done'));
  light(['LA', 'FR', 'DS'], false);
  const tail = [
    [['CR'], 'CR', 'Review passed after one round of fixes'],
    [['SM', 'gate'], 'SM', 'final call: approved', true],
    [['ship'], '↗', 'Shipped to production'],
  ];
  for (const [nodes, tag, message, human] of tail) {
    light(nodes, true);
    const li = write(tag, message, t0, human);
    await sleep(850);
    light(nodes, false);
    li.classList.add('done');
  }
  button.disabled = false;
});

/* Work orders: private tickets through the endpoint, or an email when none is set */
const order = $('[data-ticket]');
const status = $('[data-status]');
order?.addEventListener('submit', async (e) => {
  e.preventDefault();
  status.classList.remove('err');
  for (const field of $$('input,textarea', order)) field.removeAttribute('aria-invalid');
  const invalid = $$('input[required],textarea[required]', order).filter((f) => !f.checkValidity());
  if (invalid.length) {
    invalid.forEach((f) => f.setAttribute('aria-invalid', 'true'));
    invalid[0].focus();
    status.textContent = invalid[0].name === 'email'
      ? 'Add an email I can reply to.'
      : invalid[0].name === 'message' ? 'Add a few more details, at least 20 characters.' : 'Fill in your name.';
    status.classList.add('err');
    return;
  }
  const form = new FormData(order);
  const ticket = {
    name: form.get('name').trim(),
    email: form.get('email').trim(),
    kind: form.get('kind'),
    timeline: form.get('timeline'),
    message: form.get('message').trim(),
    company_site: form.get('company_site'),
    turnstile: form.get('cf-turnstile-response') ?? '',
  };
  const kindLabel = data.tickets.kinds[ticket.kind] ?? 'Work order';
  if (!data.tickets.endpoint) {
    const body = `${ticket.message}\n\nKind: ${kindLabel}\nTimeline: ${ticket.timeline || 'Not sure yet'}\nReply to: ${ticket.name} <${ticket.email}>`;
    location.href = `mailto:${data.tickets.email}?subject=${encodeURIComponent(`[${kindLabel}] ${ticket.name}`)}&body=${encodeURIComponent(body)}`;
    status.textContent = 'Your email app should open with the work order filled in. Send it from there.';
    return;
  }
  const button = $('button[type=submit]', order);
  button.disabled = true;
  status.textContent = 'Filing…';
  try {
    const response = await fetch(data.tickets.endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(ticket),
    });
    const result = await response.json().catch(() => ({}));
    if (!response.ok || !result.ok) throw new Error(result.error ?? `HTTP ${response.status}`);
    order.hidden = true;
    const filed = $('[data-filed]');
    $('[data-filed-text]', filed).textContent = `Work order${result.number ? ` #${result.number}` : ''} is on my desk. I’ll reply to ${ticket.email}.`;
    filed.hidden = false;
    filed.focus();
  } catch (error) {
    status.innerHTML = '';
    status.append(`Couldn’t file it (${error.message}). Email me instead: `);
    const mail = document.createElement('a');
    mail.href = `mailto:${data.tickets.email}`;
    mail.textContent = data.tickets.email;
    status.append(mail);
    status.classList.add('err');
    button.disabled = false;
  }
});

/* Keyboard */
document.addEventListener('keydown', (e) => {
  if ((e.key === 'Enter' || e.key === ' ') && e.target.matches?.('[data-agent],[data-rule]')) {
    e.preventDefault();
    e.target.dispatchEvent(new MouseEvent('click', { bubbles: true }));
    return;
  }
  if (e.key === 'Escape') { closeCard(); return; }
  if (e.metaKey || e.ctrlKey || e.altKey || e.target.closest?.('input,textarea,select')) return;
  const sections = ['sheet-hero', 'sheet-crew', 'sheet-stack', 'sheet-skyline'];
  if (/^[1-4]$/.test(e.key)) document.getElementById(sections[Number(e.key) - 1])?.scrollIntoView({ behavior: reducedMotion ? 'auto' : 'smooth' });
  if (e.key === 't' || e.key === 'T') applyTheme(themes[(themes.indexOf(currentTheme()) + 1) % themes.length]);
});
