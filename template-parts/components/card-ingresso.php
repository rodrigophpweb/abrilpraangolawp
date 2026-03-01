<?php
if (!defined('ABSPATH')) exit;

/**
 * Espera:
 * $args['post'] = WP_Post
 */
$p = $args['post'] ?? null;
if (!$p instanceof WP_Post) return;

$id = $p->ID;
$link = get_permalink($id);
$title = get_the_title($id);

$preco_avista = function_exists('get_field') ? (string) get_field('preco_a_vista', $id) : '';
$preco_cartao = function_exists('get_field') ? (string) get_field('preco_cartao_credito', $id) : '';
$validade     = function_exists('get_field') ? (string) get_field('validade_ticket', $id) : '';
$chave_pix    = function_exists('get_field') ? (string) get_field('chave_pix', $id) : '';
$link_cartao  = function_exists('get_field') ? (string) get_field('link_pagamento_cartao', $id) : '';

$img = get_the_post_thumbnail($id, 'medium', [
  'loading' => 'lazy',
  'decoding' => 'async',
]);

$validade_fmt = function_exists('ec_format_date') ? ec_format_date($validade, 'd/m/Y') : '';
?>

<article class="ec-card ec-card--ingresso">
  <header class="ec-card__header">
    <h3 class="ec-card__title"><?php echo esc_html($title); ?></h3>
  </header>

  <?php if ($img) : ?>
    <figure class="ec-card__figure">
      <?php echo $img; ?>
    </figure>
  <?php endif; ?>

  <div class="ec-ingresso__details">
    <?php if ($preco_avista) : ?>
      <p class="ec-tag" aria-label="Preço à vista">
        À vista: <strong><?php echo esc_html($preco_avista); ?></strong>
      </p>
    <?php endif; ?>

    <?php if ($preco_cartao) : ?>
      <p class="ec-tag" aria-label="Preço no cartão">
        Cartão: <strong><?php echo esc_html($preco_cartao); ?></strong>
      </p>
    <?php endif; ?>

    <?php if ($validade && $validade_fmt) : ?>
      <p class="ec-ingresso__validade">
        Válido até:
        <time datetime="<?php echo esc_attr($validade); ?>">
          <?php echo esc_html($validade_fmt); ?>
        </time>
      </p>
    <?php endif; ?>
  </div>

  <footer class="ec-ingresso__actions">
    <a class="ec-button" href="<?php echo esc_url($link); ?>">Ver pacote</a>

    <?php if ($link_cartao) : ?>
      <a class="ec-button ec-button--primary" href="<?php echo esc_url($link_cartao); ?>" target="_blank" rel="noopener">
        Pagar no cartão
      </a>
    <?php endif; ?>

    <?php if ($chave_pix) : ?>
      <button
        type="button"
        class="ec-button ec-button--pix"
        data-copy="<?php echo esc_attr($chave_pix); ?>"
        aria-label="Copiar chave Pix"
      >
        Copiar Pix
      </button>
      <p class="ec-pix__hint" aria-live="polite"></p>
    <?php endif; ?>
  </footer>
</article>  