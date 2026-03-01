<?php
if (!defined('ABSPATH')) exit;

$home_id = get_queried_object_id();

// Dados do evento (ACF Options)
$data_inicio = function_exists('get_field') ? get_field('data_inicio_evento', 'option') : '';
$data_fim    = function_exists('get_field') ? get_field('data_fim_evento', 'option') : '';

$inicio_fmt = function_exists('ec_format_date') ? ec_format_date($data_inicio, 'd M Y') : '';
$fim_fmt    = function_exists('ec_format_date') ? ec_format_date($data_fim, 'd M Y') : '';

$periodo_legivel = trim($inicio_fmt . ($fim_fmt ? ' — ' . $fim_fmt : ''));

// Background: imagem destacada da Home
$bg_url = get_the_post_thumbnail_url($home_id, 'full');

// Link dos botões (você pode mudar depois para buscar páginas específicas)
$link_agenda   = home_url('/grade-do-evento/');
$link_ingresso = home_url('/pacotes/');

// Timestamp final para countdown (fim do evento às 23:59:59)
$end_ts = '';
if ($data_fim) {
  $end_ts = strtotime($data_fim . ' 23:59:59');
}
?>

<section class="ec-hero" <?php echo $bg_url ? 'style="--ec-hero-bg:url(' . esc_url($bg_url) . ');"' : ''; ?>>
  <div class="ec-hero__overlay" aria-hidden="true"></div>

  <div class="container ec-hero__grid">
    <header class="ec-hero__content">
      <h1 class="ec-hero__title"><?php echo esc_html(get_bloginfo('name')); ?></h1>

      <?php if ($periodo_legivel) : ?>
        <p class="ec-hero__date">
          <time datetime="<?php echo esc_attr($data_inicio ?: ''); ?>">
            <?php echo esc_html($periodo_legivel); ?>
          </time>
        </p>
      <?php endif; ?>

      <div class="ec-countdown" data-countdown="<?php echo esc_attr($end_ts ?: ''); ?>">
        <p class="ec-countdown__label">Contagem regressiva</p>
        <div class="ec-countdown__grid" aria-live="polite">
          <div class="ec-countdown__item"><strong data-dd>--</strong><span>dias</span></div>
          <div class="ec-countdown__item"><strong data-hh>--</strong><span>horas</span></div>
          <div class="ec-countdown__item"><strong data-mm>--</strong><span>min</span></div>
          <div class="ec-countdown__item"><strong data-ss>--</strong><span>seg</span></div>
        </div>
      </div>

      <div class="ec-hero__actions">
        <a class="ec-button" href="<?php echo esc_url($link_agenda); ?>">Ver grade</a>
        <a class="ec-button ec-button--primary" href="<?php echo esc_url($link_ingresso); ?>">Ingressos</a>
      </div>
    </header>
  </div>
</section>