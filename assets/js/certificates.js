(() => {
  const grid = document.getElementById('certificateGrid');
  const timeline = document.getElementById('timeline');
  const certs = window.PORTFOLIO_DATA.certificates;

  grid.innerHTML = certs.map(c =>
    `<article class="certificate-card" tabindex="0">
      <div class="certificate-preview">${c.image ? `<img src="${c.image}" alt="${c.title}" loading="lazy">` : '✧'}</div>
      <div class="certificate-info">
        <p class="card-label">${c.category}</p>
        <h3>${c.title}</h3>
        <p>${c.issuer} · ${c.date}</p>
        ${c.file ? `<a class="project-open" href="${c.file}" target="_blank" rel="noopener">View certificate ↗</a>` : ''}
      </div>
    </article>`
  ).join('');

  timeline.innerHTML = window.PORTFOLIO_DATA.journey.map(x =>
    `<article class="timeline-item">
      <time>${x.date}</time>
      <h3>${x.title}</h3>
      <p>${x.description}</p>
    </article>`
  ).join('');
})();
