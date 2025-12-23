<?php
// Koneksi ke database
include 'koneksi.php'; // Pastikan file koneksi.php ada dan berisi konfigurasi koneksi database

// Contoh penggunaan koneksi (opsional, tergantung kebutuhan)
// $result = mysqli_query($koneksi, "SELECT * FROM nama_tabel");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIDCELL - Konter HP & Jasa Terlengkap</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6; /* Biru muda */
            --secondary: #2563eb; /* Biru sedang */
            --accent: #60a5fa; /* Biru muda terang */
            --light: #ffffff; /* Putih */
            --dark: #212529; /* Hitam/abu gelap */
            --success: #4ade80;
            --white-section: #ffffff; /* Warna putih untuk section */
            --blue-section: linear-gradient(135deg, #3b82f6, #2563eb); /* Gradiasi biru untuk section */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--blue-section); /* Gradiasi biru sebagai background utama */
            color: var(--light);
            min-height: 100vh;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles - Berwarna Putih */
        header {
            padding: 20px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            background: var(--light); /* Putih */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary); /* Biru muda */
        }

        .logo span {
            color: var(--accent); /* Biru muda terang */
        }

        .nav-links {
            display: flex;
            gap: 30px;
        }

        .nav-links a {
            color: var(--primary); /* Biru muda */
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--accent); /* Biru muda terang */
        }

        /* Hero Section - Berwarna Biru */
        .hero {
            padding: 160px 0 80px;
            text-align: center;
            position: relative;
            background: var(--blue-section); /* Gradiasi biru */
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.2;
            color: var(--light); /* Putih */
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            color: var(--light); /* Putih */
        }

        .cta-button {
            background: linear-gradient(45deg, var(--primary), var(--secondary));
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin: 10px;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        }

        /* Services Section - Berwarna Putih */
        .services {
            padding: 80px 0;
            background: var(--white-section); /* Putih */
            color: var(--dark); /* Hitam/abu untuk teks */
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 50px;
            font-weight: 600;
            color: var(--primary); /* Biru muda */
        }

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .service-card {
            background: var(--light); /* Putih */
            padding: 40px 30px;
            border-radius: 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid rgba(59, 130, 246, 0.1); /* Biru border transparan */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.1);
        }

        .service-icon {
            font-size: 3rem;
            color: var(--primary); /* Biru muda */
            margin-bottom: 20px;
        }

        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            font-weight: 600;
            color: var(--primary); /* Biru muda */
        }

        .service-card p {
            opacity: 0.8;
            line-height: 1.6;
            color: var(--dark); /* Hitam/abu */
        }

        /* About Section - Berwarna Putih */
        .about {
            padding: 80px 0;
            background: var(--white-section); /* Putih */
            color: var(--dark); /* Hitam/abu untuk teks */
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .about-text h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 600;
            color: var(--primary); /* Biru muda */
        }

        .about-text p {
            font-size: 1.1rem;
            line-height: 1.8;
            opacity: 0.9;
            margin-bottom: 20px;
            color: var(--dark); /* Hitam/abu */
        }

        .about-image {
            text-align: center;
        }

        .about-image img {
            max-width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        /* Stats Section - Berwarna Biru */
        .stats {
            padding: 80px 0;
            background: var(--blue-section); /* Gradiasi biru */
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            text-align: center;
        }

        .stat-item h3 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--light); /* Putih */
        }

        .stat-item p {
            font-size: 1.1rem;
            opacity: 0.9;
            color: var(--light); /* Putih */
        }

        /* Contact Section - Berwarna Putih */
        .contact {
            padding: 80px 0;
            background: var(--white-section); /* Putih */
            color: var(--dark); /* Hitam/abu untuk teks */
        }

        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            text-align: center;
        }

        .contact-item {
            padding: 30px;
        }

        .contact-icon {
            font-size: 3rem;
            color: var(--primary); /* Biru muda */
            margin-bottom: 20px;
        }

        .contact-item h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            font-weight: 600;
            color: var(--primary); /* Biru muda */
        }

        /* Footer - Berwarna Putih */
        footer {
            padding: 40px 0;
            background: var(--white-section); /* Putih */
            text-align: center;
            color: var(--dark); /* Hitam/abu */
            border-top: 1px solid rgba(59, 130, 246, 0.1); /* Biru border transparan */
        }

        .footer-content p {
            opacity: 0.8;
            margin-bottom: 20px;
            color: var(--dark); /* Hitam/abu */
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
        }

        .social-links a {
            color: var(--primary); /* Biru muda */
            font-size: 1.5rem;
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: var(--accent); /* Biru muda terang */
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .about-content {
                grid-template-columns: 1fr;
            }

            .nav-links {
                display: none;
            }
        }

        /* Animation Classes */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .animate-on-scroll.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Floating Elements */
        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <!-- Header - Berwarna Putih -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">MID<span>CELL</span></div>
                <div class="nav-links">
                    <a href="#home">Beranda</a>
                    <a href="#services">Layanan</a>
                    <a href="#about">Tentang</a>
                    <a href="#contact">Kontak</a>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section - Berwarna Biru -->
    <section id="home" class="hero">
        <div class="container">
            <h1 class="animate-on-scroll">MIDCELL - Konter HP & Jasa Terlengkap</h1>
            <p class="animate-on-scroll">Layanan konter, pembayaran, dan jasa servis terpercaya dengan harga terjangkau dan pelayanan cepat.</p>
            <a href="#services" class="cta-button animate-on-scroll">Lihat Layanan</a>
            <a href="#contact" class="cta-button animate-on-scroll" style="background: linear-gradient(45deg, #60a5fa, #3b82f6);">Hubungi Kami</a>
        </div>
    </section>

    <!-- Services Section - Berwarna Putih -->
    <section id="services" class="services">
        <div class="container">
            <h2 class="section-title animate-on-scroll">Layanan Kami</h2>
            <div class="services-grid">
                <div class="service-card animate-on-scroll">
                    <div class="service-icon floating">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Pulsa & Paket Data</h3>
                    <p>Penjualan pulsa dan paket data semua operator dengan harga bersaing dan proses instan.</p>
                </div>
                <div class="service-card animate-on-scroll">
                    <div class="service-icon floating">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3>Bayar Listrik</h3>
                    <p>Pembayaran listrik PLN prabayar dan pascabayar dengan cepat dan aman.</p>
                </div>
                <div class="service-card animate-on-scroll">
                    <div class="service-icon floating">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <h3>Transfer & Tarik Tunai</h3>
                    <p>Layanan transfer dan tarik tunai antar bank dengan biaya terjangkau.</p>
                </div>
                <div class="service-card animate-on-scroll">
                    <div class="service-icon floating">
                        <i class="fas fa-headphones"></i>
                    </div>
                    <h3>Jasa Servis HP</h3>
                    <p>Servis perangkat Android dan iPhone dengan teknisi berpengalaman.</p>
                </div>
                <div class="service-card animate-on-scroll">
                    <div class="service-icon floating">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h3>Aksesoris HP</h3>
                    <p>Penjualan aksesoris handphone original dengan harga terbaik.</p>
                </div>
                <div class="service-card animate-on-scroll">
                    <div class="service-icon floating">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                    <h3>Layanan Lainnya</h3>
                    <p>Pembayaran BPJS, Pajak, PDAM, dan berbagai layanan lainnya.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section - Berwarna Putih -->
    <section id="about" class="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text animate-on-scroll">
                    <h2>Mengapa Memilih MIDCELL?</h2>
                    <p>Kami adalah penyedia layanan konter HP dan jasa terlengkap yang telah dipercaya oleh ribuan pelanggan. Dengan pengalaman bertahun-tahun, kami menawarkan pelayanan yang cepat, aman, dan terpercaya.</p>
                    <p>Tim kami selalu siap melayani Anda 24 jam dengan teknisi profesional dan peralatan modern untuk menangani semua kebutuhan perangkat mobile Anda.</p>
                    <a href="#contact" class="cta-button">Hubungi Kami</a>
                </div>
                <div class="about-image animate-on-scroll">
                    <img src="https://images.unsplash.com/photo-1607472586893-edb6b070ac88?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&h=400&q=80" alt="Konter HP dan Jasa Servis">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section - Berwarna Biru -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item animate-on-scroll">
                    <h3>10000+</h3>
                    <p>Pelanggan Setia</p>
                </div>
                <div class="stat-item animate-on-scroll">
                    <h3>50000+</h3>
                    <p>Transaksi Harian</p>
                </div>
                <div class="stat-item animate-on-scroll">
                    <h3>99%</h3>
                    <p>Kepuasan Pelanggan</p>
                </div>
                <div class="stat-item animate-on-scroll">
                    <h3>24/7</h3>
                    <p>Dukungan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section - Berwarna Putih -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title animate-on-scroll">Hubungi Kami</h2>
            <div class="contact-info">
                <div class="contact-item animate-on-scroll">
                    <div class="contact-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Lokasi</h3>
                    <p>Jl. Contoh Alamat No. 123, Nganjuk, Jawa Timur</p>
                </div>
                <div class="contact-item animate-on-scroll">
                    <div class="contact-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3>Telepon</h3>
                    <p>0812-3456-7890</p>
                </div>
                <div class="contact-item animate-on-scroll">
                    <div class="contact-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Jam Operasional</h3>
                    <p>Setiap Hari: 08:00 - 21:00 WIB</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer - Berwarna Putih -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <p>&copy; 2025 MIDCELL. Konter HP & Jasa Terlengkap.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Scroll animation
        function animateOnScroll() {
            const elements = document.querySelectorAll('.animate-on-scroll');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('visible');
                }
            });
        }

        // Initial check
        animateOnScroll();

        // Add scroll event listener
        window.addEventListener('scroll', animateOnScroll);

        // Smooth scrolling for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add floating effect to feature icons on hover
        const serviceIcons = document.querySelectorAll('.service-icon');
        serviceIcons.forEach(icon => {
            icon.addEventListener('mouseenter', function() {
                this.style.animation = 'none';
                setTimeout(() => {
                    this.style.animation = 'floating 3s ease-in-out infinite';
                }, 10);
            });
        });
    </script>
</body>
</html>