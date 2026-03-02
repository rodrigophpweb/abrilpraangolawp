<?php
if (!defined('ABSPATH')) exit;

// Página "Mestre Meinha" (slug)
$pagina = get_page_by_path('mestre-meinha');

if (!$pagina) : ?>
  <section class="ec-section ec-feature">
    <div class="container">
      <header>
        <h2>Mestre Meinha</h2>
      </header>
      <p>Crie uma página com o slug <strong>mestre-meinha</strong> para exibir esta seção.</p>
    </div>
  </section>
  <?php return; endif;

$titulo   = get_the_title($pagina);
$link     = get_permalink($pagina);
$img_html = get_the_post_thumbnail($pagina->ID, 'large', ['loading' => 'lazy', 'decoding' => 'async']);

$texto = wp_strip_all_tags($pagina->post_content);
$excerpt = mb_substr(trim($texto), 0, 420);
if (mb_strlen($texto) > 420) $excerpt .= '…';
?>

<section class="ec-section ec-feature ec-feature--mestre" id="mestre-meinha">
  <div class="container ec-feature__grid">
    <figure class="ec-feature__media">
      <?php if ($img_html) : ?>
        <?php echo $img_html; ?>
      <?php else : ?>
        <div class="ec-feature__placeholder" aria-hidden="true"></div>
      <?php endif; ?>
    </figure>

    <article class="ec-feature__content">
      <header>
        <h2><?php echo esc_html($titulo); ?></h2>
      </header>

      <?php echo get_the_content( $more_link_text = null, $strip_teaser = false, $post = $pagina ); ?>

      <p>
        <a class="ec-button ec-button--primary" href="<?php echo esc_url($link); ?>">
          Saiba mais
        </a>
      </p>
    </article>
  </div>
</section>