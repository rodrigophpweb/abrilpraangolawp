<?php
if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', function () {

  // Google Fonts — Montserrat (títulos) + Open Sans (corpo)
  wp_enqueue_style(
    'ec-google-fonts',
    'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,600&family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,600&display=swap',
    [],
    null
  );

  wp_enqueue_style(
    'ec-global',
    get_theme_file_uri('/assets/css/global.css'),
    ['ec-google-fonts'],
    '1.0.0'
  );

  $global_js = get_theme_file_path('/assets/js/global.js');
  if (file_exists($global_js)) {
    wp_enqueue_script(
      'ec-global',
      get_theme_file_uri('/assets/js/global.js'),
      [],
      '1.0.0',
      true
    );
  }

  // Home (front-page)
  if (is_front_page()) {
    wp_enqueue_style(
      'ec-home',
      get_theme_file_uri('/assets/css/home.css'),
      ['ec-global'],
      '1.0.0'
    );

    wp_enqueue_script(
      'ec-home',
      get_theme_file_uri('/assets/js/home.js'),
      [],
      '1.0.0',
      true
    );

    // Swiper (CDN) — carrossel de patrocinadores
    wp_enqueue_style(
      'ec-swiper',
      'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
      [],
      null
    );

    wp_enqueue_script(
      'ec-swiper',
      'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
      [],
      null,
      true
    );

    wp_enqueue_script(
      'ec-patrocinio',
      get_theme_file_uri('/assets/js/patrocinio.js'),
      ['ec-swiper'],
      '1.0.0',
      true
    );
  }

  // Página de Inscrições (slug: inscricoes)
  if (is_page('inscricoes')) {
    wp_enqueue_style(
      'ec-inscricoes',
      get_theme_file_uri('/assets/css/page-inscricoes.css'),
      ['ec-global'],
      '1.0.0'
    );

    wp_enqueue_script(
      'ec-inscricao',
      get_theme_file_uri('/assets/js/inscricao.js'),
      [],
      '1.0.0',
      true
    );

    wp_localize_script('ec-inscricao', 'ecInscricao', [
      'ajaxUrl' => admin_url('admin-ajax.php'),
    ]);
  }

  // Archives (CPTs)
  $archive_cpts = ['ingressos', 'oficineiros', 'convidados', 'programacao'];
  if (is_post_type_archive($archive_cpts) || is_tax('agenda')) {
    wp_enqueue_style(
      'ec-archive',
      get_theme_file_uri('/assets/css/archive.css'),
      ['ec-global'],
      '1.0.0'
    );
  }

  // Singles (CPTs)
  if (is_singular($archive_cpts)) {
    wp_enqueue_style(
      'ec-single',
      get_theme_file_uri('/assets/css/single.css'),
      ['ec-global'],
      '1.0.0'
    );
  }

  // Clipboard JS — single de ingressos (copiar Pix)
  if (is_singular('ingressos')) {
    wp_enqueue_script(
      'ec-clipboard',
      get_theme_file_uri('/assets/js/clipboard.js'),
      [],
      '1.0.0',
      true
    );
  }
});

/**
 * CSS dinâmico (ACF Options) -> imprime no <head>
 */
add_action('wp_head', function () {

  // Se ACF não existir, não quebra o tema
  if (!function_exists('get_field')) return;

  // Helper para fallback
  $opt = function(string $name, $default = '') {
    $val = get_field($name, 'option');
    return ($val !== null && $val !== '') ? $val : $default;
  };

  $cor_principal   = $opt('cor_principal', '#111111');
  $cor_secundaria  = $opt('cor_secundaria', '#444444');
  $cor_fundo       = $opt('cor_fundo', '#ffffff');
  $cor_clara       = $opt('cor_clara', '#f5f5f5');
  $cor_escura      = $opt('cor_escura', '#111111');
  $cor_titulo      = $opt('cor_titulo', '#111111');
  $cor_paragrafo   = $opt('cor_paragrafo', '#222222');
  $cor_link        = $opt('cor_link', '#0b5fff');
  $cor_link_hover  = $opt('cor_link_hover', '#0847b8');
  $cor_link_active = $opt('cor_link_active', '#062f7a');

  // Cores de regiões (header, footer, seções)
  $cor_header_bg      = $opt('cor_header_bg', '');
  $cor_header_texto   = $opt('cor_header_texto', '');
  $cor_footer_bg      = $opt('cor_footer_bg', '');
  $cor_footer_texto   = $opt('cor_footer_texto', '');
  $cor_secoes_bg      = $opt('cor_secoes_bg', '');
  $cor_secoes_titulo  = $opt('cor_secoes_titulo', '');

  $tipo_bg         = $opt('tipo_background', 'cor');
  $bg_cor          = $opt('background_cor', $cor_fundo);
  $bg_img_id       = $opt('background_imagem', 0);
  $bg_position     = $opt('background_position', 'center');
  $bg_size         = $opt('background_size', 'cover');
  $bg_repeat       = $opt('background_repeat', 'no-repeat');

  $fonte_base_rem  = (float) $opt('fonte_base_rem', 1);
  if ($fonte_base_rem <= 0) $fonte_base_rem = 1;

  $links_underline = (bool) $opt('links_underline', false);
  $botoes_underline = (bool) $opt('botoes_underline', false);

  // Font-size base: 1rem = 16px, então 1 = 100%, 1.125 = 112.5% etc.
  $html_percent = max(75, min(150, (int) round($fonte_base_rem * 100)));

  $bg_image_url = '';
  if ($tipo_bg === 'imagem' && $bg_img_id) {
    $url = wp_get_attachment_image_url((int)$bg_img_id, 'full');
    if ($url) $bg_image_url = $url;
  }

  ?>
  <style id="ec-theme-vars">
    :root{
      --ec-cor-principal: <?php echo esc_html($cor_principal); ?>;
      --ec-cor-secundaria: <?php echo esc_html($cor_secundaria); ?>;
      --ec-cor-fundo: <?php echo esc_html($cor_fundo); ?>;
      --ec-cor-clara: <?php echo esc_html($cor_clara); ?>;
      --ec-cor-escura: <?php echo esc_html($cor_escura); ?>;
      --ec-cor-titulo: <?php echo esc_html($cor_titulo); ?>;
      --ec-cor-paragrafo: <?php echo esc_html($cor_paragrafo); ?>;
      --ec-cor-link: <?php echo esc_html($cor_link); ?>;
      --ec-cor-link-hover: <?php echo esc_html($cor_link_hover); ?>;
      --ec-cor-link-active: <?php echo esc_html($cor_link_active); ?>;
      <?php if ($cor_header_bg) : ?>
      --ec-cor-header-bg: <?php echo esc_html($cor_header_bg); ?>;
      <?php endif; ?>
      <?php if ($cor_header_texto) : ?>
      --ec-cor-header-texto: <?php echo esc_html($cor_header_texto); ?>;
      <?php endif; ?>
      <?php if ($cor_footer_bg) : ?>
      --ec-cor-footer-bg: <?php echo esc_html($cor_footer_bg); ?>;
      <?php endif; ?>
      <?php if ($cor_footer_texto) : ?>
      --ec-cor-footer-texto: <?php echo esc_html($cor_footer_texto); ?>;
      <?php endif; ?>
      <?php if ($cor_secoes_bg) : ?>
      --ec-cor-secoes-bg: <?php echo esc_html($cor_secoes_bg); ?>;
      <?php endif; ?>
      <?php if ($cor_secoes_titulo) : ?>
      --ec-cor-secoes-titulo: <?php echo esc_html($cor_secoes_titulo); ?>;
      <?php endif; ?>
    }

    html { font-size: <?php echo (int) $html_percent; ?>%; }

    body{
      color: var(--ec-cor-paragrafo);
      background-color: <?php echo esc_html($bg_cor); ?>;
      <?php if ($bg_image_url) : ?>
      background-image: url('<?php echo esc_url($bg_image_url); ?>');
      background-position: <?php echo esc_html($bg_position); ?>;
      background-size: <?php echo esc_html($bg_size); ?>;
      background-repeat: <?php echo esc_html($bg_repeat); ?>;
      <?php endif; ?>
    }

    h1,h2,h3,h4,h5,h6 { color: var(--ec-cor-titulo); }

    a{ color: var(--ec-cor-link); <?php echo $links_underline ? 'text-decoration: underline;' : 'text-decoration: none;'; ?> }
    a:hover{ color: var(--ec-cor-link-hover); }
    a:active{ color: var(--ec-cor-link-active); }

    .ec-button, button, [type="submit"]{
      <?php echo $botoes_underline ? 'text-decoration: underline;' : 'text-decoration: none;'; ?>
    }

    <?php if ($cor_secoes_titulo) : ?>
    .ec-section h2 { color: <?php echo esc_html($cor_secoes_titulo); ?>; }
    <?php endif; ?>

    <?php if ($cor_header_texto) : ?>
    .site-branding figcaption strong { color: <?php echo esc_html($cor_header_texto); ?>; }
    <?php endif; ?>

    <?php if ($cor_footer_texto) : ?>
    .site-footer p { color: <?php echo esc_html($cor_footer_texto); ?>; }
    <?php endif; ?>
  </style>
  <?php
});