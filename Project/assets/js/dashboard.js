document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtn = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const themeText = document.getElementById('themeText');
    const body = document.body;

    // Check saved theme preference in browser storage
    const savedTheme = localStorage.getItem('theme') || 'dark';

    function applyTheme(theme) {
        if (theme === 'light') {
            body.classList.remove('dark-theme');
            body.classList.add('light-theme');
            themeIcon.textContent = '🌙';
            themeText.textContent = 'Dark Mode';
        } else {
            body.classList.remove('light-theme');
            body.classList.add('dark-theme');
            themeIcon.textContent = '☀️';
            themeText.textContent = 'Light Mode';
        }
    }

    // Apply preference on page load
    applyTheme(savedTheme);

    // Toggle click event
    themeToggleBtn.addEventListener('click', () => {
        const isDark = body.classList.contains('dark-theme');
        const newTheme = isDark ? 'light' : 'dark';
        
        applyTheme(newTheme);
        localStorage.setItem('theme', newTheme);
    });
});