<?php
if (!defined('ABSPATH')) exit;

// Campos ACF Options
$intro = function_exists('get_field') ? get_field('seguranca_intro', 'option') : '';
$itens = function_exists('get_field') ? get_field('seguranca_itens', 'option') : [];

if (!is_array($itens)) $itens = [];
?>

<section class="ec-section ec-seguranca" id="seguranca">
  <div class="container">

    <header class="ec-seguranca__header">
      <h2>Segurança</h2>
      <?php if ($intro) : ?>
        <div class="ec-seguranca__intro"><?php echo wp_kses_post($intro); ?></div>
      <?php endif; ?>
    </header>

    <?php if (empty($itens)) : ?>
      <p>Cadastre itens de segurança em Config. do Evento.</p>
    <?php else : ?>
      <ul class="ec-seguranca__grid" role="list">
        <?php foreach ($itens as $item) :
          $titulo    = $item['titulo'] ?? '';
          $descricao = $item['descricao'] ?? '';
          if (!$titulo && !$descricao) continue;
        ?>
          <li class="ec-seguranca__card">
            <?php if ($titulo) : ?>
              <h3 class="ec-seguranca__card-title"><?php echo esc_html($titulo); ?></h3>
            <?php endif; ?>
            <?php if ($descricao) : ?>
              <p class="ec-seguranca__card-desc"><?php echo esc_html($descricao); ?></p>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

  </div>
</section>
