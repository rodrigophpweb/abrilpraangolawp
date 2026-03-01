<?php
/**
 * Template: Página de Inscrições (slug: inscricoes)
 */
get_header();

// Buscar ingressos publicados para o <select>
$ingressos = new WP_Query([
  'post_type'      => 'ingressos',
  'posts_per_page' => -1,
  'post_status'    => 'publish',
  'orderby'        => 'menu_order',
  'order'          => 'ASC',
]);
?>

<main class="site-main">
  <section class="ec-section ec-inscricao" id="inscricao">
    <div class="container">

      <header class="ec-inscricao__header">
        <h1><?php the_title(); ?></h1>
        <?php if (get_the_content()) : ?>
          <div class="ec-inscricao__intro">
            <?php the_content(); ?>
          </div>
        <?php endif; ?>
      </header>

      <!-- Feedback: mensagem de sucesso/erro (renderizada via JS) -->
      <div class="ec-inscricao__feedback" role="status" aria-live="polite" hidden></div>

      <form
        class="ec-inscricao__form"
        id="ec-form-inscricao"
        method="post"
        novalidate
      >
        <!-- Nonce + honeypot -->
        <?php wp_nonce_field('ec_inscricao_nonce', 'nonce'); ?>
        <input type="text" name="website" autocomplete="off" tabindex="-1" aria-hidden="true" class="ec-hp">

        <fieldset class="ec-inscricao__fieldset">
          <legend>Dados pessoais</legend>

          <div class="ec-inscricao__grid">

            <div class="ec-field ec-field--full">
              <label for="ec-nome">Nome completo <abbr title="obrigatório">*</abbr></label>
              <input type="text" id="ec-nome" name="nome_completo" required minlength="3" autocomplete="name">
            </div>

            <div class="ec-field">
              <label for="ec-email">E-mail <abbr title="obrigatório">*</abbr></label>
              <input type="email" id="ec-email" name="email" required autocomplete="email">
            </div>

            <div class="ec-field">
              <label for="ec-whatsapp">WhatsApp <abbr title="obrigatório">*</abbr></label>
              <input type="tel" id="ec-whatsapp" name="whatsapp" required minlength="8" autocomplete="tel">
            </div>

            <div class="ec-field">
              <label for="ec-associacao">Associação / Grupo / Escola</label>
              <input type="text" id="ec-associacao" name="associacao">
            </div>

            <div class="ec-field">
              <label for="ec-apelido">Apelido na capoeira</label>
              <input type="text" id="ec-apelido" name="apelido_capoeira">
            </div>

            <div class="ec-field">
              <label for="ec-camiseta">Tamanho da camiseta</label>
              <select id="ec-camiseta" name="tamanho_camiseta">
                <option value="">— Selecione —</option>
                <option value="P">P</option>
                <option value="M">M</option>
                <option value="G">G</option>
                <option value="GG">GG</option>
              </select>
            </div>

            <div class="ec-field">
              <label for="ec-alergia-alimento">Alergia a alimento?</label>
              <select id="ec-alergia-alimento" name="alergia_alimento">
                <option value="">— Selecione —</option>
                <option value="nao">Não</option>
                <option value="sim">Sim</option>
              </select>
            </div>

            <div class="ec-field">
              <label for="ec-alergia-med">Alergia a medicamento?</label>
              <select id="ec-alergia-med" name="alergia_medicamento">
                <option value="">— Selecione —</option>
                <option value="nao">Não</option>
                <option value="sim">Sim</option>
              </select>
            </div>

          </div>
        </fieldset>

        <fieldset class="ec-inscricao__fieldset">
          <legend>Pacote e pagamento</legend>

          <div class="ec-inscricao__grid">

            <div class="ec-field ec-field--full">
              <label for="ec-pacote">Pacote / Ingresso <abbr title="obrigatório">*</abbr></label>
              <select id="ec-pacote" name="pacote_id" required>
                <option value="">— Selecione o pacote —</option>
                <?php while ($ingressos->have_posts()) : $ingressos->the_post();
                  $pid          = get_the_ID();
                  $preco_avista = function_exists('get_field') ? (string) get_field('preco_a_vista', $pid) : '';
                  $preco_cartao = function_exists('get_field') ? (string) get_field('preco_cartao_credito', $pid) : '';
                  $info = [];
                  if ($preco_avista) $info[] = 'Pix: ' . $preco_avista;
                  if ($preco_cartao) $info[] = 'Cartão: ' . $preco_cartao;
                  $label = get_the_title();
                  if ($info) $label .= ' (' . implode(' | ', $info) . ')';
                ?>
                  <option value="<?php echo esc_attr($pid); ?>">
                    <?php echo esc_html($label); ?>
                  </option>
                <?php endwhile; wp_reset_postdata(); ?>
              </select>
            </div>

            <div class="ec-field ec-field--full">
              <p class="ec-field__label">Forma de pagamento <abbr title="obrigatório">*</abbr></p>
              <div class="ec-radios">
                <label class="ec-radio">
                  <input type="radio" name="forma_pagamento" value="pix" required>
                  <span>Pix (à vista)</span>
                </label>
                <label class="ec-radio">
                  <input type="radio" name="forma_pagamento" value="cartao">
                  <span>Cartão de crédito</span>
                </label>
              </div>
            </div>

            <div class="ec-field">
              <label for="ec-data-pgto">Data do pagamento</label>
              <input type="date" id="ec-data-pgto" name="data_pagamento">
            </div>

            <div class="ec-field">
              <label class="ec-checkbox">
                <input type="checkbox" name="transporte" value="1">
                <span>Preciso de transporte (+R$&nbsp;70,00)</span>
              </label>
            </div>

          </div>
        </fieldset>

        <fieldset class="ec-inscricao__fieldset">
          <legend>Confirmação</legend>

          <div class="ec-field ec-field--full">
            <label class="ec-checkbox">
              <input type="checkbox" name="termo" value="1" required>
              <span>Li e aceito o <a href="#termo" target="_blank" rel="noopener">termo de compromisso</a>. <abbr title="obrigatório">*</abbr></span>
            </label>
          </div>
        </fieldset>

        <footer class="ec-inscricao__actions">
          <button type="submit" class="ec-button ec-button--primary ec-inscricao__submit">
            Enviar inscrição
          </button>
        </footer>

      </form>

    </div>
  </section>
</main>

<?php get_footer(); ?>
