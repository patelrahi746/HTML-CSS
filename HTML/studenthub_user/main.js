// main.js – handles burger menu toggle, theme switch (placeholder), and simple UI interactions
document.addEventListener('DOMContentLoaded', function() {
  const menuToggle = document.getElementById('menu-toggle');
  const nav = document.getElementById('nav');
  if (menuToggle && nav) {
    menuToggle.addEventListener('click', function() {
      nav.classList.toggle('active');
    });
  }

  // Theme toggle (light/dark) – persists in localStorage
  const themeToggle = document.getElementById('theme-toggle');
  if (themeToggle) {
    const current = localStorage.getItem('theme') || 'light';
    document.body.dataset.theme = current;
    themeToggle.textContent = current === 'dark' ? 'Light Mode' : 'Dark Mode';
    themeToggle.addEventListener('click', function() {
      const newTheme = document.body.dataset.theme === 'dark' ? 'light' : 'dark';
      document.body.dataset.theme = newTheme;
      localStorage.setItem('theme', newTheme);
      this.textContent = newTheme === 'dark' ? 'Light Mode' : 'Dark Mode';
    });
  }
});
