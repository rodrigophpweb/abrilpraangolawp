<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {

  $cpts = [
    'oficineiros'    => ['name' => 'Oficineiros', 'singular' => 'Oficineiro'],
    'convidados'     => ['name' => 'Convidados', 'singular' => 'Convidado'],
    'patrocinadores' => ['name' => 'Patrocinadores', 'singular' => 'Patrocinador'],
    'ingressos'      => ['name' => 'Ingressos', 'singular' => 'Ingresso'],
    'galeria'        => ['name' => 'Galeria', 'singular' => 'Item da Galeria'],
    'depoimentos'    => ['name' => 'Depoimentos', 'singular' => 'Depoimento'],
    'programacao'    => ['name' => 'Programação', 'singular' => 'Item da Programação'],
  ];

  foreach ($cpts as $slug => $labels) {
    register_post_type($slug, [
      'labels' => [
        'name'          => $labels['name'],
        'singular_name' => $labels['singular'],
      ],
      'public'       => true,
      'has_archive'  => true,
      'menu_icon'    => 'dashicons-calendar-alt',
      'supports'     => ['title', 'editor', 'thumbnail', 'excerpt'],
      'show_in_rest' => true,
      'rewrite'      => ['slug' => $slug],
    ]);
  }

  // Taxonomia "agenda" para Programação (modo categoria)
register_taxonomy('agenda', ['programacao'], [
  'labels' => [
    'name'          => 'Agenda',
    'singular_name' => 'Agenda',
    'search_items'  => 'Buscar agendas',
    'all_items'     => 'Todas as agendas',
    'parent_item'   => 'Agenda pai',
    'parent_item_colon' => 'Agenda pai:',
    'edit_item'     => 'Editar agenda',
    'update_item'   => 'Atualizar agenda',
    'add_new_item'  => 'Adicionar nova agenda',
    'new_item_name' => 'Nova agenda',
    'menu_name'     => 'Agenda',
  ],
  'public'            => true,
  'hierarchical'      => true, // ✅ ISSO faz virar "categoria"
  'show_in_rest'      => true,
  'show_admin_column' => true,
  'rewrite'           => ['slug' => 'agenda'],

  // ✅ Força o metabox estilo categoria (checklist)
  'meta_box_cb'       => 'post_categories_meta_box',
]);

});