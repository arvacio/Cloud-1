#!/usr/bin/env bash
# deploy.sh - Sincroniza el proyecto con las 3 VM web (Ubuntu + NGINX)
set -euo pipefail

SERVIDORES=(
  "129.80.101.184 key-NGINX.key"
  "129.80.188.211 key-nginx_1.key"
  "193.122.145.132 key-nginx_2.key"
)
USUARIO="ubuntu"
DESTINO="/var/www/html/agenda"

cd "$(dirname "$0")"

for entrada in "${SERVIDORES[@]}"; do
  read -r ip llave <<< "$entrada"
  echo ">>> Sincronizando con $ip (llave: $llave) ..."
  tar --exclude='.git*' --exclude='database' --exclude='deploy.sh' --exclude='README.md' -czf - . \
    | ssh -i "$HOME/.ssh/$llave" -o StrictHostKeyChecking=accept-new "$USUARIO@$ip" \
      "find $DESTINO -mindepth 1 -delete && tar -xzf - -C $DESTINO && find $DESTINO -type d -exec chmod 755 {} + && find $DESTINO -type f -exec chmod 644 {} +"
  echo "    OK: $ip"
done
echo "Despliegue terminado en ${#SERVIDORES[@]} servidores."
