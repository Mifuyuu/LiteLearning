#!/usr/bin/env bash
# Deploy the app on the prod server (run this ON the server, e.g. /opt/litelearning).
# Pulls latest code, rebuilds the php/queue/scheduler images, runs migrations,
# and syncs the built frontend assets to the host (nginx serves public/ from disk,
# but npm run build only happens inside the docker image — so it must be copied out).
set -euo pipefail

BRANCH="${1:-1.1}"
COMPOSE="docker compose -f docker-compose.prod.yml"

git pull origin "$BRANCH"

sudo $COMPOSE build php queue scheduler
sudo $COMPOSE up -d --no-deps php queue scheduler

sudo docker exec php php artisan migrate --force
sudo docker exec php php artisan optimize:clear
sudo docker exec php php artisan optimize

sudo docker cp php:/var/www/html/public/build /opt/litelearning/public/build.new
sudo rm -rf public/build
sudo mv public/build.new public/build
sudo chown -R "$(whoami)":"$(whoami)" public/build

echo "Deployed $(git rev-parse --short HEAD) on branch $BRANCH"
