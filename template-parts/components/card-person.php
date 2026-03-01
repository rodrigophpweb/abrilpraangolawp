<?php
if (!defined('ABSPATH')) exit;

/**
 * Espera:
 * $args['post'] = WP_Post
 * $args['button_label'] = string (opcional)
 */
$p = $args['post'] ?? null;
$button = $args['button_label'] ?? 'Ver detalhes';

if (!$p instanceof WP_Post) return;

$link = get_permalink($p->ID);
$title = get_the_title($p->ID);
$img = get_the_post_thumbnail($p->ID, 'medium', [
  'loading' => 'lazy',
  'decoding' => 'async',
]);
?>

<article class="ec-card">
  <figure class="ec-card__figure">
    <?php if ($img) : ?>
      <?php echo $img; ?>
    <?php else : ?>
      <div class="ec-card__placeholder" aria-hidden="true"></div>
    <?php endif; ?>

    <figcaption class="ec-card__caption">
      <h3 class="ec-card__title"><?php echo esc_html($title); ?></h3>
    </figcaption>
  </figure>

  <p class="ec-card__actions">
    <a class="ec-button" href="<?php echo esc_url($link); ?>">
      <?php echo esc_html($button); ?>
    </a>
  </p>
</article>