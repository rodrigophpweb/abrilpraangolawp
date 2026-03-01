<?php
get_header();

$cpt_obj = get_queried_object();
$title   = $cpt_obj ? $cpt_obj->label : 'Oficineiros';
?>

<main class="site-main">
  <div class="container">

    <nav class="ec-breadcrumb" aria-label="Breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
      <span aria-hidden="true">›</span>
      <span aria-current="page"><?php echo esc_html($title); ?></span>
    </nav>

    <header class="ec-archive__header">
      <h1><?php echo esc_html($title); ?></h1>
    </header>

    <?php if (have_posts()) : ?>
      <section class="ec-archive__grid ec-archive__grid--people" aria-label="<?php echo esc_attr($title); ?>">
        <?php while (have_posts()) : the_post(); ?>
          <?php get_template_part('template-parts/components/card-person', null, [
            'post'         => get_post(),
            'button_label' => 'Ver oficineiro',
          ]); ?>
        <?php endwhile; ?>
      </section>

      <?php the_posts_pagination([
        'mid_size'  => 2,
        'prev_text' => '&laquo; Anterior',
        'next_text' => 'Próximo &raquo;',
        'class'     => 'ec-pagination',
      ]); ?>
    <?php else : ?>
      <p>Nenhum oficineiro cadastrado ainda.</p>
    <?php endif; ?>

  </div>
</main>

<?php get_footer(); ?>
