// js/event.js – Event Management CRUD (admin UI)
// Assumes admin.html includes this script with defer.

document.addEventListener('DOMContentLoaded', () => {
    // Element references
    const tableBody = document.querySelector('#event-table tbody');
    const addBtn = document.getElementById('add-event-btn');
    const modal = document.getElementById('event-modal');
    const closeBtn = document.getElementById('close-event-modal');
    const form = document.getElementById('event-form');
    const modalTitle = document.getElementById('modal-title');
    const idInput = document.getElementById('event-id');
    const titleInput = document.getElementById('event-title');
    const dateInput = document.getElementById('event-date');
    const descInput = document.getElementById('event-description');
    const posterInput = document.getElementById('event-poster');

    // Load events on page load
    loadEvents();

    // Open modal for adding a new event
    addBtn.addEventListener('click', () => {
        openModal();
    });

    // Close modal handlers
    closeBtn.addEventListener('click', () => closeModal());
    window.addEventListener('click', e => {
        if (e.target === modal) closeModal();
    });

    // Form submit – add or update
    form.addEventListener('submit', e => {
        e.preventDefault();
        const isUpdate = Boolean(idInput.value);
        const action = isUpdate ? 'update' : 'add';
        const formData = new FormData();
        formData.append('action', action);
        if (isUpdate) formData.append('id', idInput.value);
        formData.append('title', titleInput.value.trim());
        formData.append('event_date', dateInput.value);
        formData.append('description', descInput.value.trim());
        if (posterInput.files[0]) {
            formData.append('poster', posterInput.files[0]);
        }
        fetch('backend/event_crud.php', {
            method: 'POST',
            body: formData,
        })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    alert(res.message || 'Success');
                    closeModal();
                    loadEvents();
                } else {
                    alert('Error: ' + (res.message || 'Unknown error'));
                }
            })
            .catch(err => alert('Request failed: ' + err));
    });

    // ---------- Helper Functions ----------
    function openModal(event = null) {
        if (event) {
            // Editing existing event
            modalTitle.textContent = 'Edit Event';
            idInput.value = event.id;
            titleInput.value = event.title;
            dateInput.value = event.event_date;
            descInput.value = event.description;
            // Note: we do NOT pre‑fill file input for security reasons.
        } else {
            // Adding new event
            modalTitle.textContent = 'Add Event';
            idInput.value = '';
            titleInput.value = '';
            dateInput.value = '';
            descInput.value = '';
            posterInput.value = '';
        }
        modal.style.display = 'block';
    }

    function closeModal() {
        modal.style.display = 'none';
        form.reset();
    }

    function loadEvents() {
        const params = new URLSearchParams({ action: 'list' });
        fetch('backend/event_crud.php?' + params)
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    renderEvents(res.data);
                } else {
                    tableBody.innerHTML = '<tr><td colspan="6">Failed to load events</td></tr>';
                }
            })
            .catch(() => {
                tableBody.innerHTML = '<tr><td colspan="6">Error fetching events</td></tr>';
            });
    }

    function renderEvents(events) {
        if (!Array.isArray(events) || events.length === 0) {
            tableBody.innerHTML = '<tr><td colspan="6">No events found</td></tr>';
            return;
        }
        const rows = events.map(ev => {
            const posterImg = ev.poster_path
                ? `<img src="${ev.poster_path}" alt="Poster" style="max-width:80px;max-height:60px;">`
                : '';
            return `
                <tr data-id="${ev.id}">
                    <td>${ev.id}</td>
                    <td>${escapeHtml(ev.title)}</td>
                    <td>${ev.event_date}</td>
                    <td>${escapeHtml(ev.description || '')}</td>
                    <td>${posterImg}</td>
                    <td>
                        <button class="edit-event btn">Edit</button>
                        <button class="delete-event btn">Delete</button>
                    </td>
                </tr>`;
        }).join('');
        tableBody.innerHTML = rows;
        // Attach row‑level handlers
        tableBody.querySelectorAll('.edit-event').forEach(btn => {
            btn.addEventListener('click', () => {
                const tr = btn.closest('tr');
                const id = tr.dataset.id;
                fetchEvent(id);
            });
        });
        tableBody.querySelectorAll('.delete-event').forEach(btn => {
            btn.addEventListener('click', () => {
                const tr = btn.closest('tr');
                const id = tr.dataset.id;
                if (confirm('Delete this event?')) {
                    deleteEvent(id);
                }
            });
        });
    }

    function fetchEvent(id) {
        const params = new URLSearchParams({ action: 'get', id });
        fetch('backend/event_crud.php?' + params)
            .then(r => r.json())
            .then(res => {
                if (res.success && res.data) {
                    openModal(res.data);
                } else {
                    alert('Failed to fetch event details');
                }
            })
            .catch(() => alert('Error fetching event'));
    }

    function deleteEvent(id) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        fetch('backend/event_crud.php', {
            method: 'POST',
            body: formData,
        })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    alert(res.message || 'Deleted');
                    loadEvents();
                } else {
                    alert('Delete failed: ' + (res.message || ''));
                }
            })
            .catch(err => alert('Request error: ' + err));
    }

    // Simple HTML escaping for safety
    function escapeHtml(str) {
        return str.replace(/[&<"'>]/g, tag => {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            };
            return map[tag] || tag;
        });
    }
});
