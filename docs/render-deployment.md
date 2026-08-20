# Render Deployment

Date: August 20, 2026

## Decision

Use Render for the real Laravel website, with a free Render web service and free Render Postgres database for the first hosted phase.

Render can deploy Docker-based web services and supports Blueprints through `render.yaml`. Render's free tier supports web services and Render Postgres databases, but free instances have limitations and should not be treated as final production capacity.

MySQL can run on Render through a Docker/private service with a persistent disk, but that is not the best free first deployment path. For now, use Postgres because Render manages it directly in Blueprints.

## Files Added

- `render.yaml`: creates the Laravel web service and a free Postgres database.
- `.dockerignore`: keeps Docker builds smaller and avoids copying local secrets or generated runtime files.

## How To Deploy

1. Open Render Dashboard.
2. Choose **New +**.
3. Choose **Blueprint**.
4. Connect GitHub repository `kirannetpack-ui/kwdc`.
5. Select branch `production-readiness-reminders-notifications`.
6. Render will read `render.yaml`.
7. Fill all `sync: false` environment variables.
8. Deploy.

## Required Secret Values

Render will ask for these because they are marked `sync: false`:

- `APP_KEY`
- `APP_URL`
- `MAIL_HOST`
- `MAIL_PORT`
- `MAIL_USERNAME`
- `MAIL_PASSWORD`
- `MAIL_ENCRYPTION`
- `MAIL_FROM_ADDRESS`
- `KHALTI_PUBLIC_KEY`
- `KHALTI_SECRET_KEY`
- `ESEWA_MERCHANT_CODE`
- `ESEWA_SECRET_KEY`

Generate `APP_KEY` locally with:

```bash
php artisan key:generate --show
```

Set `APP_URL` to the Render URL first, for example:

```text
https://kwdc-web.onrender.com
```

After a custom domain is connected through Cloudflare, update `APP_URL` to the final `https://` domain.

## Notes For Free Phase

- `QUEUE_CONNECTION=sync` is used so the free web service can run without a separate paid worker.
- `SESSION_DRIVER=database` and `CACHE_STORE=database` use the free Postgres database.
- The app startup script runs production preflight checks and migrations.
- Real production should later add a background worker, scheduler, durable file storage, and paid database/service plans.

## References

- Render Blueprint YAML reference: https://render.com/docs/blueprint-spec
- Render Laravel Docker guide: https://render.com/docs/deploy-php-laravel-docker
- Render free tier: https://render.com/docs/free
