<?php
if (!defined('ABSPATH')) exit;

$theme_includes = [
  '/inc/setup.php',
  '/inc/security.php',
  '/inc/performance.php',
  '/inc/enqueue.php',
  '/inc/helpers.php',
  '/inc/cpt.php',
  '/inc/acf.php',
  '/inc/schema.php',
  '/inc/ajax-inscricao.php',
];

foreach ($theme_includes as $file) {
  $path = get_theme_file_path($file);
  if (file_exists($path)) require_once $path;
}