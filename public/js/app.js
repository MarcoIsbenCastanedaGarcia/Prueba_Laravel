document.addEventListener('DOMContentLoaded', () => {
  // --- MODO OSCURO / CLARO ---
  const themeToggleBtn = document.getElementById('themeToggleBtn');
  const themeIcon = document.getElementById('themeIcon');
  const themeText = document.getElementById('themeText');

  const currentTheme = localStorage.getItem('theme') || 'light';
  document.documentElement.setAttribute('data-theme', currentTheme);
  updateThemeUI(currentTheme);

  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', () => {
      const newTheme =
        document.documentElement.getAttribute('data-theme') === 'dark'
          ? 'light'
          : 'dark';

      document.documentElement.setAttribute('data-theme', newTheme);
      localStorage.setItem('theme', newTheme);
      updateThemeUI(newTheme);
    });
  }

  function updateThemeUI(theme) {
    if (!themeIcon || !themeText) return;
    if (theme === 'dark') {
      themeIcon.textContent = '☀️';
      themeText.textContent = 'Modo Claro';
    } else {
      themeIcon.textContent = '🌙';
      themeText.textContent = 'Modo Oscuro';
    }
  }

  // --- MANEJO DE MODALES ---
  window.openModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.add('active');
  };

  window.closeModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (modal) modal.classList.remove('active');
  };
});