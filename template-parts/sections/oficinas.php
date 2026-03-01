<?php
if (!defined('ABSPATH')) exit;

// Página "Oficinas" (slug)
$pagina = get_page_by_path('oficinas');

$titulo = $pagina ? get_the_title($pagina) : 'Oficinas';
$conteudo = $pagina ? apply_filters('the_content', $pagina->post_content) : '';

// Query CPT oficineiros
$q = new WP_Query([
  'post_type'      => 'oficineiros',
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'orderby'        => 'title',
  'order'          => 'ASC',
]);
?>

<section class="ec-section ec-cards" id="oficinas">
  <div class="container">
    <header class="ec-cards__header">
      <h2><?php echo esc_html($titulo); ?></h2>
      <?php if ($conteudo) : ?>
        <div class="ec-cards__intro"><?php echo $conteudo; ?></div>
      <?php endif; ?>
    </header>

    <?php if (!$q->have_posts()) : ?>
      <p>Ainda não temos oficineiros definidos.</p>
    <?php else : ?>
      <section class="ec-cards__grid" aria-label="Oficineiros">
        <?php while ($q->have_posts()) : $q->the_post(); ?>
          <?php
            get_template_part('template-parts/components/card-person', null, [
              'post' => get_post(),
              'button_label' => 'Ver oficineiro',
            ]);
          ?>
        <?php endwhile; ?>
      </section>
    <?php endif; ?>

  </div>
</section>

<?php wp_reset_postdata(); ?>