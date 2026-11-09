const KINDS = {
  project: 'New product or project',
  contract: 'Contract work',
  agents: 'Agent workflow setup',
  contact: 'Contact',
};

export default {
  async fetch(request, env) {
    const origin = request.headers.get('Origin') ?? '';
    const allowed = env.ALLOWED_ORIGINS.split(',').map((o) => o.trim());
    const cors = {
      'Access-Control-Allow-Origin': allowed.includes(origin) ? origin : allowed[0],
      'Access-Control-Allow-Methods': 'POST, OPTIONS',
      'Access-Control-Allow-Headers': 'Content-Type',
      Vary: 'Origin',
    };
    const reply = (status, body) => new Response(JSON.stringify(body), {
      status,
      headers: { ...cors, 'Content-Type': 'application/json' },
    });

    if (request.method === 'OPTIONS') return new Response(null, { status: 204, headers: cors });
    if (request.method !== 'POST') return reply(405, { ok: false, error: 'POST only' });
    if (!allowed.includes(origin)) return reply(403, { ok: false, error: 'origin not allowed' });
    if (Number(request.headers.get('Content-Length') ?? 0) > 16_000) return reply(413, { ok: false, error: 'too large' });

    let ticket;
    try {
      ticket = await request.json();
    } catch {
      return reply(400, { ok: false, error: 'invalid JSON' });
    }
    // Bots fill the hidden field; let them think it worked.
    if (ticket.company_site) return reply(200, { ok: true });

    const name = clean(ticket.name, 80);
    const email = clean(ticket.email, 120);
    const timeline = clean(ticket.timeline, 40);
    const message = String(ticket.message ?? '').slice(0, 4000).trim();
    const kind = Object.hasOwn(KINDS, ticket.kind) ? ticket.kind : 'contact';
    if (!name || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) || message.length < 20) {
      return reply(422, { ok: false, error: 'missing fields' });
    }

    if (env.TURNSTILE_SECRET) {
      const form = new FormData();
      form.append('secret', env.TURNSTILE_SECRET);
      form.append('response', String(ticket.turnstile ?? ''));
      form.append('remoteip', request.headers.get('CF-Connecting-IP') ?? '');
      const check = await fetch('https://challenges.cloudflare.com/turnstile/v0/siteverify', { method: 'POST', body: form })
        .then((r) => r.json())
        .catch(() => ({ success: false }));
      if (!check.success) return reply(403, { ok: false, error: 'bot check failed' });
    }

    const issue = await fetch(`https://api.github.com/repos/${env.GITHUB_REPO}/issues`, {
      method: 'POST',
      headers: {
        Authorization: `Bearer ${env.GITHUB_TOKEN}`,
        Accept: 'application/vnd.github+json',
        'User-Agent': 'work-order-intake',
      },
      body: JSON.stringify({
        title: `[${KINDS[kind]}] ${defuse(name)}`,
        body: [
          `**From:** ${defuse(name)}`,
          `**Email:** ${defuse(email)}`,
          `**Kind:** ${KINDS[kind]}`,
          `**Timeline:** ${defuse(timeline) || 'Not sure yet'}`,
          '',
          // A fence keeps visitor text from rendering links, images or @mentions.
          '````text',
          message.replace(/`{3,}/g, (m) => 'ˋ'.repeat(m.length)),
          '````',
        ].join('\n'),
        labels: ['work-order', kind],
      }),
    });
    if (!issue.ok) return reply(502, { ok: false, error: 'could not file the ticket' });
    const { number } = await issue.json();

    return reply(201, { ok: true, number });
  },
};

function clean(value, max) {
  return String(value ?? '').slice(0, max).replace(/\s+/g, ' ').trim();
}

function defuse(text) {
  return text.replace(/@/g, '@​').replace(/[<>[\]()`*_]/g, (c) => `\\${c}`);
}
