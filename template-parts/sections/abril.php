<?php
if (!defined('ABSPATH')) exit;

// Página "Abril pra Angola" (vincular por slug)
$pagina = get_page_by_path('abril-pra-angola');

if (!$pagina) {
  ?>
  <section class="ec-section ec-abril">
    <div class="container">
      <header>
        <h2>Abril pra Angola</h2>
      </header>
      <p>Crie uma página com o slug <strong>abril-pra-angola</strong> para exibir esta seção.</p>
    </div>
  </section>
  <?php
  return;
}

// Conteúdo da página
$titulo = get_the_title($pagina);
$conteudo = apply_filters('the_content', $pagina->post_content);

// Dados do evento (Options)
$data_inicio = function_exists('get_field') ? get_field('data_inicio_evento', 'option') : '';
$data_fim    = function_exists('get_field') ? get_field('data_fim_evento', 'option') : '';
$local       = function_exists('get_field') ? get_field('local_evento', 'option') : '';

// Linha "Quinta a Domingo" calculada a partir das datas
$dia_inicio = function_exists('ec_weekday') ? ec_weekday($data_inicio) : '';
$dia_fim    = function_exists('ec_weekday') ? ec_weekday($data_fim) : '';

$faixa_dias = '';
if ($dia_inicio && $dia_fim) {
  $faixa_dias = mb_convert_case($dia_inicio, MB_CASE_TITLE, 'UTF-8') . ' a ' . mb_convert_case($dia_fim, MB_CASE_TITLE, 'UTF-8');
}

// Datas formatadas
$inicio_fmt = function_exists('ec_format_date') ? ec_format_date($data_inicio, 'd/m/Y') : '';
$fim_fmt    = function_exists('ec_format_date') ? ec_format_date($data_fim, 'd/m/Y') : '';
$periodo = trim($inicio_fmt . ($fim_fmt ? ' — ' . $fim_fmt : ''));
?>

<section class="ec-section ec-abril" id="abril">
  <div class="container ec-abril__grid">
    <header class="ec-abril__header">
      <h2><?php echo esc_html($titulo); ?></h2>
    </header>

    <article class="ec-abril__content">
      <?php echo $conteudo; ?>
    </article>

    <aside class="ec-abril__meta" aria-label="Informações do evento">
      <div class="ec-abril__card">
        <h3>Localização</h3>
        <p><?php echo esc_html($local ?: 'Defina o local em Config. do Evento.'); ?></p>
      </div>

      <div class="ec-abril__card">
        <h3>Quando</h3>
        <?php if ($faixa_dias) : ?>
          <p class="ec-abril__days"><?php echo esc_html($faixa_dias); ?></p>
        <?php endif; ?>

        <?php if ($periodo) : ?>
          <p class="ec-abril__period">
            <time datetime="<?php echo esc_attr($data_inicio ?: ''); ?>">
              <?php echo esc_html($periodo); ?>
            </time>
          </p>
        <?php else : ?>
          <p>Defina datas em Config. do Evento.</p>
        <?php endif; ?>
      </div>
    </aside>
  </div>
</section>