<?php
get_header();

$home_id    = get_queried_object_id();
$has_acf    = function_exists('get_field');

/**
 * Seções da Home — ordem e toggles declarativos.
 *
 * 'toggle'   → nome do campo ACF true/false na página Home.
 * 'template' → caminho relativo para get_template_part().
 *
 * Para adicionar/remover/reordenar seções, basta editar este array.
 */
$sections = [
  ['toggle' => 'mostrar_hero',          'template' => 'template-parts/sections/hero'],
  ['toggle' => 'mostrar_abril',         'template' => 'template-parts/sections/abril'],
  ['toggle' => 'mostrar_mestre_meinha', 'template' => 'template-parts/sections/mestre-meinha'],
  ['toggle' => 'mostrar_evento',        'template' => 'template-parts/sections/evento'],
  ['toggle' => 'mostrar_grade',         'template' => 'template-parts/sections/grade'],
  ['toggle' => 'mostrar_oficinas',      'template' => 'template-parts/sections/oficinas'],
  ['toggle' => 'mostrar_presencas',     'template' => 'template-parts/sections/presencas'],
  ['toggle' => 'mostrar_pacotes',       'template' => 'template-parts/sections/pacotes'],
  ['toggle' => 'mostrar_local',         'template' => 'template-parts/sections/local'],
  ['toggle' => 'mostrar_seguranca',    'template' => 'template-parts/sections/seguranca'],
  ['toggle' => 'mostrar_patrocinio',   'template' => 'template-parts/sections/patrocinio'],
];
?>

<main class="site-main">
  <?php
  foreach ($sections as $section) :
    $visible = $has_acf ? (bool) get_field($section['toggle'], $home_id) : true;

    if ($visible) :
      get_template_part($section['template']);
    endif;
  endforeach;
  ?>
</main>

<?php
get_footer();