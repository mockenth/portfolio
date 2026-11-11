(() => {
  const grid = document.getElementById('designGrid');
  const modal = document.getElementById('mediaModal');
  if (!grid || !modal) return;

  const items = window.PORTFOLIO_DATA.designs;
  let shown = [], index = 0;
  const img = document.getElementById('modalImage');
  const title = document.getElementById('modalTitle');
  const desc = document.getElementById('modalDescription');

  function render(filter = 'all') {
    shown = items.filter(x => filter === 'all' || x.category === filter);
    grid.innerHTML = shown.map((x, i) =>
      `<article class="design-item" data-index="${i}" tabindex="0" role="button" aria-label="Preview ${x.title}"><img src="${x.image}" alt="${x.title}" loading="lazy"><div class="design-overlay"><small>${x.caption}</small><h3>${x.title} ↗</h3></div></article>`
    ).join('') || '<p>No designs in this category yet.</p>';

    grid.querySelectorAll('.design-item').forEach(el => {
      el.addEventListener('click', () => open(+el.dataset.index));
      el.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          open(+el.dataset.index);
        }
      });
    });
  }

  function open(i) {
    index = i;
    update();
    modal.hidden = false;
    document.body.classList.add('modal-open');
    document.getElementById('modalClose').focus();
  }

  function update() {
    const x = shown[index];
    img.src = x.image;
    img.alt = x.title;
    title.textContent = x.title;
    desc.textContent = x.description;
  }

  function close() {
    modal.hidden = true;
    document.body.classList.remove('modal-open');
  }

  document.getElementById('modalClose').addEventListener('click', close);
  modal.addEventListener('click', e => { if (e.target === modal) close(); });
  document.getElementById('modalPrev').addEventListener('click', () => {
    index = (index - 1 + shown.length) % shown.length;
    update();
  });
  document.getElementById('modalNext').addEventListener('click', () => {
    index = (index + 1) % shown.length;
    update();
  });
  document.addEventListener('keydown', e => {
    if (modal.hidden) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowLeft') {
      index = (index - 1 + shown.length) % shown.length;
      update();
    }
    if (e.key === 'ArrowRight') {
      index = (index + 1) % shown.length;
      update();
    }
  });

  document.querySelectorAll('.design-filters .filter-btn').forEach(b => b.addEventListener('click', () => {
    document.querySelectorAll('.design-filters .filter-btn').forEach(x => x.classList.remove('active'));
    b.classList.add('active');
    render(b.dataset.filter);
  }));

  render();
})();
