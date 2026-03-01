<?php
get_header();
?>
<main class="site-main">
  <section class="container">
    <header>
      <h1><?php bloginfo('name'); ?></h1>
      <p><?php bloginfo('description'); ?></p>
    </header>

    <?php if (have_posts()) : ?>
      <?php while (have_posts()) : the_post(); ?>
        <article <?php post_class(); ?>>
          <header>
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
          </header>
          <div>
            <?php the_excerpt(); ?>
          </div>
        </article>
      <?php endwhile; ?>
    <?php else : ?>
      <p>Nenhum conteúdo encontrado.</p>
    <?php endif; ?>
  </section>
</main>
<?php
get_footer();