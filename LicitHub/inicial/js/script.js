// Função para verificar se o usuário está logado
function estaLogado() {
  return localStorage.getItem('usuarioLogado') === 'true';
}

// Função para atualizar status de login/cadastro no topo da página
function atualizarStatusUsuario() {
  const authStatus = document.getElementById('auth-status');
  const usuarioLogado = estaLogado();
  const nomeUsuario = localStorage.getItem('nomeUsuario') || 'Usuário';

  if (authStatus) {
    if (usuarioLogado) {
      authStatus.innerHTML = `
        <span>Bem-vindo(a), <strong>${nomeUsuario}</strong>!</span>
        <a href="#" id="btn-logout" class="btn btn-outline">Sair</a>
      `;

      const btnLogout = document.getElementById('btn-logout');
      btnLogout.addEventListener('click', function (e) {
        e.preventDefault();
        logout();
        location.reload();
      });
    } else {
      authStatus.innerHTML = `
        <a href="../login/login.html" class="btn btn-outline">Entrar</a>
        <a href="../login/cadastro.html" class="btn btn-primary">Cadastrar</a>
      `;
    }
  }
}

// Função para login e logout (controla o localStorage)
function login(nomeUsuario) {
  localStorage.setItem('usuarioLogado', 'true');
  localStorage.setItem('nomeUsuario', nomeUsuario || 'Usuário');
}

function logout() {
  localStorage.removeItem('usuarioLogado');
  localStorage.removeItem('nomeUsuario');
}

// Quando o DOM carregar
document.addEventListener('DOMContentLoaded', () => {
  atualizarStatusUsuario();

  // Botão "Comece agora"
  const botaoComeceAgora = document.getElementById('btn-comece-agora');
  if (botaoComeceAgora) {
    botaoComeceAgora.addEventListener('click', function (event) {
      event.preventDefault();

      if (estaLogado()) {
        const planosSection = document.getElementById('planos');
        if (planosSection) {
          planosSection.scrollIntoView({ behavior: 'smooth' });
        }
      } else {
        window.location.href = '../login/cadastro.html'; // Redireciona para cadastro
      }
    });
  }
});

// --------------------------------------------
// Abaixo o restante do seu código já existente
// --------------------------------------------

// Mobile Menu Toggle
const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
const navLinks = document.querySelector('.nav-links');

if (mobileMenuBtn) {
  mobileMenuBtn.addEventListener('click', () => {
    mobileMenuBtn.classList.toggle('active');
    navLinks.classList.toggle('active');

    const spans = mobileMenuBtn.querySelectorAll('span');
    if (mobileMenuBtn.classList.contains('active')) {
      spans[0].style.transform = 'rotate(45deg) translate(6px, 6px)';
      spans[1].style.opacity = '0';
      spans[2].style.transform = 'rotate(-45deg) translate(6px, -6px)';
    } else {
      spans[0].style.transform = 'none';
      spans[1].style.opacity = '1';
      spans[2].style.transform = 'none';
    }
  });
}

// Fechar menu mobile ao clicar em um link
const menuItems = document.querySelectorAll('.nav-links a');
menuItems.forEach(item => {
  item.addEventListener('click', () => {
    navLinks.classList.remove('active');
    if (mobileMenuBtn.classList.contains('active')) {
      mobileMenuBtn.click();
    }
  });
});

// Header scroll effect
const header = document.querySelector('.header');
window.addEventListener('scroll', () => {
  if (window.scrollY > 50) {
    header.style.padding = '10px 0';
    header.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
  } else {
    header.style.padding = '15px 0';
    header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
  }
});

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) {
      window.scrollTo({
        top: target.offsetTop - 80,
        behavior: 'smooth'
      });
    }
  });
});

// Testimonial slider
const testimonialSlides = document.querySelectorAll('.testimonial-slide');
const dots = document.querySelectorAll('.dot');
let currentSlide = 0;
const slideInterval = 5000;

function showSlide(index) {
  testimonialSlides.forEach(slide => slide.classList.remove('active'));
  dots.forEach(dot => dot.classList.remove('active'));

  testimonialSlides[index].classList.add('active');
  dots[index].classList.add('active');
  currentSlide = index;
}

showSlide(0);

let slideTimer = setInterval(() => {
  currentSlide = (currentSlide + 1) % testimonialSlides.length;
  showSlide(currentSlide);
}, slideInterval);

dots.forEach((dot, index) => {
  dot.addEventListener('click', () => {
    clearInterval(slideTimer);
    showSlide(index);
    slideTimer = setInterval(() => {
      currentSlide = (currentSlide + 1) % testimonialSlides.length;
      showSlide(currentSlide);
    }, slideInterval);
  });
});

// Counter animation
const counters = document.querySelectorAll('.counter-number');
const speed = 200;

function animateCounters() {
  counters.forEach(counter => {
    const target = parseInt(counter.getAttribute('data-count'));
    let count = 0;
    const increment = Math.ceil(target / speed);

    function updateCount() {
      if (count < target) {
        count += increment;
        if (count > target) count = target;
        counter.textContent = count;
        setTimeout(updateCount, 10);
      }
    }

    updateCount();
  });
}

// Check if element is in viewport
function isInViewport(element) {
  const rect = element.getBoundingClientRect();
  return (
    rect.top >= 0 &&
    rect.left >= 0 &&
    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
    rect.right <= (window.innerWidth || document.documentElement.clientWidth)
  );
}

const counterSection = document.querySelector('.counter-section');
let animated = false;

window.addEventListener('scroll', () => {
  if (counterSection && isInViewport(counterSection) && !animated) {
    animateCounters();
    animated = true;
  }
});

if (counterSection && isInViewport(counterSection)) {
  animateCounters();
  animated = true;
}
