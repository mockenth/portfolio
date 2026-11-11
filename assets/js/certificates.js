(() => {
  const grid = document.getElementById('certificateGrid');
  const timeline = document.getElementById('timeline');
  if (!grid) return;

  const certs = window.PORTFOLIO_DATA.certificates;
  let currentIndex = 0;

  // 1. Render the Certificate Grid
  grid.innerHTML = certs.map((c, i) =>
    `<article class="certificate-card" data-index="${i}" tabindex="0" role="button" aria-label="Preview ${c.title}">
      <div class="certificate-preview">${c.image ? `<img src="${c.image}" alt="${c.title}" loading="lazy">` : '✧'}</div>
      <div class="certificate-info">
        <p class="card-label">${c.category || 'Certificate'}</p>
        <h3>${c.title}</h3>
        <p>${c.issuer} · ${c.date}</p>
        ${c.file ? `<a class="project-open" href="${c.file}" target="_blank" rel="noopener" onclick="event.stopPropagation()">View file ↗</a>` : ''}
      </div>
    </article>`
  ).join('');

  // 2. Render the Timeline
  if (timeline) {
    timeline.innerHTML = window.PORTFOLIO_DATA.journey.map(x =>
      `<article class="timeline-item">
        <time>${x.date}</time>
        <h3>${x.title}</h3>
        <p>${x.description}</p>
      </article>`
    ).join('');
  }

  // 3. Modal Lightbox Logic
  const modal = document.getElementById('certificateModal');
  const imgEl = document.getElementById('certModalImage');
  const titleEl = document.getElementById('certModalTitle');
  const descEl = document.getElementById('certModalDesc');
  const linkEl = document.getElementById('certModalLink'); // <-- New Link Element

  function openModal(i) {
    currentIndex = i;
    updateModal();
    modal.hidden = false;
    document.body.classList.add('modal-open');
    document.getElementById('certModalClose').focus();
  }

  function updateModal() {
    const c = certs[currentIndex];
    imgEl.src = c.image || '';
    imgEl.alt = c.title;
    titleEl.textContent = c.title;
    descEl.textContent = `${c.issuer} · ${c.date}`;

    // Show or hide the link button based on whether a file exists
    if (c.file) {
      linkEl.href = c.file;
      linkEl.style.display = 'inline-block';
    } else {
      linkEl.style.display = 'none';
    }
  }

  function closeModal() {
    modal.hidden = true;
    document.body.classList.remove('modal-open');
    const card = grid.querySelector(`.certificate-card[data-index="${currentIndex}"]`);
    if (card) card.focus();
  }

  // Attach click & keyboard events to the cards
  grid.querySelectorAll('.certificate-card').forEach(card => {
    card.addEventListener('click', (e) => {
      if (e.target.tagName.toLowerCase() === 'a') return;
      openModal(parseInt(card.dataset.index, 10));
    });
    card.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openModal(parseInt(card.dataset.index, 10));
      }
    });
  });

  // Modal controls
  document.getElementById('certModalClose').addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

  document.getElementById('certModalPrev').addEventListener('click', () => {
    currentIndex = (currentIndex - 1 + certs.length) % certs.length;
    updateModal();
  });

  document.getElementById('certModalNext').addEventListener('click', () => {
    currentIndex = (currentIndex + 1) % certs.length;
    updateModal();
  });

  document.addEventListener('keydown', (e) => {
    if (modal.hidden) return;
    if (e.key === 'Escape') closeModal();
    if (e.key === 'ArrowLeft') {
      currentIndex = (currentIndex - 1 + certs.length) % certs.length;
      updateModal();
    }
    if (e.key === 'ArrowRight') {
      currentIndex = (currentIndex + 1) % certs.length;
      updateModal();
    }
  });
})();
