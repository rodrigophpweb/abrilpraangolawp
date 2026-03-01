<?php
if (!defined('ABSPATH')) exit;

/**
 * Schema.org JSON-LD — Organization (global) + Event (Home).
 *
 * @package eventos-capoeira
 */

/* ---------------------------------------------------------------
 * Hook principal
 * ------------------------------------------------------------- */
add_action('wp_head', 'ec_output_schema', 20);

/**
 * Imprime os blocos JSON-LD no <head>.
 */
function ec_output_schema(): void {

  // Organization — sempre
  $org = ec_build_organization_schema();
  if ($org) {
    ec_print_jsonld($org);
  }

  // Event — apenas na Home
  if (is_front_page()) {
    $event = ec_build_event_schema();
    if ($event) {
      ec_print_jsonld($event);
    }
  }
}

/* ---------------------------------------------------------------
 * Organization
 * ------------------------------------------------------------- */
function ec_build_organization_schema(): array {

  $schema = [
    '@context' => 'https://schema.org',
    '@type'    => 'Organization',
    'name'     => get_bloginfo('name'),
    'url'      => home_url('/'),
  ];

  // Logo (custom_logo)
  $custom_logo_id = get_theme_mod('custom_logo');
  if ($custom_logo_id) {
    $logo_url = wp_get_attachment_image_url((int) $custom_logo_id, 'full');
    if ($logo_url) {
      $schema['logo'] = $logo_url;
    }
  }

  // Redes sociais
  $same_as = [];

  if (function_exists('get_field')) {
    $instagram = get_field('instagram', 'option');
    $youtube   = get_field('youtube', 'option');

    if ($instagram) $same_as[] = esc_url($instagram);
    if ($youtube)   $same_as[] = esc_url($youtube);
  }

  if ($same_as) {
    $schema['sameAs'] = $same_as;
  }

  return $schema;
}

/* ---------------------------------------------------------------
 * Event (Home)
 * ------------------------------------------------------------- */
function ec_build_event_schema(): array {

  if (!function_exists('get_field')) return [];

  $start_raw = get_field('data_inicio_evento', 'option');
  $end_raw   = get_field('data_fim_evento', 'option');
  $local     = get_field('local_evento', 'option');

  // Sem datas = sem schema Event
  if (!$start_raw || !$end_raw) return [];

  $home_id = (int) get_option('page_on_front');

  $schema = [
    '@context'            => 'https://schema.org',
    '@type'               => 'Event',
    'name'                => $home_id ? get_the_title($home_id) : get_bloginfo('name'),
    'startDate'           => ec_format_iso_datetime($start_raw),
    'endDate'             => ec_format_iso_datetime($end_raw),
    'eventStatus'         => 'https://schema.org/EventScheduled',
    'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
  ];

  // Location
  if ($local) {
    $schema['location'] = [
      '@type'   => 'Place',
      'name'    => $local,
      'address' => [
        '@type'           => 'PostalAddress',
        'addressLocality' => 'São Paulo',
        'addressCountry'  => 'BR',
      ],
    ];
  }

  // Image (thumbnail da Home)
  if ($home_id && has_post_thumbnail($home_id)) {
    $img_url = get_the_post_thumbnail_url($home_id, 'full');
    if ($img_url) {
      $schema['image'] = $img_url;
    }
  }

  // Description
  $home_post = $home_id ? get_post($home_id) : null;
  if ($home_post && $home_post->post_content) {
    $schema['description'] = wp_trim_words(
      wp_strip_all_tags($home_post->post_content),
      30,
      '…'
    );
  }

  // Organizer
  $schema['organizer'] = [
    '@type' => 'Organization',
    'name'  => get_bloginfo('name'),
    'url'   => home_url('/'),
  ];

  // Offers (ingressos)
  $offers = ec_get_event_offers();
  if ($offers) {
    $schema['offers'] = $offers;
  }

  return $schema;
}

/* ---------------------------------------------------------------
 * Helpers
 * ------------------------------------------------------------- */

/**
 * Converte uma data Y-m-d em ISO 8601 com timezone America/Sao_Paulo.
 *
 * @param string $date_string Data no formato Y-m-d.
 * @return string ISO 8601 (ex.: 2026-04-10T00:00:00-03:00).
 */
function ec_format_iso_datetime(string $date_string): string {
  try {
    $tz   = new DateTimeZone('America/Sao_Paulo');
    $date = new DateTime($date_string, $tz);
    return $date->format('c'); // ISO 8601
  } catch (Exception $e) {
    return $date_string;
  }
}

/**
 * Retorna array de Offer a partir do CPT ingressos publicados.
 */
function ec_get_event_offers(): array {

  if (!function_exists('get_field')) return [];

  $q = new WP_Query([
    'post_type'      => 'ingressos',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
    'orderby'        => 'title',
    'order'          => 'ASC',
    'no_found_rows'  => true,
  ]);

  if (!$q->have_posts()) return [];

  $offers = [];

  while ($q->have_posts()) {
    $q->the_post();

    $preco_raw = get_field('preco_a_vista');

    // Normaliza preço: remove "R$", espaços, pontos de milhar e troca vírgula por ponto
    $preco = '';
    if ($preco_raw) {
      $preco = preg_replace('/[^\d,.]/', '', (string) $preco_raw);
      $preco = str_replace('.', '', $preco); // remove pontos de milhar
      $preco = str_replace(',', '.', $preco); // vírgula → ponto decimal
    }

    $validade_raw = get_field('validade_ticket');

    $offer = [
      '@type'         => 'Offer',
      'name'          => get_the_title(),
      'priceCurrency' => 'BRL',
      'availability'  => 'https://schema.org/InStock',
      'url'           => get_permalink(),
    ];

    if ($preco !== '' && is_numeric($preco)) {
      $offer['price'] = $preco;
    }

    if ($validade_raw) {
      $offer['validFrom'] = ec_format_iso_datetime($validade_raw);
    }

    $offers[] = $offer;
  }

  wp_reset_postdata();

  return $offers;
}

/**
 * Imprime um bloco <script type="application/ld+json">.
 *
 * @param array $data Dados do schema.
 */
function ec_print_jsonld(array $data): void {
  if (!$data) return;

  echo '<script type="application/ld+json">'
     . wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
     . '</script>' . "\n";
}
