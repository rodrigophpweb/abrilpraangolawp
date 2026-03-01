<?php
if (!defined('ABSPATH')) exit;

// Página "Presenças" (slug)
$pagina = get_page_by_path('presencas');

$titulo = $pagina ? get_the_title($pagina) : 'Presenças confirmadas';
$conteudo = $pagina ? apply_filters('the_content', $pagina->post_content) : '';

// Query CPT convidados
$q = new WP_Query([
  'post_type'      => 'convidados',
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'orderby'        => 'title',
  'order'          => 'ASC',
]);
?>

<section class="ec-section ec-cards" id="presencas">
  <div class="container">
    <header class="ec-cards__header">
      <h2><?php echo esc_html($titulo); ?></h2>
      <?php if ($conteudo) : ?>
        <div class="ec-cards__intro"><?php echo $conteudo; ?></div>
      <?php endif; ?>
    </header>

    <?php if (!$q->have_posts()) : ?>
      <p>Ainda não temos convidados definidos.</p>
    <?php else : ?>
      <section class="ec-cards__grid" aria-label="Convidados confirmados">
        <?php while ($q->have_posts()) : $q->the_post(); ?>
          <?php
            get_template_part('template-parts/components/card-person', null, [
              'post' => get_post(),
              'button_label' => 'Ver convidado',
            ]);
          ?>
        <?php endwhile; ?>
      </section>
    <?php endif; ?>

  </div>
</section>

<?php wp_reset_postdata(); ?>