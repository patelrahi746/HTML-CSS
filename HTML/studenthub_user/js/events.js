// events.js – fetch and render events with search & pagination

document.addEventListener('DOMContentLoaded', function () {
  const eventsContainer = document.getElementById('eventsContainer');
  const searchInput = document.getElementById('search');
  const prevBtn = document.getElementById('prevBtn');
  const nextBtn = document.getElementById('nextBtn');
  const pageInfo = document.getElementById('pageInfo');

  const itemsPerPage = 5;
  let allEvents = [];
  let filtered = [];
  let currentPage = 1;

  // Fetch JSON data
  fetch('data/events.json')
    .then(res => res.ok ? res.json() : Promise.reject('Failed to load'))
    .then(data => {
      allEvents = data.events || [];
      filtered = allEvents;
      renderPage(1);
      updatePagination();
    })
    .catch(err => {
      eventsContainer.innerHTML = `<p style="color:red;">${err}</p>`;
    });

  const renderPage = (page) => {
    currentPage = page;
    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageItems = filtered.slice(start, end);

    eventsContainer.innerHTML = '';
    if (pageItems.length === 0) {
      eventsContainer.innerHTML = '<p>No events found.</p>';
      return;
    }
    pageItems.forEach(ev => {
      const card = document.createElement('div');
      card.className = 'card';
      card.innerHTML = `
        <h3>${ev.title}</h3>
        <p><strong>Date:</strong> ${ev.date}</p>
        <p>${ev.description}</p>
      `;
      eventsContainer.appendChild(card);
    });
    updatePagination();
  };

  const updatePagination = () => {
    const totalPages = Math.ceil(filtered.length / itemsPerPage) || 1;
    pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
    prevBtn.disabled = currentPage <= 1;
    nextBtn.disabled = currentPage >= totalPages;
  };

  prevBtn.addEventListener('click', () => {
    if (currentPage > 1) renderPage(currentPage - 1);
  });
  nextBtn.addEventListener('click', () => {
    const totalPages = Math.ceil(filtered.length / itemsPerPage);
    if (currentPage < totalPages) renderPage(currentPage + 1);
  });

  // Simple debounce
  let debounceTimer;
  searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      const term = searchInput.value.trim().toLowerCase();
      if (term === '') {
        filtered = allEvents;
      } else {
        filtered = allEvents.filter(ev =>
          ev.title.toLowerCase().includes(term) ||
          ev.description.toLowerCase().includes(term)
        );
      }
      renderPage(1);
    }, 300);
  });
});
