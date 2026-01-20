#!/usr/bin/env bash

set -e

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)"

SRC_DIR="$SCRIPT_DIR/../develop/images/icons"
OUT_FILE="$SCRIPT_DIR/../public/assets/images/icons.svg"

TMP_DIR="$(mktemp -d)"
SPRITE_BODY="$TMP_DIR/body.svg"

echo "" > "$SPRITE_BODY"

echo "Building SVG sprite..."

for FILE in "$SRC_DIR"/*.svg; do
  NAME="$(basename "$FILE" .svg | tr '[:upper:]' '[:lower:]' | tr ' ' '-')"
  echo " → $NAME"
  # Remove outer <svg> wrapper, remove fills, remove width/height
  CONTENT=$(sed -E \
    -e 's/<svg[^>]*>//g' \
    -e 's/<\/svg>//g' \
    -e 's/fill="[^"]*"//g' \
    -e 's/stroke="[^"]*"//g' \
    -e 's/width="[^"]*"//g' \
    -e 's/height="[^"]*"//g' \
    -e 's/[[:space:]]{2,}/ /g' \
    "$FILE")

  cat >> "$SPRITE_BODY" <<EOF
<symbol id="$NAME" viewBox="0 0 20 20">$CONTENT</symbol>
EOF

done

# Assemble final sprite
cat > "$OUT_FILE" <<EOF
<svg xmlns="http://www.w3.org/2000/svg" style="display:none">$(
  cat "$SPRITE_BODY"
)
</svg>
EOF

rm -rf "$TMP_DIR"

echo "Sprite written to:"
echo " → $OUT_FILE"
