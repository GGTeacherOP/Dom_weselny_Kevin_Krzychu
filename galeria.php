<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bursztynowy Pałac - Dom Weselny</title>
    <meta name="description" content="Bursztynowy Pałac - wyjątkowe miejsce na Twoje wymarzone wesele. Odkryj nasze sale, galerię i opinie zadowolonych klientów.">
    <link rel="stylesheet" href="galeria.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="top-border"></div>
    
    <div class="main-container">
        <header>
            <div class="img_header">
                <a href="index.php"><img class="logo_header" src="logo.png" alt="dom_weselny"></a>
                <img class="logo_txt_header" src="logo_txt.png" alt="dom_weselny_txt">
            </div>
            <div class="link_header">
                <a href="sale.php" style="word-spacing: 15px">Sale</a>
                <a href="galeria.php"style="word-spacing: 15px">Galeria</a>
                <a href="kontakt.php"style="word-spacing: 15px">Kontakt</a>
                <a href="opinie.php"style="word-spacing: 15px">Opinie</a>
                
                <?php if (isset($_SESSION['logged_in'])): ?>
                    <span style="margin-right: 15px; color: var(--amber-darker);">Witaj, <?php echo htmlspecialchars($_SESSION['imie']); ?>!</span>
                    <?php 
                    $panel_link = '';
                    switch($_SESSION['role']) {
                        case 'admin':
                            $panel_link = 'panel.php';
                            break;
                        case 'manager':
                            $panel_link = 'panel.php';
                            break;
                        case 'klient':
                            $panel_link = 'panel.php';
                            break;
                        case 'kelner':
                            $panel_link = 'panel.php';
                            break;
                        case 'sprzataczka':
                            $panel_link = 'panel.php';
                            break;
                        case 'kucharz':
                            $panel_link = 'panel.php';
                            break;
                        default:
                            $panel_link = 'panel.php';
                    }
                    ?>
                    <a href="<?php echo $panel_link; ?>" class="header-link">Mój Panel</a>
                    <a href="logout.php" class="header-link">Wyloguj</a>
                <?php else: ?>
                    <a href="login.php" class="header-link">Zaloguj się</a>
                    <a href="login.php" class="header-link">Rejestracja</a>
                <?php endif; ?>
            </div>
        </header>
        
        <div class="container">
            <h1 class="hero-title">Galeria</h1>
            
            <section id="photos">
                <h2 class="section-title">Nasze Przestrzenie</h2>
                <div class="gallery">
                    <div class="gallery-item" onclick="openModal('w1.jpg')">
                        <img src="w1.jpg" alt="Sala weselna">
                        <div class="gallery-caption">Sala Balowa</div>
                    </div>
                    
                    <div class="gallery-item" onclick="openModal('w2.jpg')">
                        <img src="w2.jpg" alt="Ogród weselny">
                        <div class="gallery-caption">Ogród Weselny</div>
                    </div>
                    
                    <div class="gallery-item" onclick="openModal('w3.jpg')">
                        <img src="w3.jpg" alt="Stoły weselne">
                        <div class="gallery-caption">Przyjęcie Weselne</div>
                    </div>
                    
                    <div class="gallery-item" onclick="openModal('https://images.unsplash.com/photo-1523438885200-e635ba2c371e?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80')">
                        <img src="https://images.unsplash.com/photo-1523438885200-e635ba2c371e?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Wesele w naszym obiekcie">
                        <div class="gallery-caption">Wesele 2023</div>
                    </div>
                    
                    <div class="gallery-item" onclick="openModal('https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80')">
                        <img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Przyjęcie weselne">
                        <div class="gallery-caption">Przyjęcie 2022</div>
                    </div>
                    
                    <div class="gallery-item" onclick="openModal('https://images.unsplash.com/photo-1511795409834-ef04bbd61622?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80')">
                        <img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" alt="Tort weselny">
                        <div class="gallery-caption">Tort Weselny</div>
                    </div>
                </div>
            </section>
            
            <section id="videos">
                <h2 class="section-title">Filmy Promocyjne</h2>
                
                <div class="video-container">
                    <iframe src="https://www.youtube.com/embed/1g4zj-shsVU" title="Prezentacja sali weselnej" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                <div class="video-caption">Prezentacja sali weselnej</div>
                
                <div class="video-container">
                    <iframe src="https://www.youtube.com/embed/kFAui_qBpxg" title="Wesele Anny i Marka" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                </div>
                <div class="video-caption">Wesele Anny i Marka</div>
            </section>
        </div>
        
        <footer class="page-footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-heading">Bursztynowy Pałac</h3>
                    <p class="footer-text">Twoje wymarzone wesele w wyjątkowej scenerii. Z nami stworzysz niezapomniane chwile.</p>

                </div>
                
                <div class="footer-section">
                    <h3 class="footer-heading">Kontakt</h3>
                    <ul class="footer-links">
                        <li><a href="tel:+48786020787" class="footer-link"><i class="fas fa-phone-alt"></i> +48 786 020 787</a></li>
                        <li><a href="mailto:kontakt@bursztynowypalac.pl" class="footer-link"><i class="fas fa-envelope"></i> kontakt@bursztynowypalac.pl</a></li>
                        <li><a href="#" class="footer-link"><i class="fas fa-map-marker-alt"></i> ul. Weselna 123, 00-001 Warszawa</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-heading">Godziny otwarcia</h3>
                    <p class="footer-text"><i class="far fa-clock"></i> Pon-Pt: 10:00 - 20:00</p>
                    <p class="footer-text"><i class="far fa-clock"></i> Sb-Nd: 11:00 - 18:00</p>
                    <p class="footer-text">Zapraszamy również po godzinach po wcześniejszym umówieniu.</p>
                </div>
            </div>
            

        </footer>
    </div>
    
    <!-- Prawy banner -->
    <div class="side-banner right"></div>
    
    
    <!-- Modal do powiększania zdjęć -->
    <div id="myModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="img01">
    </div>
    
    <script src="galeria.js"></script>
</body>
</html>