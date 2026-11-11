(() => {
  const grid = document.getElementById('projectGrid');
  if (!grid) return;

  const data = window.PORTFOLIO_DATA.projects;

  function render(filter = 'all') {
    grid.innerHTML = data
      .filter(p => filter === 'all' || p.category === filter)
      .map((p, i) =>
        `<article class="project-card" data-project-id="${i}" tabindex="0" role="button" aria-label="View details for ${p.title}">
          <div class="project-thumb">
            ${p.image ? `<img src="${p.image}" alt="${p.title}" loading="lazy">` : `<div class="placeholder-art">${String(p.title).slice(0, 2).toUpperCase()}<span>.</span></div>`}
          </div>
          <div class="project-body">
            <p class="card-label">${p.type} / ${p.date}</p>
            <h3>${p.title}</h3>
            <p>${p.description}</p>
            <div class="project-tech">${p.tech.map(t => `<span>${t}</span>`).join('')}</div>
          </div>
        </article>`
      ).join('') || '<p>No projects in this category yet.</p>';

    // Attach click events to open modal
    grid.querySelectorAll('.project-card').forEach(card => {
      card.addEventListener('click', () => openProjectModal(card.dataset.projectId));
      card.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          openProjectModal(card.dataset.projectId);
        }
      });
    });
  }

  function openProjectModal(id) {
    const project = data[id];
    if (!project) return;

    const modal = document.getElementById('projectDetailModal');
    if (!modal) return;

    document.getElementById('pdType').textContent = project.type || 'PROJECT';
    document.getElementById('pdTitle').textContent = project.title;
    document.getElementById('pdSubtitle').textContent = project.subtitle || '';
    document.getElementById('pdDescription').textContent = project.description || '';

    const featuresList = document.getElementById('pdFeatures');
    if (project.features && project.features.length > 0) {
      featuresList.innerHTML = project.features.map(f => `<li>${f}</li>`).join('');
      featuresList.style.display = 'grid';
    } else {
      featuresList.style.display = 'none';
    }

    document.getElementById('pdTech').innerHTML = (project.tech || []).map(t => `<span>${t}</span>`).join('');

    const link = document.getElementById('pdLink');
    if (project.link && project.link !== '#') {
      link.href = project.link;
      link.style.display = 'inline-block';
    } else if (project.github && project.github !== '#') {
      link.href = project.github;
      link.textContent = 'VIEW GITHUB ↗';
      link.style.display = 'inline-block';
    } else {
      link.style.display = 'none';
    }

    const img = document.getElementById('pdImage');
    if (project.image) {
      img.src = project.image;
      img.style.display = 'block';
    } else {
      img.style.display = 'none';
    }

    modal.hidden = false;
    document.body.classList.add('modal-open');
    document.getElementById('closeProjectBtn').focus();
  }

  function closeProjectModal() {
    const modal = document.getElementById('projectDetailModal');
    if (modal) modal.hidden = true;
    document.body.classList.remove('modal-open');
  }

  document.getElementById('closeProjectBtn')?.addEventListener('click', closeProjectModal);
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !document.getElementById('projectDetailModal')?.hidden) {
      closeProjectModal();
    }
  });

  document.querySelectorAll('.project-filters .filter-btn').forEach(b => b.addEventListener('click', () => {
    document.querySelectorAll('.project-filters .filter-btn').forEach(x => x.classList.remove('active'));
    b.classList.add('active');
    render(b.dataset.filter);
  }));

  render();
})();
