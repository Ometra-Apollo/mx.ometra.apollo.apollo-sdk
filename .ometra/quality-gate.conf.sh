#!/usr/bin/env bash
QUALITY_GATE_JOBS=(composer quality)
quality_gate_composer() {
  composer validate --strict --no-interaction
  composer audit --locked --no-interaction
  composer install --prefer-dist --no-interaction --no-progress
}
quality_gate_quality() {
  composer run lint
  php -d memory_limit=512M vendor/bin/phpstan analyse --no-progress
  composer run test
}
