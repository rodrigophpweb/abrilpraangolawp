<?php
if (!defined('ABSPATH')) exit;

// Página "Local do Evento" (slug)
$pagina = get_page_by_path('local-do-evento');

if (!$pagina) : ?>
  <section class="ec-section ec-local" id="local-do-evento">
    <div class="container">
      <p>Crie uma página com o slug <strong>local-do-evento</strong> para exibir esta seção.</p>
    </div>
  </section>
<?php return; endif;

$titulo   = get_the_title($pagina);
$conteudo = apply_filters('the_content', $pagina->post_content);

// Campos ACF Options
$local_evento = function_exists('get_field')
  ? get_field('local_evento', 'option')
  : '';

$maps_iframe = function_exists('get_field')
  ? get_field('google_maps_iframe', 'option')
  : '';

// Link "Como chegar"
$maps_url = $local_evento
  ? ec_google_maps_url($local_evento)
  : '';

// Allowlist para iframe do Maps
$allowed_iframe = [
  'iframe' => [
    'src'             => [],
    'width'           => [],
    'height'          => [],
    'style'           => [],
    'allowfullscreen' => [],
    'loading'         => [],
    'referrerpolicy'  => [],
    'title'           => [],
    'aria-label'      => [],
  ],
];
?>

<section class="ec-section ec-local" id="local-do-evento">
  <a id="local" aria-hidden="true"></a>
  <div class="container">

    <div class="ec-local__grid">

      <article class="ec-local__content">
        <header class="ec-local__header">
          <h2><?php echo esc_html($titulo); ?></h2>
        </header>

        <div class="ec-local__text">
          <?php echo $conteudo; ?>
        </div>
      </article>

      <aside class="ec-local__aside" aria-label="Localização e mapa">

        <?php if ($local_evento) : ?>
          <div class="ec-local__info">
            <h3>Localização</h3>
            <p><?php echo esc_html($local_evento); ?></p>
          </div>
        <?php endif; ?>

        <?php if ($maps_iframe) : ?>
          <figure class="ec-local__map" aria-label="Mapa do local do evento">
            <?php echo wp_kses($maps_iframe, $allowed_iframe); ?>
          </figure>
        <?php endif; ?>

        <?php if ($maps_url) : ?>
          <a
            href="<?php echo esc_url($maps_url); ?>"
            class="ec-button ec-button--primary ec-local__cta"
            target="_blank"
            rel="noopener"
          >
            Como chegar
          </a>
        <?php endif; ?>

      </aside>

    </div>

  </div>
</section>
