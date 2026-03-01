<?php
if (!defined('ABSPATH')) exit;

// Página "Evento" (slug)
$pagina = get_page_by_path('evento');

if (!$pagina) : ?>
  <section class="ec-section ec-feature">
    <div class="container">
      <header>
        <h2>O Evento</h2>
      </header>
      <p>Crie uma página com o slug <strong>evento</strong> para exibir esta seção.</p>
    </div>
  </section>
  <?php return; endif;

$link = get_permalink($pagina);
$img_html = get_the_post_thumbnail($pagina->ID, 'large', ['loading' => 'lazy', 'decoding' => 'async']);

// Título editável (ACF) com fallback para título da página
$titulo_editavel = function_exists('get_field') ? get_field('titulo_secao_home', $pagina->ID) : '';
$titulo = $titulo_editavel ?: get_the_title($pagina);

// Conteúdo em parágrafos (conforme spec) + botão saiba mais
$conteudo = apply_filters('the_content', $pagina->post_content);
?>

<section class="ec-section ec-feature ec-feature--evento" id="evento">
  <div class="container ec-feature__grid ec-feature__grid--reverse">
    <article class="ec-feature__content">
      <header>
        <h2><?php echo esc_html($titulo); ?></h2>
      </header>

      <div class="ec-feature__text">
        <?php echo $conteudo; ?>
      </div>

      <p>
        <a class="ec-button ec-button--primary" href="<?php echo esc_url($link); ?>">
          Saiba mais
        </a>
      </p>
    </article>

    <figure class="ec-feature__media">
      <?php if ($img_html) : ?>
        <?php echo $img_html; ?>
      <?php else : ?>
        <div class="ec-feature__placeholder" aria-hidden="true"></div>
      <?php endif; ?>
    </figure>
  </div>
</section>