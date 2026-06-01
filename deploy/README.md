# DMS Server Deploy

This deploy bundle expects this server layout:

```text
/var/www/
├── dms_back/  # backend repo
└── unionist/  # frontend repo
```

If your frontend repo has another path, change `FRONTEND_CONTEXT` in `deploy/.env`.

## 1. Prepare environment

```bash
cd /var/www/dms_back/deploy
cp .env.example .env
```

Generate a Laravel app key and put it into `APP_KEY`:

```bash
printf 'base64:%s\n' "$(openssl rand -base64 32)"
```

Then edit at least these values in `.env`:

- `APP_URL`
- `FRONTEND_URL`
- `SANCTUM_STATEFUL_DOMAINS`
- `MYSQL_PASSWORD`
- `MYSQL_ROOT_PASSWORD`

For IP-only deployment, use the IP address without protocol in `SANCTUM_STATEFUL_DOMAINS` and leave `SESSION_DOMAIN` empty.

For the current new server:

```env
APP_URL=http://89.207.253.143
FRONTEND_URL=http://89.207.253.143
SESSION_DOMAIN=
SANCTUM_STATEFUL_DOMAINS=89.207.253.143
```

## 2. Start a clean server

```bash
docker compose config
docker compose up -d --build
docker compose ps
```

Backend startup runs:

- `php artisan storage:link`
- `php artisan migrate --force`
- `php artisan optimize:clear`
- `php artisan config:cache`
- `php artisan route:cache`
- `php artisan view:cache`

If this is a clean database and you need starter data:

```bash
docker compose exec backend php artisan db:seed --force
```

## 3. GitHub Actions CI/CD

The existing backend and frontend workflows deploy through SSH. For the new server, update GitHub repository secrets in both repos:

- `DEPLOY_HOST` - new server IP or host
- `DEPLOY_PORT` - SSH port, usually `22`
- `DEPLOY_USER` - SSH user
- `DEPLOY_SSH_KEY` - private key that can SSH into the server
- `DEPLOY_ROOT` - `/var/www`
- `DEPLOY_COMPOSE_FILE` - optional, defaults to `docker-compose.yml`

These optional secrets are only needed if the folders differ from the current server layout:

- `DEPLOY_BACKEND_DIR` - defaults to `dms_back`
- `DEPLOY_FRONTEND_DIR` - defaults to `unionist`
- `DEPLOY_COMPOSE_DIR` - defaults to `dms_back/deploy`

For the current new server, the required GitHub Actions secrets are:

```text
DEPLOY_HOST=89.207.253.143
DEPLOY_PORT=22
DEPLOY_ROOT=/var/www
DEPLOY_USER=<ssh user>
DEPLOY_SSH_KEY=<private ssh key>
```

## 4. Restore database from `backup.sql`

Use this only if you need the existing dumped data from `dms/backup.sql`.

```bash
docker compose up -d mysql
docker compose exec -T mysql sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' < ../backup.sql
docker compose up -d --build
```

The dump includes old migration records. On the next backend start Laravel will apply any newer migrations that are present in the codebase.

## 5. Useful operations

```bash
docker compose logs -f backend
docker compose logs -f nginx
docker compose exec backend php artisan route:list --path=api
docker compose exec backend php artisan migrate:status
docker compose exec backend php artisan optimize:clear
```

Create an initial admin user if the restored database does not already have one:

```bash
docker compose exec backend php artisan tinker --execute="Modules\\User\\Models\\User::updateOrCreate(['email' => 'admin@example.com'], ['name' => 'Admin', 'role' => 'admin', 'password' => bcrypt('ChangeMe123!')]);"
```

Change that password immediately after first login.
