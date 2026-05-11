let scrollToTopButton = document.getElementById("scrollToTopBtn");
        window.onscroll = function() { 
            if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
                scrollToTopButton.style.display = "flex";
            } else {
                scrollToTopButton.style.display = "none";
            }
        };
        scrollToTopButton.onclick = function() { window.scrollTo({ top: 0, behavior: 'smooth' }); }

        const hamburgerBtn = document.getElementById('hamburgerMenu');
        const hamburgerIcon = document.getElementById('hamburgerIcon');
        const mainNav = document.getElementById('mainNav');
        const ctaButton = document.getElementById('ctaButton');
        const header = document.querySelector('header');

        function toggleMenu(event) {
            if(event) event.stopPropagation();
            
            const isOpen = mainNav.classList.contains('open');
            
            if (isOpen) {
                closeMenu();
            } else {
                openMenu();
            }
        }
        
        function openMenu() {
            mainNav.classList.add('open');
            hamburgerBtn.classList.add('active');
            hamburgerIcon.innerText = "close"; 
        }
        
        function closeMenu() {
            mainNav.classList.remove('open');
            hamburgerBtn.classList.remove('active');
            hamburgerIcon.innerText = "menu";
        }

        hamburgerBtn.onclick = toggleMenu;
        
        // Close menu when clicking outside of it
        document.addEventListener('click', function(event) {
            const isClickInsideNav = mainNav.contains(event.target);
            const isClickOnHamburger = hamburgerBtn.contains(event.target);
            const isMenuOpen = mainNav.classList.contains('open');
            
            if (isMenuOpen && !isClickInsideNav && !isClickOnHamburger) {
                closeMenu();
            }
        });

        const navLinks = mainNav.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', closeMenu);
        });

        const fadeElements = document.querySelectorAll('.fade-in');
        const fadeInOnScroll = () => {
            fadeElements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const windowHeight = window.innerHeight;
                if (elementTop < windowHeight - 80) {
                    element.classList.add('visible');
                }
            });
        };
        window.addEventListener('scroll', fadeInOnScroll);
        fadeInOnScroll(); 

        // Navbar only handles menu, scroll, and fade-in behavior.
        // Booking modal open/close and form submission are handled in script/bookapp.js.
