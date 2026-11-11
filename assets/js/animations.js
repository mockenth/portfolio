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

/**
 * Portfolio Animations
 * 1. Typewriter Effect (Letter by Letter)
 * 2. Scroll Reveal Animations
 */

document.addEventListener("DOMContentLoaded", () => {

  /* ==========================================
     1. TYPEWRITER EFFECT (Letter by Letter)
     ========================================== */
  const typedRoleElement = document.getElementById("typedRole");

  if (typedRoleElement) {
    // Add or change the phrases you want it to cycle through here
    const phrases = [
      "Web Development",
      "Graphic Design",
      "UI/UX Design"
    ];

    let phraseIndex = 0;
    let letterIndex = 0;
    let isDeleting = false;
    let typeSpeed = 100; // Speed of typing (ms per letter)

    function typeLetterByLetter() {
      const currentPhrase = phrases[phraseIndex];

      if (isDeleting) {
        // Remove one letter at a time
        typedRoleElement.textContent = currentPhrase.substring(0, letterIndex - 1);
        letterIndex--;
        typeSpeed = 50; // Delete faster than typing
      } else {
        // Add one letter at a time
        typedRoleElement.textContent = currentPhrase.substring(0, letterIndex + 1);
        letterIndex++;
        typeSpeed = 100; // Normal typing speed
      }

      // If the word is fully typed out
      if (!isDeleting && letterIndex === currentPhrase.length) {
        typeSpeed = 2000; // Pause at the end of the word
        isDeleting = true;
      }
      // If the word is fully deleted
      else if (isDeleting && letterIndex === 0) {
        isDeleting = false;
        phraseIndex = (phraseIndex + 1) % phrases.length; // Move to next word
        typeSpeed = 500; // Pause before typing the next word
      }

      // Call the function again based on the current speed
      setTimeout(typeLetterByLetter, typeSpeed);
    }

    // Start the effect after a short delay
    setTimeout(typeLetterByLetter, 1000);
  }


  /* ==========================================
     2. SCROLL REVEAL ANIMATIONS
     ========================================== */
  const revealElements = document.querySelectorAll(".reveal");

  if (revealElements.length > 0) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add("active");
          // Stop observing once the element is revealed
          observer.unobserve(entry.target);
        }
      });
    }, {
      root: null,
      threshold: 0.15, // Triggers when 15% of the element is visible
      rootMargin: "0px 0px -50px 0px"
    });

    revealElements.forEach(element => {
      revealObserver.observe(element);
    });
  }

});
