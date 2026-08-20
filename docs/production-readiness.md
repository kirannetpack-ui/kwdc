# KTM-WDC Production Readiness

This checklist tracks what must be true before KTM-WDC is safe to launch for real users.

## Must Fix Before Launch

- Rotate the exposed OpenAI API key from the earlier screenshot and replace it only in the server environment.
- Set production environment values:
  - `APP_ENV=production`
  - `APP_DEBUG=false`
  - `APP_URL=https://your-real-domain`
  - `LOG_LEVEL=warning`
  - `SESSION_SECURE_COOKIE=true`
  - `SESSION_ENCRYPT=true`
  - `SESSION_SAME_SITE=lax` or stricter if the payment flow allows it
- Generate and store a unique production `APP_KEY`. Never use the placeholder/local key.
- Configure a real database with backups. SQLite is fine for local work, but production should use managed MySQL/Postgres unless traffic will stay very small.
- Configure real email delivery. Current local defaults use log mail, so activation, reset, invoice, and notification email will not reach users.
- Configure payment credentials and callback URLs for Khalti/eSewa in live mode.
- Decide the AI provider for production. If using `free`, no external AI key is needed. If using OpenAI/Gemini/Groq, set the key only in production secrets.
- Build frontend assets for production with `npm run build`; do not expose the Vite dev server to public traffic.
- Run migrations on the production database and seed only intentional production data.
- Set up a queue worker for notifications, reminders, invoices, and any background jobs.
- Set up a scheduler/cron entry for Laravel scheduled tasks.
- Configure storage for uploaded warehouse documents, proofs, images, and QR assets. Use durable object storage if deploying to ephemeral servers.
- Add monitoring for errors, failed jobs, uptime, and disk/storage usage.
- Confirm role access for every user type: admin, client, driver, equipment owner, property owner, and security agency.

## Security Checks

- Confirm no secrets are committed to git or visible in screenshots.
- Confirm debug/test routes remain disabled in production.
- Confirm uploaded files are validated, stored outside public code paths, and served only when authorized.
- Confirm admin-only routes require admin middleware.
- Confirm payment callbacks verify provider signatures or lookup responses before marking invoices paid.
- Confirm password reset, activation, and registration emails are sent through a trusted mail provider.
- Review mass assignment on admin update endpoints before allowing staff to edit users, drivers, equipment, and warehouses.

## Performance Checks

- Run `php artisan config:cache`, `php artisan route:cache`, and `php artisan view:cache` during deployment.
- Use a production web server such as Nginx/Apache/Caddy with PHP-FPM. Do not use `php artisan serve` for production.
- Use Redis or a managed cache if traffic grows beyond small internal use.
- Paginate large admin lists instead of loading every record at once.
- Move expensive AI, email, PDF, and notification work to queues.

## Current Local Findings

- Local app mode is `local` with debug enabled.
- Local database is SQLite.
- Tests pass locally.
- `npm audit` reports a Vite/esbuild development-server advisory. The suggested automatic fix requires a breaking Vite upgrade, so handle it deliberately during dependency upgrade work.
- Composer is not currently available on this machine's PATH, so PHP dependency audit could not be run here.
