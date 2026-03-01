<?php
if (!defined('ABSPATH')) exit;

/**
 * Formata data do ACF (Y-m-d) para d/m/Y (ou formato WP).
 */
function ec_format_date($date, $format = 'd/m/Y') {
  if (!$date) return '';
  $ts = strtotime($date);
  if (!$ts) return '';
  return date_i18n($format, $ts);
}

/**
 * Retorna dia da semana (ex: "quinta-feira") para uma data ACF.
 */
function ec_weekday($date) {
  if (!$date) return '';
  $ts = strtotime($date);
  if (!$ts) return '';
  return date_i18n('l', $ts);
}

/**
 * Gera URL do Google Maps "Como chegar" a partir de um endereço.
 */
function ec_google_maps_url($address) {
  if (!$address) return '';
  return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address);
}