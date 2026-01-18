<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Fullstack Web Developer based in Johor Bahru. Specializing in modern web applications, ERP systems, CRM systems, and ecommerce platforms.">
    <meta name="keywords" content="Fullstack Developer, Web Developer, Johor Bahru, ERP Systems, CRM Systems, Ecommerce">
    <title>Fullstack Web Developer | Johor Bahru</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('frontend/css/style.css') ?>">
</head>
<body>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#home">Portfolio</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#skills">Skills</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#portfolio">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="hero-content">
                        <h1>Fullstack Web Developer</h1>
                        <p class="lead">Building modern, scalable web applications with passion and precision</p>
                        <div class="location-wrapper">
                            <span class="location-divider"></span>
                            <span class="location-text">
                                <i class="bi bi-geo-alt"></i>
                                <span>Johor Bahru, Malaysia</span>
                            </span>
                            <span class="location-divider"></span>
                        </div>
                        <div class="hero-buttons">
                            <a href="#portfolio" class="btn btn-primary-custom">View My Work</a>
                            <a href="#contact" class="btn btn-outline-custom">Get In Touch</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="section about-section">
        <div class="container">
            <div class="section-title fade-in-up">
                <h2>About Me</h2>
                <p>Get to know more about my background and expertise</p>
            </div>
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-text fade-in-up">
                        <p>
                            I'm a passionate <span class="highlight">Fullstack Web Developer</span> based in Johor Bahru, Malaysia, 
                            dedicated to creating exceptional digital experiences. With expertise spanning both frontend and backend 
                            development, I bring ideas to life through clean, efficient code and user-centered design.
                        </p>
                        <p>
                            My journey in web development has led me to work on diverse projects, from sleek personal and company 
                            profile websites to complex enterprise solutions like ERP and CRM systems. I specialize in building 
                            robust ecommerce platforms that drive business growth and deliver seamless user experiences.
                        </p>
                        <p>
                            I believe in writing maintainable code, following best practices, and staying updated with the latest 
                            technologies. Every project is an opportunity to solve problems creatively and deliver value to clients 
                            and end-users alike.
                        </p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-image fade-in-up">
                        <div style="background: linear-gradient(135deg, #005c5a, #007a77); height: 400px; border-radius: 20px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(0, 92, 90, 0.2);">
                            <i class="bi bi-code-slash" style="font-size: 8rem; color: rgba(255, 255, 255, 0.3);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section id="skills" class="section skills-section">
        <div class="container">
            <div class="section-title fade-in-up">
                <h2>Technical Skills</h2>
                <p>Technologies and tools I work with</p>
            </div>
            <div class="skills-grid">
                <div class="skill-category fade-in-up">
                    <h3><i class="bi bi-browser-chrome"></i> Frontend</h3>
                    <ul class="skill-list">
                        <li><i class="bi bi-check-circle-fill"></i> HTML5 & CSS3</li>
                        <li><i class="bi bi-check-circle-fill"></i> JavaScript (ES6+)</li>
                        <li><i class="bi bi-check-circle-fill"></i> Bootstrap CSS</li>
                        <li><i class="bi bi-check-circle-fill"></i> Responsive Design</li>
                    </ul>
                </div>
                <div class="skill-category fade-in-up">
                    <h3><i class="bi bi-server"></i> Backend</h3>
                    <ul class="skill-list">
                        <li><i class="bi bi-check-circle-fill"></i> PHP (CodeIgniter)</li>
                        <li><i class="bi bi-check-circle-fill"></i> RESTful APIs</li>
                        <li><i class="bi bi-check-circle-fill"></i> Database Design (MySQL)</li>
                        <li><i class="bi bi-check-circle-fill"></i> Authentication & Security</li>
                    </ul>
                </div>
                <div class="skill-category fade-in-up">
                    <h3><i class="bi bi-tools"></i> Tools & Others</h3>
                    <ul class="skill-list">
                        <li><i class="bi bi-check-circle-fill"></i> Git & Version Control</li>
                        <li><i class="bi bi-check-circle-fill"></i> Cloud Services (Linode)</li>
                        <li><i class="bi bi-check-circle-fill"></i> Testing & Debugging</li>
                        <li><i class="bi bi-check-circle-fill"></i> Agile Methodologies</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Section -->
    <section id="portfolio" class="section portfolio-section">
        <div class="container">
            <div class="section-title fade-in-up">
                <h2>Portfolio</h2>
                <p>Some of my recent projects and work</p>
            </div>
            <div class="portfolio-grid">
                <!-- Portfolio Item 1: Personal/Company Profile -->
                <div class="portfolio-card fade-in-up">
                    <div class="portfolio-card-image">
                        <span class="portfolio-card-badge">Profile Website</span>
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div class="portfolio-card-body">
                        <h3>Personal & Company Profiles</h3>
                        <p>
                            Modern, responsive profile websites that showcase personal brands and company information. 
                            Built with clean design principles and optimized for performance and SEO.
                        </p>
                        <div class="portfolio-card-tags">
                            <span class="portfolio-card-tag">HTML/CSS</span>
                            <span class="portfolio-card-tag">JavaScript</span>
                            <span class="portfolio-card-tag">Responsive</span>
                        </div>
                    </div>
                </div>

                <!-- Portfolio Item 2: ERP System -->
                <div class="portfolio-card fade-in-up">
                    <div class="portfolio-card-image">
                        <span class="portfolio-card-badge">ERP System</span>
                        <i class="bi bi-diagram-3"></i>
                    </div>
                    <div class="portfolio-card-body">
                        <h3>Enterprise Resource Planning</h3>
                        <p>
                            Comprehensive ERP solutions for managing business operations, including inventory, 
                            finance, HR, and supply chain management. Scalable architecture with robust security.
                        </p>
                        <div class="portfolio-card-tags">
                            <span class="portfolio-card-tag">PHP</span>
                            <span class="portfolio-card-tag">MySQL</span>
                            <span class="portfolio-card-tag">API</span>
                            <span class="portfolio-card-tag">Reporting</span>
                            <span class="portfolio-card-tag">Analytics</span>
                        </div>
                    </div>
                </div>

                <!-- Portfolio Item 3: CRM System -->
                <div class="portfolio-card fade-in-up">
                    <div class="portfolio-card-image">
                        <span class="portfolio-card-badge">CRM System</span>
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="portfolio-card-body">
                        <h3>Customer Relationship Management</h3>
                        <p>
                            Advanced CRM platforms for managing customer interactions, sales pipelines, and marketing 
                            campaigns. Features include analytics, automation, and integration capabilities.
                        </p>
                        <div class="portfolio-card-tags">
                            <span class="portfolio-card-tag">Automation</span>
                            <span class="portfolio-card-tag">Analytics</span>
                            <span class="portfolio-card-tag">Workflow</span>
                            <span class="portfolio-card-tag">Reporting</span>
                        </div>
                    </div>
                </div>

                <!-- Portfolio Item 4: Ecommerce Platform -->
                <div class="portfolio-card fade-in-up">
                    <div class="portfolio-card-image">
                        <span class="portfolio-card-badge">Ecommerce</span>
                        <i class="bi bi-cart-check"></i>
                    </div>
                    <div class="portfolio-card-body">
                        <h3>Ecommerce Platforms</h3>
                        <p>
                            Full-featured ecommerce solutions with shopping cart, payment integration, order management, 
                            and admin dashboard. Built for scalability and excellent user experience.
                        </p>
                        <div class="portfolio-card-tags">
                            <span class="portfolio-card-tag">Fullstack</span>
                            <span class="portfolio-card-tag">Payment Gateway</span>
                            <span class="portfolio-card-tag">Admin Panel</span>
                            <span class="portfolio-card-tag">Reporting</span>
                            <span class="portfolio-card-tag">Analytics</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="section contact-section">
        <div class="container">
            <div class="section-title fade-in-up">
                <h2>Get In Touch</h2>
                <p>Have a project in mind? Let's discuss how we can work together</p>
            </div>
            <div class="contact-content">
                <div class="contact-info fade-in-up">
                    <div class="contact-item">
                        <i class="bi bi-envelope-fill"></i>
                        <div class="contact-item-content">
                            <h4>Email</h4>
                            <p>contact@example.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <div class="contact-item-content">
                            <h4>Location</h4>
                            <p>Johor Bahru, Malaysia</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <i class="bi bi-clock-fill"></i>
                        <div class="contact-item-content">
                            <h4>Availability</h4>
                            <p>Open to new projects and opportunities</p>
                        </div>
                    </div>
                </div>
                <div class="contact-form fade-in-up">
                    <form id="contactForm">
                        <div class="form-group">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="your.email@example.com" required>
                        </div>
                        <div class="form-group">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Subject" required>
                        </div>
                        <div class="form-group">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" placeholder="Your message..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-submit">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="social-links">
                <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" aria-label="Email"><i class="bi bi-envelope"></i></a>
            </div>
            <p>&copy; <?= date('Y') ?> Fullstack Web Developer. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?= base_url('frontend/js/main.js') ?>"></script>
</body>
</html>
