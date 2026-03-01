/**
 * Clipboard — copia texto de data-copy para o clipboard.
 * Usado nos botões "Copiar Pix" (single-ingressos e cards).
 */
document.addEventListener('DOMContentLoaded', function () {
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-copy]');
    if (!btn) return;

    var text = btn.getAttribute('data-copy');
    if (!text) return;

    var hint = btn.parentElement.querySelector('.ec-pix__hint');

    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(text).then(function () {
        showHint(hint, 'Chave Pix copiada!');
      }).catch(function () {
        fallbackCopy(text, hint);
      });
    } else {
      fallbackCopy(text, hint);
    }
  });

  function fallbackCopy(text, hint) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    try {
      document.execCommand('copy');
      showHint(hint, 'Chave Pix copiada!');
    } catch (_) {
      showHint(hint, 'Não foi possível copiar.');
    }
    document.body.removeChild(ta);
  }

  function showHint(el, msg) {
    if (!el) return;
    el.textContent = msg;
    clearTimeout(el._t);
    el._t = setTimeout(function () {
      el.textContent = '';
    }, 3000);
  }
});
