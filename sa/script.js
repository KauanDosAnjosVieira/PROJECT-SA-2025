// Mobile Menu Toggle
const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
const navLinks = document.querySelector('.nav-links');

mobileMenuBtn.addEventListener('click', () => {
  navLinks.classList.toggle('active');
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
  if (!e.target.closest('.mobile-menu-btn') && !e.target.closest('.nav-links')) {
    navLinks.classList.remove('active');
  }
});

// Stats Animation
const stats = document.querySelectorAll('.stat-number');
let animated = false;

function animateStats() {
  stats.forEach(stat => {
    const target = parseInt(stat.getAttribute('data-target'));
    const duration = 2000; // 2 seconds
    const step = target / (duration / 16); // 60fps
    let current = 0;

    const counter = setInterval(() => {
      current += step;
      if (current >= target) {
        stat.textContent = target.toLocaleString();
        clearInterval(counter);
      } else {
        stat.textContent = Math.floor(current).toLocaleString();
      }
    }, 16);
  });
}

// Trigger stats animation when scrolling into view
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting && !animated) {
      animateStats();
      animated = true;
    }
  });
});

const statsSection = document.querySelector('.stats');
if (statsSection) {
  observer.observe(statsSection);
}

// Smooth scroll for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function (e) {
    e.preventDefault();
    const targetId = this.getAttribute('href');
    if (targetId === '#') return;
    
    const target = document.querySelector(targetId);
    if (target) {
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
      });
      // Close mobile menu after clicking
      navLinks.classList.remove('active');
    }
  });
});

// Add scroll animation to feature cards
const featureCards = document.querySelectorAll('.feature-card');
const fadeInOptions = {
  threshold: 0.3,
  rootMargin: '0px 0px -50px 0px'
};

const fadeInObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
      fadeInObserver.unobserve(entry.target);
    }
  });
}, fadeInOptions);

featureCards.forEach(card => {
  card.style.opacity = '0';
  card.style.transform = 'translateY(20px)';
  card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
  fadeInObserver.observe(card);
});
