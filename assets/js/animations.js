/*(() => {
  const els = document.querySelectorAll('.reveal');

  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver(entries => entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        io.unobserve(e.target);
      }
    }), { threshold: .12 });

    els.forEach(el => io.observe(el));
  } else {
    els.forEach(el => el.classList.add('visible'));
  }
})(); */

// ==========================================
// Typewriter Effect
// ==========================================
document.addEventListener("DOMContentLoaded", () => {
  const typedRoleElement = document.getElementById("typedRole");

  // Add or remove words you want to cycle through here
  const words = ["Web Development", "Graphic Design", "UI/UX Design", "Software Engineering"];

  let wordIndex = 0;
  let charIndex = 0;
  let isDeleting = false;
  let typeSpeed = 100;

  function type() {
    const currentWord = words[wordIndex];

    if (isDeleting) {
      // Remove a character
      typedRoleElement.textContent = currentWord.substring(0, charIndex - 1);
      charIndex--;
      typeSpeed = 50; // Deleting speed
    } else {
      // Add a character
      typedRoleElement.textContent = currentWord.substring(0, charIndex + 1);
      charIndex++;
      typeSpeed = 100; // Typing speed
    }

    // Handle word completion
    if (!isDeleting && charIndex === currentWord.length) {
      typeSpeed = 2000; // Pause at the end of the word
      isDeleting = true;
    }
    // Handle word deletion completion
    else if (isDeleting && charIndex === 0) {
      isDeleting = false;
      wordIndex = (wordIndex + 1) % words.length; // Move to next word
      typeSpeed = 500; // Pause before typing next word
    }

    setTimeout(type, typeSpeed);
  }

  // Start the typewriter effect after a short delay
  if (typedRoleElement) {
    setTimeout(type, 1000);
  }
});
