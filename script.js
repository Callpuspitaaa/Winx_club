document.addEventListener('DOMContentLoaded', () => {
    // --- Logic untuk Hamburger Menu ---
    const menuToggle = document.getElementById('menu-toggle');
    const menuNav = document.getElementById('menu-nav');

    if (menuToggle && menuNav) {
        menuToggle.addEventListener('click', () => {
            menuNav.classList.toggle('is-active');
            menuToggle.classList.toggle('is-active');
        });
    }

    // --- Logic untuk Animasi saat Scroll ---
    const revealElements = document.querySelectorAll('.reveal');

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });

    revealElements.forEach(element => {
        revealObserver.observe(element);
    });

    // --- Logic untuk Header Transparan saat di atas ---
    const header = document.querySelector('header');
    if (header) {
        const headerObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    header.classList.remove('scrolled');
                } else {
                    header.classList.add('scrolled');
                }
            });
        }, { rootMargin: "-50px 0px 0px 0px" });

        // Element dummy di bawah header untuk diobservasi
        const heroSection = document.querySelector('.hero'); // Asumsi ada section hero
        if(heroSection) {
            headerObserver.observe(heroSection);
        }
    }

    // --- Logic untuk Lightbox Galeri ---
    const modal = document.getElementById("lightbox-modal");
    const modalImg = document.getElementById("lightbox-image");
    const galleryItems = document.querySelectorAll(".gallery-item img");
    const closeBtn = document.querySelector(".lightbox-close");

    if (modal && modalImg && galleryItems.length > 0 && closeBtn) {
        galleryItems.forEach(item => {
            item.addEventListener('click', () => {
                modal.style.display = "block";
                modalImg.src = item.src;
            });
        });

        closeBtn.addEventListener('click', () => {
            modal.style.display = "none";
        });

        // Close modal when clicking outside the image
        window.addEventListener('click', (event) => {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        });
    }
});