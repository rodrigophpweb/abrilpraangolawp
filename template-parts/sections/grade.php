<?php
if (!defined('ABSPATH')) exit;

// Página "Grade do Evento" (slug)
$pagina = get_page_by_path('grade-do-evento');

if (!$pagina) : ?>
  <section class="ec-section ec-grade" id="grade">
    <div class="container">
      <header>
        <h2>Grade do Evento</h2>
      </header>
      <p>Crie uma página com o slug <strong>grade-do-evento</strong> para exibir esta seção.</p>
    </div>
  </section>
  <?php return; endif;

$titulo = get_the_title($pagina);
$conteudo = apply_filters('the_content', $pagina->post_content);

// Buscar itens de Programação (ordenar por horário_inicio)
$query = new WP_Query([
  'post_type'      => 'programacao',
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'orderby'        => 'meta_value',
  'meta_key'       => 'horario_inicio',
  'order'          => 'ASC',
]);

// Agrupar por termo "agenda"
$grupos = []; // [term_id => ['term' => WP_Term|string, 'items' => [WP_Post...]]]

if ($query->have_posts()) {
  foreach ($query->posts as $post) {
    $terms = get_the_terms($post->ID, 'agenda');

    if (is_array($terms) && !empty($terms)) {
      // Se tiver múltiplos, pegamos o primeiro por simplicidade
      $term = $terms[0];
      $key = 'term_' . $term->term_id;

      if (!isset($grupos[$key])) {
        $grupos[$key] = ['term' => $term, 'items' => []];
      }
      $grupos[$key]['items'][] = $post;

    } else {
      $key = 'term_none';
      if (!isset($grupos[$key])) {
        $grupos[$key] = ['term' => 'Sem agenda', 'items' => []];
      }
      $grupos[$key]['items'][] = $post;
    }
  }
}
wp_reset_postdata();
?>

<section class="ec-section ec-grade" id="grade-do-evento">
  <div class="container">
    <header class="ec-grade__header">
      <h2><?php echo esc_html($titulo); ?></h2>
      <div class="ec-grade__intro">
        <?php echo $conteudo; ?>
      </div>
    </header>

    <?php if (empty($grupos)) : ?>
      <p>A programação ainda não foi cadastrada.</p>
    <?php else : ?>
      <article class="ec-grade__grid" aria-label="Programação do evento">
        <?php foreach ($grupos as $grupo) : ?>
          <?php foreach ($grupo['items'] as $p) : ?>
            <?php
              $term_label = is_object($grupo['term']) ? $grupo['term']->name : (string) $grupo['term'];

              $horario_inicio = function_exists('get_field') ? get_field('horario_inicio', $p->ID) : '';
              $horario_fim    = function_exists('get_field') ? get_field('horario_fim', $p->ID) : '';
              $local_prog     = function_exists('get_field') ? get_field('local_programacao', $p->ID) : '';

              $time_label = trim($horario_inicio . ($horario_fim ? ' — ' . $horario_fim : ''));
              $link = get_permalink($p->ID);
            ?>
            <section class="ec-grade__card">
              <header class="ec-grade__card-header">
                <h3><?php echo esc_html($term_label); ?></h3>
                <strong class="ec-grade__title">
                  <a href="<?php echo esc_url($link); ?>"><?php echo esc_html(get_the_title($p)); ?></a>
                </strong>

                <?php if ($time_label) : ?>
                  <p class="ec-grade__time">
                    <time datetime="<?php echo esc_attr($horario_inicio ?: ''); ?>">
                      <?php echo esc_html($time_label); ?>
                    </time>
                  </p>
                <?php endif; ?>

                <?php if ($local_prog) : ?>
                  <p class="ec-grade__local"><?php echo esc_html($local_prog); ?></p>
                <?php endif; ?>
              </header>

              <div class="ec-grade__content">
                <?php
                  // Conteúdo do post da Programação (parágrafos)
                  echo apply_filters('the_content', $p->post_content);
                ?>
              </div>
            </section>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </article>
    <?php endif; ?>
  </div>
</section>