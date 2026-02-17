# Local S3 Testing (Docker + MinIO)

## 1) Start local S3

```bash
docker compose -f docker-compose.s3.yml up -d
```

- S3 API: `http://127.0.0.1:9000`
- MinIO Console: `http://127.0.0.1:9001`
- Username: `minioadmin`
- Password: `minioadmin`
- Bucket: `litelearning`

## 2) Laravel `.env` settings

Use these values:

```dotenv
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=minioadmin
AWS_SECRET_ACCESS_KEY=minioadmin
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=litelearning
AWS_USE_PATH_STYLE_ENDPOINT=true
AWS_ENDPOINT=http://127.0.0.1:9000
AWS_URL=http://127.0.0.1:9000/litelearning
```

Then reload config:

```bash
php artisan config:clear
```

## 3) Quick upload test

```bash
php artisan tinker --execute "\Illuminate\Support\Facades\Storage::disk('s3')->put('healthcheck.txt', 'ok'); echo \Illuminate\Support\Facades\Storage::disk('s3')->url('healthcheck.txt') . PHP_EOL;"
```

If successful, you'll get a URL under `http://127.0.0.1:9000/litelearning/...`.

## 4) Stop service

```bash
docker compose -f docker-compose.s3.yml down
```
