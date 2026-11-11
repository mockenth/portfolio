(() => {
  const root = document.documentElement;
  const btn = document.getElementById('themeToggle');

  function setTheme(theme) {
    root.dataset.theme = theme;
    try {
      localStorage.setItem('portfolio-theme', theme);
    } catch (e) {}
    if (btn) {
      btn.querySelector('.theme-icon').textContent = theme === 'dark' ? '☼' : '☾';
      btn.setAttribute('aria-label', theme === 'dark' ? 'Switch to light theme' : 'Switch to dark theme');
    }
  }

  const initial = root.dataset.theme || 'dark';
  setTheme(initial);
  btn?.addEventListener('click', () => setTheme(root.dataset.theme === 'dark' ? 'light' : 'dark'));
})();
