function pad(n) {
  return String(n).padStart(2, "0");
}

function initCountdown(root) {
  const end = Number(root.dataset.countdown || 0);
  const dd = root.querySelector("[data-dd]");
  const hh = root.querySelector("[data-hh]");
  const mm = root.querySelector("[data-mm]");
  const ss = root.querySelector("[data-ss]");

  if (!end || !dd || !hh || !mm || !ss) return;

  function tick() {
    const now = Math.floor(Date.now() / 1000);
    let diff = end - now;
    if (diff < 0) diff = 0;

    const days = Math.floor(diff / 86400);
    diff -= days * 86400;

    const hours = Math.floor(diff / 3600);
    diff -= hours * 3600;

    const mins = Math.floor(diff / 60);
    diff -= mins * 60;

    dd.textContent = days;
    hh.textContent = pad(hours);
    mm.textContent = pad(mins);
    ss.textContent = pad(diff);
  }

  tick();
  setInterval(tick, 1000);
}

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".ec-countdown").forEach(initCountdown);
});

async function copyText(text) {
  // Clipboard API
  if (navigator.clipboard && window.isSecureContext) {
    await navigator.clipboard.writeText(text);
    return true;
  }

  // Fallback
  const ta = document.createElement("textarea");
  ta.value = text;
  ta.style.position = "fixed";
  ta.style.left = "-9999px";
  document.body.appendChild(ta);
  ta.focus();
  ta.select();
  const ok = document.execCommand("copy");
  document.body.removeChild(ta);
  return ok;
}

document.addEventListener("click", async (e) => {
  const btn = e.target.closest("[data-copy]");
  if (!btn) return;

  const key = btn.getAttribute("data-copy");
  if (!key) return;

  const hint = btn.parentElement?.querySelector(".ec-pix__hint");

  try {
    await copyText(key);
    if (hint) hint.textContent = "Chave Pix copiada ✅";
    btn.textContent = "Copiado!";
    setTimeout(() => (btn.textContent = "Copiar Pix"), 1800);
  } catch (err) {
    if (hint) hint.textContent = "Não consegui copiar automaticamente. Segure e copie manualmente:";
    // fallback visual simples
    window.prompt("Copie a chave Pix:", key);
  }
});