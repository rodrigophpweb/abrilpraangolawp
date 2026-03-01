/**
 * Inscrição — AJAX form handler (Vanilla JS)
 */
(function () {
  'use strict';

  const form     = document.getElementById('ec-form-inscricao');
  if (!form) return;

  const feedback = form.closest('.ec-inscricao').querySelector('.ec-inscricao__feedback');
  const btnSubmit = form.querySelector('.ec-inscricao__submit');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Limpar erros anteriores
    clearFieldErrors();
    feedback.hidden = true;
    feedback.innerHTML = '';

    // Indicar carregamento
    btnSubmit.disabled = true;
    btnSubmit.textContent = 'Enviando…';

    try {
      const data = new FormData(form);
      data.append('action', 'ec_inscricao_submit');

      const res = await fetch(ecInscricao.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        body: data,
      });

      const json = await res.json();

      if (json.success) {
        renderSuccess(json.data);
        form.hidden = true;
      } else {
        renderError(json.data);
      }

    } catch (err) {
      renderError({ message: 'Erro de conexão. Tente novamente.' });
    } finally {
      btnSubmit.disabled = false;
      btnSubmit.textContent = 'Enviar inscrição';
    }
  });

  /* ── Renderizadores ───────────────────────── */

  function renderSuccess(data) {
    let html = `
      <div class="ec-alert ec-alert--success">
        <h2>${esc(data.message)}</h2>
        <dl class="ec-alert__summary">
          <dt>Nome</dt><dd>${esc(data.nome)}</dd>
          <dt>Pacote</dt><dd>${esc(data.pacote_titulo)}</dd>
          <dt>Valor</dt><dd>${esc(data.preco)}</dd>
        </dl>`;

    if (data.transporte) {
      html += `<p><strong>Transporte:</strong> Sim (+R$ 70,00)</p>`;
    }

    if (data.forma === 'pix' && data.chave_pix) {
      html += `
        <div class="ec-alert__payment">
          <h3>Pagamento via Pix</h3>
          <p>Copie a chave abaixo e realize o pagamento:</p>
          <div class="ec-pix-box">
            <code class="ec-pix-box__key">${esc(data.chave_pix)}</code>
            <button type="button" class="ec-button ec-button--pix" data-copy="${esc(data.chave_pix)}">
              Copiar chave
            </button>
          </div>
        </div>`;
    }

    if (data.forma === 'cartao' && data.link_cartao) {
      html += `
        <div class="ec-alert__payment">
          <h3>Pagamento via Cartão</h3>
          <p>Clique no botão abaixo para acessar o link de pagamento:</p>
          <a class="ec-button ec-button--primary" href="${esc(data.link_cartao)}" target="_blank" rel="noopener">
            Pagar no cartão
          </a>
        </div>`;
    }

    html += `</div>`;

    feedback.innerHTML = html;
    feedback.hidden = false;
    feedback.scrollIntoView({ behavior: 'smooth', block: 'start' });

    // Botão copiar Pix
    const copyBtn = feedback.querySelector('[data-copy]');
    if (copyBtn) {
      copyBtn.addEventListener('click', () => {
        navigator.clipboard.writeText(copyBtn.dataset.copy).then(() => {
          copyBtn.textContent = 'Copiado!';
          setTimeout(() => { copyBtn.textContent = 'Copiar chave'; }, 2500);
        });
      });
    }
  }

  function renderError(data) {
    const msg = data?.message || 'Ocorreu um erro inesperado.';

    feedback.innerHTML = `<div class="ec-alert ec-alert--error"><p>${esc(msg)}</p></div>`;
    feedback.hidden = false;
    feedback.scrollIntoView({ behavior: 'smooth', block: 'start' });

    // Marcar campos inválidos
    if (data?.fields) {
      for (const [name, errMsg] of Object.entries(data.fields)) {
        const input = form.querySelector(`[name="${name}"]`);
        if (!input) continue;
        const wrapper = input.closest('.ec-field');
        if (!wrapper) continue;
        wrapper.classList.add('ec-field--error');
        const hint = document.createElement('span');
        hint.className = 'ec-field__error';
        hint.textContent = errMsg;
        wrapper.appendChild(hint);
      }
    }
  }

  function clearFieldErrors() {
    form.querySelectorAll('.ec-field--error').forEach((el) => {
      el.classList.remove('ec-field--error');
    });
    form.querySelectorAll('.ec-field__error').forEach((el) => el.remove());
  }

  /** Escape básico contra XSS no innerHTML */
  function esc(str) {
    const el = document.createElement('span');
    el.textContent = str ?? '';
    return el.innerHTML;
  }

})();
