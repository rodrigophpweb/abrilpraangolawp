<?php
get_header();

$id            = get_the_ID();
$preco_avista  = function_exists('get_field') ? (string) get_field('preco_a_vista', $id) : '';
$preco_cartao  = function_exists('get_field') ? (string) get_field('preco_cartao_credito', $id) : '';
$validade      = function_exists('get_field') ? (string) get_field('validade_ticket', $id) : '';
$chave_pix     = function_exists('get_field') ? (string) get_field('chave_pix', $id) : '';
$link_cartao   = function_exists('get_field') ? (string) get_field('link_pagamento_cartao', $id) : '';
$descricao     = function_exists('get_field') ? (string) get_field('descricao_curta', $id) : '';
$validade_fmt  = function_exists('ec_format_date') ? ec_format_date($validade, 'd/m/Y') : '';
$archive_url   = get_post_type_archive_link('ingressos');
?>

<main class="site-main">
  <div class="container">

    <nav class="ec-breadcrumb" aria-label="Breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
      <span aria-hidden="true">›</span>
      <a href="<?php echo esc_url($archive_url); ?>">Ingressos</a>
      <span aria-hidden="true">›</span>
      <span aria-current="page"><?php the_title(); ?></span>
    </nav>

    <?php while (have_posts()) : the_post(); ?>
      <article class="ec-single">

        <div class="ec-single__grid<?php echo has_post_thumbnail() ? '' : ' ec-single__grid--full'; ?>">

          <?php if (has_post_thumbnail()) : ?>
            <figure class="ec-single__media">
              <?php the_post_thumbnail('large', ['loading' => 'lazy', 'decoding' => 'async']); ?>
            </figure>
          <?php endif; ?>

          <div class="ec-single__content">
            <h1 class="ec-single__title"><?php the_title(); ?></h1>

            <?php if ($descricao) : ?>
              <p class="ec-single__excerpt"><?php echo esc_html($descricao); ?></p>
            <?php endif; ?>

            <div class="ec-single__text">
              <?php the_content(); ?>
            </div>

            <!-- Bloco de pagamento -->
            <aside class="ec-payment" aria-label="Informações de pagamento">

              <?php if ($preco_avista || $preco_cartao) : ?>
                <div class="ec-payment__prices">
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
                </div>
              <?php endif; ?>

              <?php if ($validade && $validade_fmt) : ?>
                <p class="ec-payment__validity">
                  Válido até:
                  <time datetime="<?php echo esc_attr($validade); ?>"><?php echo esc_html($validade_fmt); ?></time>
                </p>
              <?php endif; ?>

              <div class="ec-payment__actions">
                <?php if ($chave_pix) : ?>
                  <div class="ec-payment__pix">
                    <p class="ec-payment__pix-key">
                      Chave Pix: <code><?php echo esc_html($chave_pix); ?></code>
                    </p>
                    <button
                      type="button"
                      class="ec-button ec-button--pix"
                      data-copy="<?php echo esc_attr($chave_pix); ?>"
                      aria-label="Copiar chave Pix"
                    >
                      Copiar Pix
                    </button>
                    <p class="ec-pix__hint" aria-live="polite"></p>
                  </div>
                <?php endif; ?>

                <?php if ($link_cartao) : ?>
                  <a
                    class="ec-button ec-button--primary"
                    href="<?php echo esc_url($link_cartao); ?>"
                    target="_blank"
                    rel="noopener"
                  >
                    Pagar no cartão
                  </a>
                <?php endif; ?>
              </div>

            </aside>

          </div>
        </div>

        <footer class="ec-single__footer">
          <a class="ec-button" href="<?php echo esc_url($archive_url); ?>">&laquo; Voltar para pacotes</a>
        </footer>

      </article>
    <?php endwhile; ?>

  </div>
</main>

<?php get_footer(); ?>
