(() => {
  const menu = document.getElementById('menuToggle');
  const links = document.getElementById('navLinks');

  menu.addEventListener('click', () => {
    const open = links.classList.toggle('open');
    menu.setAttribute('aria-expanded', String(open));
    menu.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
  });

  links.querySelectorAll('a').forEach(a => a.addEventListener('click', () => {
    links.classList.remove('open');
    menu.setAttribute('aria-expanded', 'false');
  }));

  const sections = [...document.querySelectorAll('main section[id]')];
  const nav = [...document.querySelectorAll('.nav-link')];

  const observer = new IntersectionObserver(entries => entries.forEach(e => {
    if (e.isIntersecting) {
      nav.forEach(a => a.classList.toggle('active', a.getAttribute('href') === '#' + e.target.id));
    }
  }), { rootMargin: '-35% 0px -55% 0px' });

  sections.forEach(s => observer.observe(s));

  const role = document.getElementById('typedRole');
  const roles = window.PORTFOLIO_DATA.owner.roles;
  let i = 0;

  if (role && roles.length > 1) {
    setInterval(() => {
      i = (i + 1) % roles.length;
      role.animate(
        [
          { opacity: 1, transform: 'translateY(0)' },
          { opacity: 0, transform: 'translateY(5px)' },
          { opacity: 1, transform: 'translateY(0)' }
        ],
        { duration: 450 }
      );
      role.textContent = roles[i];
    }, 2600);
  }

  document.getElementById('year').textContent = new Date().getFullYear();

    /* --- Social Links with Icons --- */
  const SOCIAL_ICONS = {
    facebook:  'M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15H8v-3h2V9.5C10 7.57 11.57 6 13.5 6H16v3h-2c-.55 0-1 .45-1 1v2h3v3h-3v6.95c5.05-.5 9-4.76 9-9.95z',
    instagram: 'M12 2.16c3.2 0 3.58.01 4.85.07 3.25.15 4.77 1.69 4.92 4.92.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.15 3.23-1.66 4.77-4.92 4.92-1.27.06-1.64.07-4.85.07s-3.58-.01-4.85-.07c-3.26-.15-4.77-1.7-4.92-4.92-.06-1.27-.07-1.64-.07-4.85s.01-3.58.07-4.85c.15-3.23 1.66-4.77 4.92-4.92C8.42 2.17 8.8 2.16 12 2.16zM12 0C8.74 0 8.33.01 7.05.07 2.7.27.27 2.69.07 7.05.01 8.33 0 8.74 0 12s.01 3.67.07 4.95c.2 4.36 2.62 6.78 6.98 6.98C8.33 23.99 8.74 24 12 24s3.67-.01 4.95-.07c4.35-.2 6.78-2.62 6.98-6.98.06-1.28.07-1.69.07-4.95s-.01-3.67-.07-4.95c-.2-4.35-2.62-6.78-6.98-6.98C15.67.01 15.26 0 12 0zm0 5.84a6.16 6.16 0 100 12.32 6.16 6.16 0 000-12.32zM12 16a4 4 0 110-8 4 4 0 010 8zm6.41-11.85a1.44 1.44 0 100 2.88 1.44 1.44 0 000-2.88z',
    github:    'M12 0C5.37 0 0 5.37 0 12c0 5.3 3.44 9.8 8.21 11.39.6.11.79-.26.79-.58v-2.23c-3.34.73-4.03-1.42-4.03-1.42-.55-1.39-1.33-1.76-1.33-1.76-1.09-.75.08-.73.08-.73 1.21.08 1.84 1.24 1.84 1.24 1.07 1.83 2.81 1.3 3.49 1 .11-.78.42-1.31.76-1.6-2.67-.31-5.47-1.33-5.47-5.93 0-1.31.47-2.38 1.24-3.22-.12-.31-.54-1.52.12-3.18 0 0 1.01-.32 3.3 1.23A11.5 11.5 0 0112 5.8c1.02.01 2.05.14 3.01.4 2.29-1.55 3.3-1.23 3.3-1.23.65 1.65.24 2.87.12 3.18.77.84 1.23 1.91 1.23 3.22 0 4.61-2.81 5.62-5.48 5.92.43.37.82 1.1.82 2.22v3.29c0 .32.19.69.8.58C20.57 21.8 24 17.3 24 12c0-6.63-5.37-12-12-12z',
    linkedin:  'M20.45 20.45h-3.55v-5.57c0-1.33-.03-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29zM5.34 7.43a2.06 2.06 0 11.02-4.12 2.06 2.06 0 01-.02 4.12zM7.12 20.45H3.56V9h3.56v11.45zM22.23 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.46c.98 0 1.77-.77 1.77-1.73V1.73C24 .77 23.21 0 22.23 0z',
    behance:   'M22 7h-7V5h7v2zm1.73 10c-.44 1.3-2.03 3-5.1 3-3.08 0-5.57-1.73-5.57-5.68 0-3.91 2.33-5.92 5.47-5.92 3.08 0 4.96 1.78 5.38 4.43.08.51.11 1.19.09 2.14h-7.72c.13 3.21 3.48 3.31 4.59 2.03h3.17zm-7.69-4h4.97c-.11-1.55-1.14-2.22-2.48-2.22-1.47 0-2.28.77-2.49 2.22zM6.48 19.99H0V5.02h6.95c5.48.08 5.58 5.44 2.72 6.91 3.46 1.26 3.58 8.06-3.19 8.06zM3 11h3.58c2.51 0 2.91-3-.31-3H3v3zm3.39 3H3v3.02h3.34c3.06 0 2.87-3.02.05-3.02z'
  };

  const SOCIAL_ALIASES = { linkin: 'linkedin' };

  const social = document.getElementById('socialLinks');
  if (social) {
    social.innerHTML = window.PORTFOLIO_DATA.socials
      .filter(s => s.url && !s.url.startsWith('['))
      .map(s => {
        let key = String(s.name || s.label || '').toLowerCase().trim().replace(/\s+/g, '');
        key = SOCIAL_ALIASES[key] || key;
        const path = SOCIAL_ICONS[key];
        const icon = path
          ? `<svg class="social-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="${path}"/></svg>`
          : `<span class="social-icon social-icon-fallback">${(s.label || s.name || '?').charAt(0).toUpperCase()}</span>`;
        return `<a href="${s.url}" target="_blank" rel="noopener">${icon}<span class="social-label">${s.label}</span></a>`;
      })
      .join('');
  }
})();
