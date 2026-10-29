#!/usr/bin/env bash
# Menyalin file FinShield ke project Laravel.
# Pemakaian:  bash pasang.sh /path/ke/project/finshield
set -euo pipefail

TARGET="${1:-}"
SUMBER="$(cd "$(dirname "$0")" && pwd)"

if [[ -z "$TARGET" || ! -f "$TARGET/artisan" ]]; then
  echo "Berikan path project Laravel (folder yang berisi file 'artisan')."
  echo "Contoh: bash pasang.sh ~/finshield"
  exit 1
fi

mkdir -p "$TARGET/resources/views/layouts" "$TARGET/resources/views/components" \
         "$TARGET/resources/css" "$TARGET/resources/js" "$TARGET/app/Http/Controllers" "$TARGET/routes"

cp "$SUMBER/routes/web.php"                                   "$TARGET/routes/web.php"
cp "$SUMBER/app/Http/Controllers/FinShieldController.php"     "$TARGET/app/Http/Controllers/FinShieldController.php"
cp "$SUMBER/resources/views/layouts/app.blade.php"            "$TARGET/resources/views/layouts/app.blade.php"
cp "$SUMBER/resources/views/components/icon.blade.php"        "$TARGET/resources/views/components/icon.blade.php"
cp "$SUMBER/resources/views/beranda.blade.php"                "$TARGET/resources/views/beranda.blade.php"
cp "$SUMBER/resources/views/edukasi-judol.blade.php"          "$TARGET/resources/views/edukasi-judol.blade.php"
cp "$SUMBER/resources/views/kalkulator-pinjol.blade.php"      "$TARGET/resources/views/kalkulator-pinjol.blade.php"
cp "$SUMBER/resources/views/kelola-uang.blade.php"            "$TARGET/resources/views/kelola-uang.blade.php"
cp "$SUMBER/resources/css/app.css"                            "$TARGET/resources/css/app.css"
cp "$SUMBER/resources/js/app.js"                              "$TARGET/resources/js/app.js"
cp "$SUMBER/tailwind.config.js"                               "$TARGET/tailwind.config.js"
cp "$SUMBER/postcss.config.js"                                "$TARGET/postcss.config.js"
cp "$SUMBER/vite.config.js"                                   "$TARGET/vite.config.js"

echo "Selesai. File FinShield sudah disalin ke: $TARGET"
echo "Lanjutkan: cd \"$TARGET\" && npm run dev   (terminal 1)   |   php artisan serve   (terminal 2)"
