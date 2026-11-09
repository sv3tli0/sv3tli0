# Work-order intake

The site's work-order form posts here. The worker checks the request, verifies Cloudflare Turnstile and files the
order as an issue in the private `sv3tli0/inbox` repo, so only the owner ever sees it. No LLM, no paid services:
Workers and Turnstile both run on Cloudflare's free tier.

## Setup

1. **Turnstile:** Cloudflare dashboard → Turnstile → add a widget for `sv3tli0.github.io`. Note the site key and the secret key.
2. **Token:** GitHub → Settings → Developer settings → Fine-grained tokens. Repository access: only `sv3tli0/inbox`.
   Permissions: Issues, read and write. Nothing else.
3. **Deploy** from this folder:
   ```sh
   npx wrangler login
   npx wrangler secret put GITHUB_TOKEN
   npx wrangler secret put TURNSTILE_SECRET
   npx wrangler deploy
   ```
4. **Wire the site:** put the worker URL and the Turnstile site key into `config/profile.php` under `tickets`
   (`endpoint`, `turnstile`) and push. Until then the form falls back to a prefilled email.

## Behaviour

- Only `ALLOWED_ORIGINS` may post; bodies over 16 KB are refused.
- A filled hidden field (bots) gets a fake success and is dropped.
- The visitor's text goes into a fenced block, so links, images and @mentions don't render.
- Labels: `work-order` plus the kind (`project`, `contract`, `agents`, `contact`).
