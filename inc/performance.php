<?php
if (!defined('ABSPATH')) exit;

/**
 * Performance — eventos-capoeira
 *
 * • Remove wp-embed no front
 * • Resource hints (preconnect / dns-prefetch)
 * • Defer scripts do tema
 * • Preload de assets críticos (logo, hero)
 * • Cache-Control de HTML para visitantes
 * • Garante loading="lazy" e decoding="async" em imagens
 *
 * @package eventos-capoeira
 */

/* ===============================================================
 * 1) Desabilitar wp-embed no front
 * ============================================================ */
add_action('wp_footer', function (): void {
  wp_deregister_script('wp-embed');
});

/* ===============================================================
 * 2) Resource Hints — preconnect / dns-prefetch
 * ============================================================ */
add_filter('wp_resource_hints', 'ec_resource_hints', 10, 2);

function ec_resource_hints(array $urls, string $relation): array {

  if ($relation === 'preconnect') {

    // Google Fonts (usado globalmente)
    $urls[] = [
      'href'        => 'https://fonts.googleapis.com',
      'crossorigin' => '',
    ];
    $urls[] = [
      'href'        => 'https://fonts.gstatic.com',
      'crossorigin' => 'anonymous',
    ];

    // Swiper CDN (somente Home)
    if (is_front_page()) {
      $urls[] = [
        'href'        => 'https://cdn.jsdelivr.net',
        'crossorigin' => '',
      ];
    }
  }

  if ($relation === 'dns-prefetch') {
    // Google Maps — somente em páginas que podem ter o embed
    if (is_front_page() || is_page()) {
      $urls[] = 'https://www.google.com';
      $urls[] = 'https://maps.google.com';
    }
  }

  return $urls;
}

/* ===============================================================
 * 3) Defer scripts do tema via script_loader_tag
 * ============================================================ */
add_filter('script_loader_tag', 'ec_defer_scripts', 10, 2);

function ec_defer_scripts(string $tag, string $handle): string {

  // Handles que devem receber defer
  $defer_handles = [
    'ec-global',
    'ec-home',
    'ec-inscricao',
    'ec-patrocinio',
    'ec-swiper',
    'ec-clipboard',
  ];

  if (!in_array($handle, $defer_handles, true)) return $tag;

  // Não duplicar se já tiver defer/async
  if (strpos($tag, ' defer') !== false || strpos($tag, ' async') !== false) return $tag;

  return str_replace(' src=', ' defer src=', $tag);
}

/* ===============================================================
 * 4) Preload de assets críticos
 * ============================================================ */
add_action('wp_head', 'ec_preload_critical_assets', 2);

function ec_preload_critical_assets(): void {
  // Não no admin
  if (is_admin()) return;

  // Preload do custom logo
  $logo_id = get_theme_mod('custom_logo');
  if ($logo_id) {
    $logo_url = wp_get_attachment_image_url((int) $logo_id, 'full');
    if ($logo_url) {
      printf(
        '<link rel="preload" href="%s" as="image" fetchpriority="high">' . "\n",
        esc_url($logo_url)
      );
    }
  }

  // Preload do hero background na Home
  if (is_front_page()) {
    $home_id = (int) get_option('page_on_front');
    if ($home_id && has_post_thumbnail($home_id)) {
      $hero_url = get_the_post_thumbnail_url($home_id, 'full');
      if ($hero_url) {
        printf(
          '<link rel="preload" href="%s" as="image" fetchpriority="high">' . "\n",
          esc_url($hero_url)
        );
      }
    }
  }
}

/* ===============================================================
 * 5) Cache-Control para HTML
 * ============================================================ */
add_filter('wp_headers', 'ec_cache_control_headers');

function ec_cache_control_headers(array $headers): array {
  if (is_admin()) return $headers;

  if (is_user_logged_in()) {
    // Usuário logado: não cachear HTML
    $headers['Cache-Control'] = 'no-cache, no-store, must-revalidate';
  } else {
    // Visitante: cache de 5 min no navegador, pode usar stale enquanto revalida
    $headers['Cache-Control'] = 'public, max-age=300, stale-while-revalidate=60';
  }

  return $headers;
}

/* ===============================================================
 * 6) Garantir loading="lazy" e decoding="async" em imagens
 *    (WP 5.5+ já faz lazy; reforçamos decoding="async")
 * ============================================================ */
add_filter('wp_get_attachment_image_attributes', 'ec_image_attributes', 10, 3);

function ec_image_attributes(array $attr, WP_Post $attachment, $size): array {

  // Não aplicar lazy na logo (acima do fold)
  if (isset($attr['class']) && strpos($attr['class'], 'custom-logo') !== false) {
    $attr['loading']       = 'eager';
    $attr['fetchpriority']  = 'high';
    $attr['decoding']       = 'async';
    return $attr;
  }

  // Demais imagens
  if (!isset($attr['loading'])) {
    $attr['loading'] = 'lazy';
  }
  if (!isset($attr['decoding'])) {
    $attr['decoding'] = 'async';
  }

  return $attr;
}

/* ===============================================================
 * 7) Remover jQuery migrate no front (se não necessário)
 * ============================================================ */
add_action('wp_default_scripts', function ($scripts): void {
  if (is_admin()) return;

  if (isset($scripts->registered['jquery'])) {
    $jquery = $scripts->registered['jquery'];
    // Remover jquery-migrate das dependências
    if ($jquery->deps) {
      $jquery->deps = array_diff($jquery->deps, ['jquery-migrate']);
    }
  }
});

/* ===============================================================
 * 8) Otimizar queries do tema (helper)
 *    Para queries que NÃO precisam de paginação nem contagem.
 * ============================================================ */

/**
 * Retorna defaults otimizados para WP_Query sem paginação.
 * Merge com args personalizados.
 *
 * @param array $args Args da WP_Query.
 * @return array Args otimizados.
 */
function ec_optimized_query_args(array $args = []): array {
  $defaults = [
    'no_found_rows'          => true,
    'update_post_meta_cache' => true,
    'update_post_term_cache' => false,
  ];

  return wp_parse_args($args, $defaults);
}

/* ===============================================================
 * 9) Remover scripts de comentários quando não necessários
 * ============================================================ */
add_action('wp_enqueue_scripts', function (): void {
  if (!is_singular() || !comments_open() || !get_option('thread_comments')) {
    wp_deregister_script('comment-reply');
  }
});

/* ===============================================================
 * 10) Preconnect Google Fonts diretamente no head (fallback)
 *     (Complementa resource hints — imprime antes de qualquer CSS)
 * ============================================================ */
add_action('wp_head', function (): void {
  if (is_admin()) return;

  echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
  echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1);
