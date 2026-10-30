// Theme toggle
(function(){
  const html = document.documentElement;
  const saved = localStorage.getItem('theme') || 'light';
  html.setAttribute('data-bs-theme', saved);
  document.addEventListener('click', (e)=>{
    if (e.target.closest('#themeToggle')) {
      const next = html.getAttribute('data-bs-theme')==='light'?'dark':'light';
      html.setAttribute('data-bs-theme', next);
      localStorage.setItem('theme', next);
    }
  });
})();
// Confirmation for destructive buttons
document.addEventListener('click', (e)=>{
  const el = e.target.closest('[data-confirm]');
  if (el && !confirm(el.dataset.confirm)) e.preventDefault();
});