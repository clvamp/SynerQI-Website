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

        const modal = document.getElementById('bookingModal');
        const modalImg = document.getElementById('modalImg');
        const modalTitle = document.getElementById('modalTitle');
        const modalPrice = document.getElementById('modalPrice');
        const serviceSelect = document.getElementById('serviceSelect');
        const dateInput = document.getElementById('bookingDate');
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);

        function getScrollbarWidth() {
            return window.innerWidth - document.documentElement.clientWidth;
        }

        window.openGeneralModal = function() {
            const scrollbarWidth = getScrollbarWidth();
            document.body.style.paddingRight = `${scrollbarWidth}px`;
            
            // FIX: Changed back to 'flex' so it centers properly on screen
            modal.style.display = 'flex'; 
            
            setTimeout(() => modal.classList.add('active'), 10);
            
            // Stops background from scrolling
            document.body.style.overflow = 'hidden'; 
            
            // Forces the form to start at the top when opened
            const formSection = document.querySelector('.modal-form-section');
            if (formSection) formSection.scrollTop = 0;
        }

        window.closeModal = function() {
            modal.classList.remove('active');
            setTimeout(() => { 
                modal.style.display = 'none'; 
                document.body.style.overflow = 'auto';
                document.body.style.paddingRight = '0px';
            }, 300);
        }

        modal.addEventListener('click', e => { if(e.target === modal) closeModal(); });
        
        document.getElementById('modalBookingForm').addEventListener('submit', function(e) {
            // e.preventDefault();
            const service = document.getElementById('serviceSelect').value;
            const name = document.getElementById('userName').value;
            const date = document.getElementById('bookingDate').value;
            // window.location.href = `mailto:emantablizo520@gmail.com?subject=Booking: ${service}&body=Name: ${name}%0D%0ADate: ${date}`;
            closeModal();
        });