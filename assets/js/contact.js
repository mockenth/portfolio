(() => {
  const btn = document.getElementById('copyEmail');
  const feedback = document.getElementById('copyFeedback');
  if (!btn) return;

  const labelEl = btn.querySelector('.copy-label');
  const DEFAULT_LABEL = labelEl ? labelEl.textContent : 'Copy my email';

  let email = (btn.dataset.email || '').trim();
  let resetTimer = null;

  /* Keep the copied address in sync with the admin panel (owner.email). */
  window.portfolioReady?.then(() => {
    const ownerEmail = window.PORTFOLIO_DATA?.owner?.email;
    if (ownerEmail) {
      email = String(ownerEmail).trim();
      btn.dataset.email = email;
    }
  });

  async function copyText(text) {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(text);
      return true;
    }
    // Fallback: works on http://localhost, older Safari, and file:// pages.
    const ta = document.createElement('textarea');
    ta.value = text;
    ta.setAttribute('readonly', '');
    ta.style.position = 'fixed';
    ta.style.top = '-1000px';
    ta.style.opacity = '0';
    document.body.appendChild(ta);
    ta.select();
    ta.setSelectionRange(0, ta.value.length);
    let ok = false;
    try { ok = document.execCommand('copy'); } catch { ok = false; }
    ta.remove();
    return ok;
  }

  function setFeedback(text, isError = false) {
    if (!feedback) return;
    feedback.textContent = text;
    feedback.classList.toggle('error', isError);
  }

  btn.addEventListener('click', async () => {
    if (!email) {
      setFeedback('Email address is not configured yet.', true);
      return;
    }

    try {
      const ok = await copyText(email);
      if (!ok) throw new Error('copy-failed');

      if (labelEl) labelEl.textContent = 'Copied!';
      btn.classList.add('copied');
      setFeedback(`${email} copied to clipboard.`);

      clearTimeout(resetTimer);
      resetTimer = setTimeout(() => {
        if (labelEl) labelEl.textContent = DEFAULT_LABEL;
        btn.classList.remove('copied');
        setFeedback('');
      }, 2200);
    } catch {
      setFeedback(`Could not copy — please select the address manually: ${email}`, true);
    }
  });
})();
