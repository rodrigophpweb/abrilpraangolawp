<?php
get_header();

$cpt_obj    = get_queried_object();
$title      = $cpt_obj ? $cpt_obj->label : 'Programação';
$archive_url = get_post_type_archive_link('programacao');

// Termos da taxonomia agenda para filtro
$agendas = get_terms([
  'taxonomy'   => 'agenda',
  'hide_empty' => true,
]);

// Termo ativo (se filtrado via ?agenda=slug)
$agenda_ativa = isset($_GET['agenda']) ? sanitize_text_field($_GET['agenda']) : '';
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

    <?php if (!empty($agendas) && !is_wp_error($agendas)) : ?>
      <nav class="ec-filter" aria-label="Filtrar por agenda">
        <a
          class="ec-filter__item<?php echo $agenda_ativa === '' ? ' ec-filter__item--active' : ''; ?>"
          href="<?php echo esc_url($archive_url); ?>"
        >Todos</a>
        <?php foreach ($agendas as $term) : ?>
          <a
            class="ec-filter__item<?php echo $agenda_ativa === $term->slug ? ' ec-filter__item--active' : ''; ?>"
            href="<?php echo esc_url(add_query_arg('agenda', $term->slug, $archive_url)); ?>"
          ><?php echo esc_html($term->name); ?></a>
        <?php endforeach; ?>
      </nav>
    <?php endif; ?>

    <?php
    // Query personalizada com filtro por agenda
    $query_args = [
      'post_type'      => 'programacao',
      'posts_per_page' => 12,
      'post_status'    => 'publish',
      'orderby'        => 'meta_value',
      'meta_key'       => 'horario_inicio',
      'order'          => 'ASC',
      'paged'          => max(1, get_query_var('paged')),
    ];

    if ($agenda_ativa) {
      $query_args['tax_query'] = [[
        'taxonomy' => 'agenda',
        'field'    => 'slug',
        'terms'    => $agenda_ativa,
      ]];
    }

    $q = new WP_Query($query_args);
    ?>

    <?php if ($q->have_posts()) : ?>
      <section class="ec-archive__grid ec-archive__grid--programacao" aria-label="Programação">
        <?php while ($q->have_posts()) : $q->the_post();
          $horario_inicio = function_exists('get_field') ? get_field('horario_inicio') : '';
          $horario_fim    = function_exists('get_field') ? get_field('horario_fim') : '';
          $local_prog     = function_exists('get_field') ? get_field('local_programacao') : '';
          $time_label     = trim($horario_inicio . ($horario_fim ? ' — ' . $horario_fim : ''));
          $terms          = get_the_terms(get_the_ID(), 'agenda');
          $term_name      = (is_array($terms) && !empty($terms)) ? $terms[0]->name : '';
        ?>
          <article class="ec-prog-card">
            <?php if ($term_name) : ?>
              <span class="ec-prog-card__badge"><?php echo esc_html($term_name); ?></span>
            <?php endif; ?>

            <h2 class="ec-prog-card__title">
              <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>

            <?php if ($time_label) : ?>
              <p class="ec-prog-card__time">
                <time datetime="<?php echo esc_attr($horario_inicio ?: ''); ?>"><?php echo esc_html($time_label); ?></time>
              </p>
            <?php endif; ?>

            <?php if ($local_prog) : ?>
              <p class="ec-prog-card__local"><?php echo esc_html($local_prog); ?></p>
            <?php endif; ?>

            <?php if (has_excerpt()) : ?>
              <p class="ec-prog-card__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
            <?php else : ?>
              <p class="ec-prog-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_content(), 30, '…')); ?></p>
            <?php endif; ?>

            <a class="ec-button" href="<?php the_permalink(); ?>">Ver detalhes</a>
          </article>
        <?php endwhile; ?>
      </section>

      <?php
      // Paginação
      $GLOBALS['wp_query'] = $q; // necessário para the_posts_pagination
      the_posts_pagination([
        'mid_size'  => 2,
        'prev_text' => '&laquo; Anterior',
        'next_text' => 'Próximo &raquo;',
        'class'     => 'ec-pagination',
      ]);
      wp_reset_query();
      ?>
    <?php else : ?>
      <p>Nenhum item de programação encontrado.</p>
    <?php endif; ?>

    <?php wp_reset_postdata(); ?>

  </div>
</main>

<?php get_footer(); ?>
