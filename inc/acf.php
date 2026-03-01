<?php
if (!defined('ABSPATH')) exit;

/**
 * ACF bootstrap (campos via PHP).
 * Requer ACF ativo.
 */
add_action('acf/init', function () {

  if (!function_exists('acf_add_local_field_group')) return;

  /**
   * 1) Página de Opções do Tema
   */
  if (function_exists('acf_add_options_page')) {
    acf_add_options_page([
      'page_title'  => 'Configurações do Evento',
      'menu_title'  => 'Config. do Evento',
      'menu_slug'   => 'ec-config-evento',
      'capability'  => 'manage_options',
      'redirect'    => false,
      'position'    => 61,
      'icon_url'    => 'dashicons-admin-generic',
      'autoload'    => true,
    ]);
  }

  /**
   * Grupo: Configurações do Evento (Options Page)
   */
  acf_add_local_field_group([
    'key' => 'group_ec_event_options',
    'title' => 'Configurações do Evento',
    'fields' => [

      // Datas e local
      [
        'key' => 'field_ec_data_inicio',
        'label' => 'Data de início do evento',
        'name' => 'data_inicio_evento',
        'type' => 'date_picker',
        'return_format' => 'Y-m-d',
        'required' => 1,
      ],
      [
        'key' => 'field_ec_data_fim',
        'label' => 'Data final do evento',
        'name' => 'data_fim_evento',
        'type' => 'date_picker',
        'return_format' => 'Y-m-d',
        'required' => 1,
      ],
      [
        'key' => 'field_ec_local_evento',
        'label' => 'Local do evento',
        'name' => 'local_evento',
        'type' => 'text',
        'required' => 1,
      ],
      [
        'key' => 'field_ec_google_maps_iframe',
        'label' => 'Google Maps (iframe embed)',
        'name' => 'google_maps_iframe',
        'type' => 'textarea',
        'instructions' => 'Cole aqui o iframe do Google Maps (embed).',
        'new_lines' => '',
      ],
      [
        'key' => 'field_ec_descricao_evento',
        'label' => 'Descrição do evento',
        'name' => 'descricao_evento',
        'type' => 'textarea',
        'new_lines' => 'br',
      ],

      // Cores (CSS variables)
      [
        'key' => 'field_ec_tab_cores',
        'label' => 'Cores',
        'type' => 'tab',
      ],
      [
        'key' => 'field_ec_cor_principal',
        'label' => 'Cor principal',
        'name' => 'cor_principal',
        'type' => 'color_picker',
      ],
      [
        'key' => 'field_ec_cor_secundaria',
        'label' => 'Cor secundária',
        'name' => 'cor_secundaria',
        'type' => 'color_picker',
      ],
      [
        'key' => 'field_ec_cor_fundo',
        'label' => 'Cor de fundo',
        'name' => 'cor_fundo',
        'type' => 'color_picker',
      ],
      [
        'key' => 'field_ec_cor_clara',
        'label' => 'Cor clara',
        'name' => 'cor_clara',
        'type' => 'color_picker',
      ],
      [
        'key' => 'field_ec_cor_escura',
        'label' => 'Cor escura',
        'name' => 'cor_escura',
        'type' => 'color_picker',
      ],
      [
        'key' => 'field_ec_cor_titulo',
        'label' => 'Cor de título',
        'name' => 'cor_titulo',
        'type' => 'color_picker',
      ],
      [
        'key' => 'field_ec_cor_paragrafo',
        'label' => 'Cor de parágrafo',
        'name' => 'cor_paragrafo',
        'type' => 'color_picker',
      ],
      [
        'key' => 'field_ec_cor_link',
        'label' => 'Cor de link',
        'name' => 'cor_link',
        'type' => 'color_picker',
      ],
      [
        'key' => 'field_ec_cor_link_hover',
        'label' => 'Cor hover de link',
        'name' => 'cor_link_hover',
        'type' => 'color_picker',
      ],
      [
        'key' => 'field_ec_cor_link_active',
        'label' => 'Cor active de link',
        'name' => 'cor_link_active',
        'type' => 'color_picker',
      ],

      // Cores de regiões (header, footer, seções)
      [
        'key' => 'field_ec_tab_cores_regioes',
        'label' => 'Cores de Regiões',
        'type' => 'tab',
      ],
      [
        'key' => 'field_ec_cor_header_bg',
        'label' => 'Fundo do Header',
        'name' => 'cor_header_bg',
        'type' => 'color_picker',
        'instructions' => 'Deixe vazio para usar o padrão do tema.',
      ],
      [
        'key' => 'field_ec_cor_header_texto',
        'label' => 'Texto do Header',
        'name' => 'cor_header_texto',
        'type' => 'color_picker',
      ],
      [
        'key' => 'field_ec_cor_footer_bg',
        'label' => 'Fundo do Footer',
        'name' => 'cor_footer_bg',
        'type' => 'color_picker',
      ],
      [
        'key' => 'field_ec_cor_footer_texto',
        'label' => 'Texto do Footer',
        'name' => 'cor_footer_texto',
        'type' => 'color_picker',
      ],
      [
        'key' => 'field_ec_cor_secoes_bg',
        'label' => 'Fundo das Seções',
        'name' => 'cor_secoes_bg',
        'type' => 'color_picker',
        'instructions' => 'Cor de fundo padrão para todas as seções da Home.',
      ],
      [
        'key' => 'field_ec_cor_secoes_titulo',
        'label' => 'Cor dos títulos das Seções',
        'name' => 'cor_secoes_titulo',
        'type' => 'color_picker',
        'instructions' => 'Cor dos H2 dentro das seções. Deixe vazio para usar a cor de título global.',
      ],

      // Background
      [
        'key' => 'field_ec_tab_background',
        'label' => 'Background',
        'type' => 'tab',
      ],
      [
        'key' => 'field_ec_bg_tipo',
        'label' => 'Tipo de background',
        'name' => 'tipo_background',
        'type' => 'radio',
        'choices' => [
          'cor' => 'Cor',
          'imagem' => 'Imagem',
        ],
        'default_value' => 'cor',
        'layout' => 'horizontal',
      ],
      [
        'key' => 'field_ec_bg_cor',
        'label' => 'Cor do background',
        'name' => 'background_cor',
        'type' => 'color_picker',
        'conditional_logic' => [
          [
            [
              'field' => 'field_ec_bg_tipo',
              'operator' => '==',
              'value' => 'cor',
            ],
          ],
        ],
      ],
      [
        'key' => 'field_ec_bg_imagem',
        'label' => 'Imagem do background',
        'name' => 'background_imagem',
        'type' => 'image',
        'return_format' => 'id',
        'preview_size' => 'medium',
        'conditional_logic' => [
          [
            [
              'field' => 'field_ec_bg_tipo',
              'operator' => '==',
              'value' => 'imagem',
            ],
          ],
        ],
      ],
      [
        'key' => 'field_ec_bg_position',
        'label' => 'Background position',
        'name' => 'background_position',
        'type' => 'select',
        'choices' => [
          'center' => 'center',
          'top' => 'top',
          'bottom' => 'bottom',
          'left' => 'left',
          'right' => 'right',
          'center top' => 'center top',
          'center bottom' => 'center bottom',
        ],
        'default_value' => 'center',
        'conditional_logic' => [
          [
            [
              'field' => 'field_ec_bg_tipo',
              'operator' => '==',
              'value' => 'imagem',
            ],
          ],
        ],
      ],
      [
        'key' => 'field_ec_bg_size',
        'label' => 'Background size',
        'name' => 'background_size',
        'type' => 'select',
        'choices' => [
          'cover' => 'cover',
          'contain' => 'contain',
          'auto' => 'auto',
        ],
        'default_value' => 'cover',
        'conditional_logic' => [
          [
            [
              'field' => 'field_ec_bg_tipo',
              'operator' => '==',
              'value' => 'imagem',
            ],
          ],
        ],
      ],
      [
        'key' => 'field_ec_bg_repeat',
        'label' => 'Background repeat',
        'name' => 'background_repeat',
        'type' => 'select',
        'choices' => [
          'no-repeat' => 'no-repeat',
          'repeat' => 'repeat',
          'repeat-x' => 'repeat-x',
          'repeat-y' => 'repeat-y',
        ],
        'default_value' => 'no-repeat',
        'conditional_logic' => [
          [
            [
              'field' => 'field_ec_bg_tipo',
              'operator' => '==',
              'value' => 'imagem',
            ],
          ],
        ],
      ],

      // Tipografia e links
      [
        'key' => 'field_ec_tab_tipografia',
        'label' => 'Tipografia',
        'type' => 'tab',
      ],
      [
        'key' => 'field_ec_fonte_base',
        'label' => 'Fonte base (rem)',
        'name' => 'fonte_base_rem',
        'type' => 'number',
        'default_value' => 1,
        'min' => 0.75,
        'max' => 1.5,
        'step' => 0.05,
      ],
      [
        'key' => 'field_ec_links_underline',
        'label' => 'Links sublinhados?',
        'name' => 'links_underline',
        'type' => 'true_false',
        'ui' => 1,
        'default_value' => 0,
      ],
      [
        'key' => 'field_ec_botoes_underline',
        'label' => 'Botões sublinhados?',
        'name' => 'botoes_underline',
        'type' => 'true_false',
        'ui' => 1,
        'default_value' => 0,
      ],

      // Segurança
      [
        'key' => 'field_ec_tab_seguranca',
        'label' => 'Segurança',
        'type' => 'tab',
      ],
      [
        'key' => 'field_ec_seguranca_intro',
        'label' => 'Texto introdutório (Segurança)',
        'name' => 'seguranca_intro',
        'type' => 'textarea',
        'new_lines' => 'br',
        'instructions' => 'Texto exibido acima dos itens de segurança na Home.',
      ],
      [
        'key' => 'field_ec_seguranca_itens',
        'label' => 'Itens de segurança',
        'name' => 'seguranca_itens',
        'type' => 'repeater',
        'layout' => 'block',
        'button_label' => 'Adicionar item',
        'sub_fields' => [
          [
            'key' => 'field_ec_seguranca_titulo',
            'label' => 'Título',
            'name' => 'titulo',
            'type' => 'text',
            'required' => 1,
          ],
          [
            'key' => 'field_ec_seguranca_descricao',
            'label' => 'Descrição',
            'name' => 'descricao',
            'type' => 'textarea',
            'new_lines' => '',
          ],
        ],
      ],
    ],
    'location' => [
      [
        [
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'ec-config-evento',
        ],
      ],
    ],
  ]);

  /**
   * 2) Toggles de seções (Home e páginas internas)
   * Aplica em páginas (page) e na front-page.
   */
  $toggle_fields = [];
  $toggles = [
    'mostrar_hero' => 'Mostrar Hero',
    'mostrar_abril' => 'Mostrar Abril pra Angola',
    'mostrar_mestre_meinha' => 'Mostrar Mestre Meinha',
    'mostrar_evento' => 'Mostrar Seção Evento',
    'mostrar_grade' => 'Mostrar Grade do Evento',
    'mostrar_oficinas' => 'Mostrar Oficinas',
    'mostrar_presencas' => 'Mostrar Presenças',
    'mostrar_pacotes' => 'Mostrar Pacotes',
    'mostrar_local' => 'Mostrar Local do Evento',
    'mostrar_inscricao' => 'Mostrar Faça sua Inscrição',
    'mostrar_patrocinio' => 'Mostrar Apoio e Patrocínio',
    'mostrar_seguranca' => 'Mostrar Segurança',
  ];

  foreach ($toggles as $name => $label) {
    $toggle_fields[] = [
      'key' => 'field_ec_toggle_' . $name,
      'label' => $label,
      'name' => $name,
      'type' => 'true_false',
      'ui' => 1,
      'default_value' => 1,
    ];
  }

  acf_add_local_field_group([
    'key' => 'group_ec_section_toggles',
    'title' => 'Seções: visibilidade',
    'fields' => $toggle_fields,
    'location' => [
      [
        [
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'page',
        ],
      ],
    ],
  ]);

  /**
   * 3) Campos dos CPTs (Ingressos, Programação, Patrocinadores, etc.)
   */

  // Ingressos
  acf_add_local_field_group([
    'key' => 'group_ec_ingressos_fields',
    'title' => 'Ingressos: campos',
    'fields' => [
      [
        'key' => 'field_ec_preco_a_vista',
        'label' => 'Preço à vista',
        'name' => 'preco_a_vista',
        'type' => 'text',
      ],
      [
        'key' => 'field_ec_preco_cartao',
        'label' => 'Preço no cartão de crédito',
        'name' => 'preco_cartao_credito',
        'type' => 'text',
      ],
      [
        'key' => 'field_ec_validade_ticket',
        'label' => 'Validade do ticket',
        'name' => 'validade_ticket',
        'type' => 'date_picker',
        'return_format' => 'Y-m-d',
      ],
      [
        'key' => 'field_ec_chave_pix',
        'label' => 'Chave Pix',
        'name' => 'chave_pix',
        'type' => 'text',
        'instructions' => 'Informe a chave Pix (CPF, e-mail, telefone ou chave aleatória).',
      ],
      [
        'key' => 'field_ec_link_cartao',
        'label' => 'Link de pagamento (Cartão)',
        'name' => 'link_pagamento_cartao',
        'type' => 'url',
      ],
      [
        'key' => 'field_ec_descricao_curta_ingresso',
        'label' => 'Descrição curta',
        'name' => 'descricao_curta',
        'type' => 'textarea',
        'new_lines' => 'br',
      ],
    ],
    'location' => [
      [
        [
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'ingressos',
        ],
      ],
    ],
  ]);

  // Programação
  acf_add_local_field_group([
    'key' => 'group_ec_programacao_fields',
    'title' => 'Programação: campos',
    'fields' => [
      [
        'key' => 'field_ec_horario_inicio',
        'label' => 'Horário início',
        'name' => 'horario_inicio',
        'type' => 'time_picker',
        'display_format' => 'H:i',
        'return_format' => 'H:i',
      ],
      [
        'key' => 'field_ec_horario_fim',
        'label' => 'Horário fim',
        'name' => 'horario_fim',
        'type' => 'time_picker',
        'display_format' => 'H:i',
        'return_format' => 'H:i',
      ],
      [
        'key' => 'field_ec_local_programacao',
        'label' => 'Local',
        'name' => 'local_programacao',
        'type' => 'text',
      ],
    ],
    'location' => [
      [
        [
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'programacao',
        ],
      ],
    ],
  ]);

  // Patrocinadores
  acf_add_local_field_group([
    'key' => 'group_ec_patrocinadores_fields',
    'title' => 'Patrocinadores: campos',
    'fields' => [
      [
        'key' => 'field_ec_link_patrocinador',
        'label' => 'Link do patrocinador',
        'name' => 'link_site',
        'type' => 'url',
      ],
    ],
    'location' => [
      [
        [
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'patrocinadores',
        ],
      ],
    ],
  ]);

  // Página Evento: título editável da seção
  acf_add_local_field_group([
    'key' => 'group_ec_evento_page_fields',
    'title' => 'Evento: campos',
    'fields' => [
      [
        'key' => 'field_ec_evento_titulo_secao',
        'label' => 'Título da seção (Home)',
        'name' => 'titulo_secao_home',
        'type' => 'text',
        'instructions' => 'Ex: "Abril pra Angola" (este título aparece na Home).',
      ],
    ],
    'location' => [
      [
        [
          'param' => 'page_template',
          'operator' => '==',
          'value' => 'default',
        ],
        [
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'page',
        ],
      ],
    ],
  ]);  

});