// js/tema.js

// Aplica o tema salvo no localStorage
document.addEventListener("DOMContentLoaded", function () {
    const savedTheme = localStorage.getItem("tema");
    if (savedTheme === "dark") {
      document.body.classList.add("dark-mode");
    }
  });

  const toggle = document.querySelector('.menu-toggle');
  const sidebar = document.querySelector('.sidebar');

  toggle.addEventListener('click', () => {
    sidebar.classList.toggle('active');
  });
  