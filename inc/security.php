<?php
if (!defined('ABSPATH')) exit;

/**
 * Hardening & Security — eventos-capoeira
 *
 * • Remove leaks de versão e meta desnecessários
 * • Desabilita emojis no front
 * • Adiciona headers de segurança (somente front-end)
 * • CSP Report-Only opcional (via constante EC_CSP_REPORT_ONLY)
 * • Helper ec_safe_iframe() para sanitizar iframes
 *
 * @package eventos-capoeira
 */

/* ===============================================================
 * 1) Remover meta tags que vazam informação
 * ============================================================ */
remove_action('wp_head', 'wp_generator');                    // meta generator
remove_action('wp_head', 'wlwmanifest_link');                // Windows Live Writer
remove_action('wp_head', 'rsd_link');                        // Really Simple Discovery
remove_action('wp_head', 'wp_shortlink_wp_head', 10);       // shortlink
remove_action('wp_head', 'rest_output_link_wp_head', 10);   // REST API <link>
remove_action('wp_head', 'wp_oembed_add_discovery_links');   // oEmbed discovery
remove_action('template_redirect', 'rest_output_link_header', 11, 0);

// Remover versão dos feeds
add_filter('the_generator', '__return_empty_string');

// Remover versão de query strings em scripts/styles (front-end apenas)
add_filter('style_loader_src', 'ec_remove_ver_query', 999);
add_filter('script_loader_src', 'ec_remove_ver_query', 999);

function ec_remove_ver_query(string $src): string {
  if (is_admin()) return $src;
  // Preserva versão de assets CDN externos
  if (strpos($src, home_url()) === false) return $src;
  return remove_query_arg('ver', $src);
}

/* ===============================================================
 * 2) Desabilitar Emojis no front
 * ============================================================ */
add_action('init', 'ec_disable_emojis');

function ec_disable_emojis(): void {
  remove_action('wp_head', 'print_emoji_detection_script', 7);
  remove_action('admin_print_scripts', 'print_emoji_detection_script');
  remove_action('wp_print_styles', 'print_emoji_styles');
  remove_action('admin_print_styles', 'print_emoji_styles');
  remove_filter('the_content_feed', 'wp_staticize_emoji');
  remove_filter('comment_text_rss', 'wp_staticize_emoji');
  remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

  add_filter('tiny_mce_plugins', function (array $plugins): array {
    return array_diff($plugins, ['wpemoji']);
  });

  add_filter('wp_resource_hints', function (array $urls, string $relation): array {
    if ($relation === 'dns-prefetch') {
      $urls = array_filter($urls, function ($url) {
        return strpos((string) $url, 'https://s.w.org') === false;
      });
    }
    return $urls;
  }, 10, 2);
}

/* ===============================================================
 * 3) Headers de segurança (somente front-end)
 * ============================================================ */
add_filter('wp_headers', 'ec_security_headers');

function ec_security_headers(array $headers): array {
  // Não aplicar no admin
  if (is_admin()) return $headers;

  $headers['X-Content-Type-Options'] = 'nosniff';
  $headers['Referrer-Policy']        = 'strict-origin-when-cross-origin';
  $headers['X-Frame-Options']        = 'SAMEORIGIN';
  $headers['Permissions-Policy']     = 'geolocation=(), microphone=(), camera=(), payment=(), usb=()';

  return $headers;
}

/* ===============================================================
 * 4) CSP Report-Only (opcional — ativar com constante)
 * ============================================================ */
add_filter('wp_headers', 'ec_csp_report_only_header');

function ec_csp_report_only_header(array $headers): array {
  if (is_admin()) return $headers;
  if (!defined('EC_CSP_REPORT_ONLY') || !EC_CSP_REPORT_ONLY) return $headers;

  $policy = implode('; ', [
    "default-src 'self'",
    "img-src 'self' data: https:",
    "style-src 'self' 'unsafe-inline' https:",
    "script-src 'self' https:",
    "frame-src https://www.google.com https://maps.google.com",
    "font-src 'self' https://fonts.gstatic.com",
    "connect-src 'self'",
  ]);

  $headers['Content-Security-Policy-Report-Only'] = $policy;

  return $headers;
}

/* ===============================================================
 * 5) Helper: sanitizar iframe (Google Maps etc.)
 * ============================================================ */

/**
 * Sanitiza uma string contendo <iframe> com allowlist restrita.
 *
 * @param string $html HTML bruto com iframe.
 * @return string HTML limpo.
 */
function ec_safe_iframe(string $html): string {
  static $allowed = null;

  if ($allowed === null) {
    $allowed = [
      'iframe' => [
        'src'             => [],
        'width'           => [],
        'height'          => [],
        'style'           => [],
        'allowfullscreen' => [],
        'loading'         => [],
        'referrerpolicy'  => [],
        'title'           => [],
        'aria-label'      => [],
      ],
    ];
  }

  return wp_kses($html, $allowed);
}

/* ===============================================================
 * 6) Desabilitar XML-RPC (raramente necessário em sites públicos)
 * ============================================================ */
add_filter('xmlrpc_enabled', '__return_false');

/* ===============================================================
 * 7) Remover header X-Pingback
 * ============================================================ */
add_filter('wp_headers', function (array $headers): array {
  unset($headers['X-Pingback']);
  return $headers;
});
