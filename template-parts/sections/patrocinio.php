<?php
if (!defined('ABSPATH')) exit;

// Página "Apoio e Patrocínio" (slug)
$pagina = get_page_by_path('apoio-e-patrocinio');

if (!$pagina) : ?>
  <section class="ec-section ec-patrocinio" id="patrocinio">
    <div class="container">
      <p>Crie uma página com o slug <strong>apoio-e-patrocinio</strong> para exibir esta seção.</p>
    </div>
  </section>
<?php return; endif;

$titulo   = get_the_title($pagina);
$conteudo = apply_filters('the_content', $pagina->post_content);

// Query CPT patrocinadores
$q = new WP_Query([
  'post_type'      => 'patrocinadores',
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'orderby'        => ['menu_order' => 'ASC', 'title' => 'ASC'],
]);
?>

<section class="ec-section ec-patrocinio" id="patrocinio">
  <div class="container">

    <header class="ec-patrocinio__header">
      <h2><?php echo esc_html($titulo); ?></h2>
      <?php if ($conteudo) : ?>
        <div class="ec-patrocinio__intro"><?php echo $conteudo; ?></div>
      <?php endif; ?>
    </header>

    <?php if (!$q->have_posts()) : ?>
      <p>Nenhum patrocinador cadastrado ainda.</p>
    <?php else : ?>

      <div class="swiper ec-swiper" aria-label="Carrossel de patrocinadores">
        <div class="swiper-wrapper">
          <?php while ($q->have_posts()) : $q->the_post();
            $link  = function_exists('get_field') ? get_field('link_site') : '';
            $thumb = get_the_post_thumbnail_url(get_the_ID(), 'medium');
            $nome  = get_the_title();
          ?>
            <div class="swiper-slide">
              <figure class="ec-patrocinio__card">
                <?php if ($thumb) : ?>
                  <?php if ($link) : ?>
                    <a href="<?php echo esc_url($link); ?>" target="_blank" rel="noopener noreferrer">
                      <img
                        src="<?php echo esc_url($thumb); ?>"
                        alt="<?php echo esc_attr($nome); ?>"
                        loading="lazy"
                        width="200"
                        height="200"
                      />
                    </a>
                  <?php else : ?>
                    <img
                      src="<?php echo esc_url($thumb); ?>"
                      alt="<?php echo esc_attr($nome); ?>"
                      loading="lazy"
                      width="200"
                      height="200"
                    />
                  <?php endif; ?>
                <?php else : ?>
                  <span class="ec-patrocinio__placeholder" aria-hidden="true"></span>
                <?php endif; ?>

                <figcaption class="ec-patrocinio__name">
                  <?php echo esc_html($nome); ?>
                </figcaption>
              </figure>
            </div>
          <?php endwhile; ?>
        </div>

        <div class="swiper-pagination"></div>
        <button class="swiper-button-prev" type="button" aria-label="Anterior"></button>
        <button class="swiper-button-next" type="button" aria-label="Próximo"></button>
      </div>

    <?php endif; ?>

  </div>
</section>

<?php wp_reset_postdata(); ?>
