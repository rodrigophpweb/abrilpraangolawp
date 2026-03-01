<?php
get_header();

$archive_url = get_post_type_archive_link('oficineiros');
?>

<main class="site-main">
  <div class="container">

    <nav class="ec-breadcrumb" aria-label="Breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
      <span aria-hidden="true">›</span>
      <a href="<?php echo esc_url($archive_url); ?>">Oficineiros</a>
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

            <div class="ec-single__text">
              <?php the_content(); ?>
            </div>
          </div>
        </div>

        <footer class="ec-single__footer">
          <a class="ec-button" href="<?php echo esc_url($archive_url); ?>">&laquo; Voltar para oficineiros</a>
        </footer>

      </article>
    <?php endwhile; ?>

  </div>
</main>

<?php get_footer(); ?>
