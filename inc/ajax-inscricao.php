<?php
if (!defined('ABSPATH')) exit;

add_action('wp_ajax_ec_inscricao_submit', 'ec_inscricao_submit');
add_action('wp_ajax_nopriv_ec_inscricao_submit', 'ec_inscricao_submit');

/**
 * Handler AJAX — Inscrição no evento.
 */
function ec_inscricao_submit() {

  // 1. Nonce
  check_ajax_referer('ec_inscricao_nonce', 'nonce');

  // 2. Honeypot
  if (!empty($_POST['website'])) {
    wp_send_json_error(['message' => 'Requisição inválida.'], 403);
  }

  // 3. Rate limit por IP (5 tentativas / 10 min)
  $ip  = sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
  $key = 'ec_rl_' . md5($ip);
  $hits = (int) get_transient($key);

  if ($hits >= 5) {
    wp_send_json_error([
      'message' => 'Muitas tentativas. Aguarde alguns minutos e tente novamente.',
    ], 429);
  }

  set_transient($key, $hits + 1, 10 * MINUTE_IN_SECONDS);

  // 4. Sanitizar & coletar campos
  $raw = [
    'nome_completo'      => sanitize_text_field($_POST['nome_completo'] ?? ''),
    'email'              => sanitize_email($_POST['email'] ?? ''),
    'whatsapp'           => sanitize_text_field($_POST['whatsapp'] ?? ''),
    'associacao'         => sanitize_text_field($_POST['associacao'] ?? ''),
    'apelido_capoeira'   => sanitize_text_field($_POST['apelido_capoeira'] ?? ''),
    'tamanho_camiseta'   => sanitize_text_field($_POST['tamanho_camiseta'] ?? ''),
    'alergia_alimento'   => sanitize_text_field($_POST['alergia_alimento'] ?? ''),
    'alergia_medicamento'=> sanitize_text_field($_POST['alergia_medicamento'] ?? ''),
    'pacote_id'          => absint($_POST['pacote_id'] ?? 0),
    'forma_pagamento'    => sanitize_text_field($_POST['forma_pagamento'] ?? ''),
    'data_pagamento'     => sanitize_text_field($_POST['data_pagamento'] ?? ''),
    'transporte'         => !empty($_POST['transporte']),
    'termo'              => !empty($_POST['termo']),
  ];

  // 5. Validação
  $errors = [];

  if (mb_strlen($raw['nome_completo']) < 3) {
    $errors['nome_completo'] = 'Nome deve ter pelo menos 3 caracteres.';
  }

  if (!is_email($raw['email'])) {
    $errors['email'] = 'E-mail inválido.';
  }

  if (mb_strlen($raw['whatsapp']) < 8) {
    $errors['whatsapp'] = 'WhatsApp deve ter pelo menos 8 caracteres.';
  }

  $camisetas_validas = ['', 'P', 'M', 'G', 'GG'];
  if (!in_array($raw['tamanho_camiseta'], $camisetas_validas, true)) {
    $errors['tamanho_camiseta'] = 'Tamanho de camiseta inválido.';
  }

  $sim_nao = ['', 'sim', 'nao'];
  if (!in_array($raw['alergia_alimento'], $sim_nao, true)) {
    $errors['alergia_alimento'] = 'Valor inválido para alergia alimentar.';
  }
  if (!in_array($raw['alergia_medicamento'], $sim_nao, true)) {
    $errors['alergia_medicamento'] = 'Valor inválido para alergia a medicamento.';
  }

  if (!in_array($raw['forma_pagamento'], ['pix', 'cartao'], true)) {
    $errors['forma_pagamento'] = 'Selecione uma forma de pagamento.';
  }

  // Validar pacote (CPT ingressos publicado)
  $pacote = get_post($raw['pacote_id']);
  if (!$pacote || $pacote->post_type !== 'ingressos' || $pacote->post_status !== 'publish') {
    $errors['pacote_id'] = 'Pacote inválido ou inexistente.';
  }

  // Data de pagamento (opcional, mas se preenchida deve ser Y-m-d)
  if ($raw['data_pagamento'] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw['data_pagamento'])) {
    $errors['data_pagamento'] = 'Data de pagamento em formato inválido.';
  }

  if (!$raw['termo']) {
    $errors['termo'] = 'Você precisa aceitar o termo de compromisso.';
  }

  if (!empty($errors)) {
    wp_send_json_error([
      'message' => 'Corrija os campos destacados.',
      'fields'  => $errors,
    ], 422);
  }

  // 6. Dados do ingresso selecionado (ACF)
  $pacote_titulo   = get_the_title($pacote);
  $chave_pix       = function_exists('get_field') ? (string) get_field('chave_pix', $pacote->ID)              : '';
  $link_cartao     = function_exists('get_field') ? (string) get_field('link_pagamento_cartao', $pacote->ID)   : '';
  $preco_avista    = function_exists('get_field') ? (string) get_field('preco_a_vista', $pacote->ID)           : '';
  $preco_cartao    = function_exists('get_field') ? (string) get_field('preco_cartao_credito', $pacote->ID)    : '';
  $validade_ticket = function_exists('get_field') ? (string) get_field('validade_ticket', $pacote->ID)         : '';

  // 7. Montar e-mail
  $transporte_txt = $raw['transporte'] ? 'Sim (R$ 70,00)' : 'Não';
  $data_pgto_txt  = $raw['data_pagamento']
    ? date_i18n('d/m/Y', strtotime($raw['data_pagamento']))
    : '—';

  $forma_txt = $raw['forma_pagamento'] === 'pix' ? 'Pix (à vista)' : 'Cartão de crédito';

  $preco_txt = $raw['forma_pagamento'] === 'pix' ? $preco_avista : $preco_cartao;

  $corpo_linhas = [
    '<h2 style="margin:0 0 8px;">Nova inscrição — ' . esc_html($raw['nome_completo']) . '</h2>',
    '<table style="border-collapse:collapse;width:100%;max-width:600px;font-family:sans-serif;font-size:14px;">',
    ec_email_row('Nome completo',      $raw['nome_completo']),
    ec_email_row('E-mail',             $raw['email']),
    ec_email_row('WhatsApp',           $raw['whatsapp']),
    ec_email_row('Associação/Grupo',   $raw['associacao'] ?: '—'),
    ec_email_row('Apelido na capoeira',$raw['apelido_capoeira'] ?: '—'),
    ec_email_row('Camiseta',           $raw['tamanho_camiseta'] ?: '—'),
    ec_email_row('Alergia alimento',   $raw['alergia_alimento'] ?: '—'),
    ec_email_row('Alergia medicamento',$raw['alergia_medicamento'] ?: '—'),
    ec_email_row('Pacote',             $pacote_titulo),
    ec_email_row('Valor',              $preco_txt ?: '—'),
    ec_email_row('Forma de pagamento', $forma_txt),
    ec_email_row('Data do pagamento',  $data_pgto_txt),
    ec_email_row('Transporte',         $transporte_txt),
    ec_email_row('Validade do ticket', $validade_ticket ? date_i18n('d/m/Y', strtotime($validade_ticket)) : '—'),
    '</table>',
  ];

  $corpo_html = implode("\n", $corpo_linhas);

  $assunto = sprintf(
    'Nova inscrição - %s - %s',
    $raw['nome_completo'],
    $pacote_titulo
  );

  $headers = [
    'Content-Type: text/html; charset=UTF-8',
    'Reply-To: ' . $raw['email'],
  ];

  // E-mail para admin
  wp_mail('mestremeinha@hotmail.com', $assunto, $corpo_html, $headers);

  // E-mail cópia para inscrito
  $corpo_inscrito  = '<p>Olá <strong>' . esc_html($raw['nome_completo']) . '</strong>,</p>';
  $corpo_inscrito .= '<p>Recebemos sua inscrição! Confira o resumo abaixo:</p>';
  $corpo_inscrito .= $corpo_html;

  if ($raw['forma_pagamento'] === 'pix' && $chave_pix) {
    $corpo_inscrito .= '<p><strong>Chave Pix para pagamento:</strong> ' . esc_html($chave_pix) . '</p>';
  } elseif ($raw['forma_pagamento'] === 'cartao' && $link_cartao) {
    $corpo_inscrito .= '<p><strong>Link para pagamento no cartão:</strong> <a href="' . esc_url($link_cartao) . '" target="_blank" rel="noopener">' . esc_url($link_cartao) . '</a></p>';
  }

  $corpo_inscrito .= '<p>Em caso de dúvidas, responda este e-mail.</p>';

  $headers_inscrito = ['Content-Type: text/html; charset=UTF-8'];

  wp_mail($raw['email'], 'Confirmação de inscrição - ' . $pacote_titulo, $corpo_inscrito, $headers_inscrito);

  // 8. Retorno JSON
  // TODO: Gravar inscrição no banco (CPT ou custom table) em etapa futura.
  wp_send_json_success([
    'message'       => 'Inscrição realizada com sucesso!',
    'pacote_id'     => $pacote->ID,
    'pacote_titulo' => $pacote_titulo,
    'preco'         => $preco_txt,
    'forma'         => $raw['forma_pagamento'],
    'chave_pix'     => ($raw['forma_pagamento'] === 'pix') ? $chave_pix : '',
    'link_cartao'   => ($raw['forma_pagamento'] === 'cartao') ? $link_cartao : '',
    'transporte'    => $raw['transporte'],
    'nome'          => $raw['nome_completo'],
  ]);
}

/**
 * Helper: monta uma <tr> para o e-mail HTML.
 */
function ec_email_row(string $label, string $value): string {
  return sprintf(
    '<tr><td style="padding:6px 10px;border:1px solid #ddd;font-weight:600;white-space:nowrap;">%s</td>'
    . '<td style="padding:6px 10px;border:1px solid #ddd;">%s</td></tr>',
    esc_html($label),
    esc_html($value)
  );
}
