/**
 * Portfolio Website - Main JavaScript
 * Handles smooth scroll, animations, form validation, and navigation
 */

(function() {
    'use strict';

    // ============================================
    // Smooth Scroll Navigation
    // ============================================
    
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth scroll for anchor links
        const navLinks = document.querySelectorAll('a[href^="#"]');
        
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                
                // Skip if it's just "#"
                if (href === '#') return;
                
                const target = document.querySelector(href);
                
                if (target) {
                    e.preventDefault();
                    
                    // Dynamically calculate navbar height based on screen size
                    const navbar = document.querySelector('.navbar');
                    const navbarHeight = navbar ? navbar.offsetHeight : 80;
                    const offsetTop = target.offsetTop - navbarHeight;
                    
                    window.scrollTo({
                        top: offsetTop,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse.classList.contains('show')) {
                        const bsCollapse = new bootstrap.Collapse(navbarCollapse, {
                            toggle: true
                        });
                    }
                }
            });
        });

        // ============================================
        // Navbar Scroll Effect
        // ============================================
        
        const navbar = document.querySelector('.navbar');
        let lastScroll = 0;
        
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            
            lastScroll = currentScroll;
        });

        // ============================================
        // Active Navigation Highlighting
        // ============================================
        
        const sections = document.querySelectorAll('.section[id]');
        const navItems = document.querySelectorAll('.navbar-nav .nav-link');
        
        function highlightActiveSection() {
            // Dynamically calculate offset based on navbar height
            const navbar = document.querySelector('.navbar');
            const navbarHeight = navbar ? navbar.offsetHeight : 80;
            const scrollPosition = window.pageYOffset + navbarHeight + 20;
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionId = section.getAttribute('id');
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    navItems.forEach(item => {
                        item.classList.remove('active');
                        if (item.getAttribute('href') === `#${sectionId}`) {
                            item.classList.add('active');
                        }
                    });
                }
            });
        }
        
        window.addEventListener('scroll', highlightActiveSection);
        highlightActiveSection(); // Initial call

        // ============================================
        // Scroll-triggered Animations
        // ============================================
        
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);
        
        // Observe all elements with fade-in-up class
        const animatedElements = document.querySelectorAll('.fade-in-up');
        animatedElements.forEach(el => observer.observe(el));

        // ============================================
        // Contact Form Validation
        // ============================================
        
        const contactForm = document.getElementById('contactForm');
        
        if (contactForm) {
            const formFields = {
                name: contactForm.querySelector('#name'),
                email: contactForm.querySelector('#email'),
                subject: contactForm.querySelector('#subject'),
                message: contactForm.querySelector('#message')
            };
            
            const submitButton = contactForm.querySelector('.btn-submit');
            
            // Real-time validation
            Object.keys(formFields).forEach(fieldName => {
                const field = formFields[fieldName];
                if (field) {
                    field.addEventListener('blur', function() {
                        validateField(fieldName, field);
                    });
                    
                    field.addEventListener('input', function() {
                        if (field.classList.contains('is-invalid')) {
                            validateField(fieldName, field);
                        }
                    });
                }
            });
            
            // Form submission
            contactForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                let isValid = true;
                
                // Validate all fields
                Object.keys(formFields).forEach(fieldName => {
                    const field = formFields[fieldName];
                    if (field && !validateField(fieldName, field)) {
                        isValid = false;
                    }
                });
                
                if (isValid) {
                    // Disable submit button
                    submitButton.disabled = true;
                    submitButton.textContent = 'Sending...';
                    
                    // Here you would normally submit to API
                    // For now, just show success message
                    setTimeout(function() {
                        showFormMessage('success', 'Thank you for your message! I will get back to you soon.');
                        contactForm.reset();
                        submitButton.disabled = false;
                        submitButton.textContent = 'Send Message';
                        
                        // Remove validation classes
                        Object.values(formFields).forEach(field => {
                            if (field) {
                                field.classList.remove('is-invalid');
                            }
                        });
                    }, 1500);
                } else {
                    // Scroll to first invalid field
                    const firstInvalid = contactForm.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalid.focus();
                    }
                }
            });
        }
        
        function validateField(fieldName, field) {
            const value = field.value.trim();
            let isValid = true;
            let errorMessage = '';
            
            // Remove existing feedback
            const existingFeedback = field.parentElement.querySelector('.invalid-feedback');
            if (existingFeedback) {
                existingFeedback.remove();
            }
            field.classList.remove('is-invalid');
            
            // Validation rules
            switch(fieldName) {
                case 'name':
                    if (value === '') {
                        isValid = false;
                        errorMessage = 'Name is required';
                    } else if (value.length < 2) {
                        isValid = false;
                        errorMessage = 'Name must be at least 2 characters';
                    }
                    break;
                    
                case 'email':
                    if (value === '') {
                        isValid = false;
                        errorMessage = 'Email is required';
                    } else if (!isValidEmail(value)) {
                        isValid = false;
                        errorMessage = 'Please enter a valid email address';
                    }
                    break;
                    
                case 'subject':
                    if (value === '') {
                        isValid = false;
                        errorMessage = 'Subject is required';
                    } else if (value.length < 3) {
                        isValid = false;
                        errorMessage = 'Subject must be at least 3 characters';
                    }
                    break;
                    
                case 'message':
                    if (value === '') {
                        isValid = false;
                        errorMessage = 'Message is required';
                    } else if (value.length < 10) {
                        isValid = false;
                        errorMessage = 'Message must be at least 10 characters';
                    }
                    break;
            }
            
            if (!isValid) {
                field.classList.add('is-invalid');
                const feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.textContent = errorMessage;
                field.parentElement.appendChild(feedback);
            }
            
            return isValid;
        }
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        
        function showFormMessage(type, message) {
            // Remove existing message
            const existingMessage = document.querySelector('.form-message');
            if (existingMessage) {
                existingMessage.remove();
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `form-message alert alert-${type === 'success' ? 'success' : 'danger'}`;
            messageDiv.textContent = message;
            messageDiv.style.marginTop = '1rem';
            messageDiv.style.padding = '1rem';
            messageDiv.style.borderRadius = '10px';
            
            if (type === 'success') {
                messageDiv.style.backgroundColor = '#d4edda';
                messageDiv.style.color = '#155724';
                messageDiv.style.border = '1px solid #c3e6cb';
            }
            
            contactForm.appendChild(messageDiv);
            
            // Scroll to message
            messageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Remove message after 5 seconds
            setTimeout(function() {
                messageDiv.style.transition = 'opacity 0.3s ease';
                messageDiv.style.opacity = '0';
                setTimeout(function() {
                    messageDiv.remove();
                }, 300);
            }, 5000);
        }

        // ============================================
        // Portfolio Card Hover Effects Enhancement
        // ============================================
        
        const portfolioCards = document.querySelectorAll('.portfolio-card');
        portfolioCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transition = 'all 0.3s ease';
            });
        });

        // ============================================
        // Mobile Menu Close on Link Click
        // ============================================
        
        const mobileNavLinks = document.querySelectorAll('.navbar-nav .nav-link');
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarCollapse = document.querySelector('.navbar-collapse');
        
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
                    if (bsCollapse) {
                        bsCollapse.hide();
                    }
                }
            });
        });
    });

})();
