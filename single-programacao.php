<?php
get_header();

$archive_url    = get_post_type_archive_link('programacao');
$horario_inicio = function_exists('get_field') ? get_field('horario_inicio') : '';
$horario_fim    = function_exists('get_field') ? get_field('horario_fim') : '';
$local_prog     = function_exists('get_field') ? get_field('local_programacao') : '';
$time_label     = trim($horario_inicio . ($horario_fim ? ' — ' . $horario_fim : ''));
$terms          = get_the_terms(get_the_ID(), 'agenda');
$term_name      = (is_array($terms) && !empty($terms)) ? $terms[0]->name : '';
?>

<main class="site-main">
  <div class="container">

    <nav class="ec-breadcrumb" aria-label="Breadcrumb">
      <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
      <span aria-hidden="true">›</span>
      <a href="<?php echo esc_url($archive_url); ?>">Programação</a>
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

            <dl class="ec-single__meta">
              <?php if ($term_name) : ?>
                <div class="ec-single__meta-item">
                  <dt>Agenda</dt>
                  <dd><span class="ec-prog-card__badge"><?php echo esc_html($term_name); ?></span></dd>
                </div>
              <?php endif; ?>

              <?php if ($time_label) : ?>
                <div class="ec-single__meta-item">
                  <dt>Horário</dt>
                  <dd><time datetime="<?php echo esc_attr($horario_inicio ?: ''); ?>"><?php echo esc_html($time_label); ?></time></dd>
                </div>
              <?php endif; ?>

              <?php if ($local_prog) : ?>
                <div class="ec-single__meta-item">
                  <dt>Local</dt>
                  <dd><?php echo esc_html($local_prog); ?></dd>
                </div>
              <?php endif; ?>
            </dl>

            <div class="ec-single__text">
              <?php the_content(); ?>
            </div>
          </div>
        </div>

        <footer class="ec-single__footer">
          <a class="ec-button" href="<?php echo esc_url($archive_url); ?>">&laquo; Voltar para programação</a>
        </footer>

      </article>
    <?php endwhile; ?>

  </div>
</main>

<?php get_footer(); ?>
