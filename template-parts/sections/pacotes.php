<?php
if (!defined('ABSPATH')) exit;

// Página "Pacotes" (slug)
$pagina = get_page_by_path('pacotes');

$titulo = $pagina ? get_the_title($pagina) : 'Pacotes';
$conteudo = $pagina ? apply_filters('the_content', $pagina->post_content) : '';

// Query CPT ingressos
$q = new WP_Query([
  'post_type'      => 'ingressos',
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'orderby'        => 'menu_order title',
  'order'          => 'ASC',
]);
?>

<section class="ec-section ec-pacotes" id="pacotes">
  <div class="container">
    <header class="ec-pacotes__header">
      <h2><?php echo esc_html($titulo); ?></h2>
      <?php if ($conteudo) : ?>
        <div class="ec-pacotes__intro"><?php echo $conteudo; ?></div>
      <?php endif; ?>
    </header>

    <?php if (!$q->have_posts()) : ?>
      <p>Ainda não temos pacotes definidos.</p>
    <?php else : ?>
      <section class="ec-pacotes__grid" aria-label="Pacotes e ingressos">
        <?php while ($q->have_posts()) : $q->the_post(); ?>
          <?php
            get_template_part('template-parts/components/card-ingresso', null, [
              'post' => get_post(),
            ]);
          ?>
        <?php endwhile; ?>
      </section>
    <?php endif; ?>
  </div>
</section>

<?php wp_reset_postdata(); ?>