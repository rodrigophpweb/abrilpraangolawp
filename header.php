<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
  <div class="container">
    <figure class="site-branding">
      <a href="<?php echo esc_url(home_url('/')); ?>">
        <?php if (has_custom_logo()) : ?>
          <?php the_custom_logo(); ?>
        <?php else : ?>
          <figcaption>
            <strong><?php bloginfo('name'); ?></strong>
          </figcaption>
        <?php endif; ?>
      </a>
    </figure>

    <nav class="site-nav" aria-label="Menu principal">
      <?php
      wp_nav_menu([
        'theme_location' => 'menu_principal',
        'container'      => false,
        'fallback_cb'    => false,
      ]);
      ?>
    </nav>
  </div>
</header>