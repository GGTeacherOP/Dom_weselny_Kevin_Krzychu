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
    <link rel="stylesheet" href="sale.css">
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
                            $panel_link = 'panel_admina.php';
                            break;
                        case 'manager':
                            $panel_link = 'panel_managera.php';
                            break;
                        case 'klient':
                            $panel_link = 'panel_klienta.php';
                            break;
                        case 'kelner':
                            $panel_link = 'panel_kelnera.php';
                            break;
                        case 'sprzataczka':
                            $panel_link = 'panel_sprzataczki.php';
                            break;
                        case 'kucharz':
                            $panel_link = 'panel_kucharza.php';
                            break;
                        default:
                            $panel_link = 'panel_klienta.php';
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

       <div class="sale-container">
        <div class="sale-header">
            <h1>Nasze Sale</h1>
            <p>Odkryj nasze przestronne i nowocześnie wyposażone sale, idealne na każdą okazję. Każda przestrzeń została zaprojektowana z myślą o komforcie i funkcjonalności.</p>
        </div>
        
        <div class="sale-grid">
            <!-- Sala 1 -->
            <div class="sale-card">
                <img src="s1.jpeg" alt="Sala Konferencyjna" class="sale-image">
                <div class="sale-content">
                    <h3 class="sale-title">Sala Konferencyjna</h3>
                    <p class="sale-description">Idealna na spotkania biznesowe i szkolenia. Wyposażona w nowoczesny sprzęt multimedialny.</p>
                    <a href="#" class="sale-btn">Zobacz więcej</a>
                </div>
            </div>
            
            <!-- Sala 2 -->
            <div class="sale-card">
                <img src="s2.jpg" alt="Sala Bankietowa" class="sale-image">
                <div class="sale-content">
                    <h3 class="sale-title">Sala Bankietowa</h3>
                    <p class="sale-description">Przestronna sala doskonała na wesela, przyjęcia i inne uroczystości. Może pomieścić do 150 osób.</p>
                    <a href="#" class="sale-btn">Zobacz więcej</a>
                </div>
            </div>
            
            <!-- Sala 3 -->
            <div class="sale-card">
                <img src="s3.jpg" alt="Sala Szkoleniowa" class="sale-image">
                <div class="sale-content">
                    <h3 class="sale-title">Sala Szkoleniowa</h3>
                    <p class="sale-description">Komfortowa przestrzeń do prowadzenia warsztatów i kursów. Wyposażona w wygodne fotele i flipcharty.</p>
                    <a href="#" class="sale-btn">Zobacz więcej</a>
                </div>
            </div>
            
            <!-- Sala 4 -->
            <div class="sale-card">
                <img src="s4.jpg" alt="Sala Kameralna" class="sale-image">
                <div class="sale-content">
                    <h3 class="sale-title">Sala Kameralna</h3>
                    <p class="sale-description">Mniejsza sala idealna na spotkania w mniejszym gronie lub kameralne przyjęcia (do 30 osób).</p>
                    <a href="#" class="sale-btn">Zobacz więcej</a>
                </div>
            </div>
            
            <!-- Sala 5 -->
            <div class="sale-card">
                <img src="s5.jpg" alt="Sala Wielofunkcyjna" class="sale-image">
                <div class="sale-content">
                    <h3 class="sale-title">Sala Wielofunkcyjna</h3>
                    <p class="sale-description">Uniwersalna przestrzeń, którą można dostosować do różnych potrzeb - od spotkań po małe eventy.</p>
                    <a href="#" class="sale-btn">Zobacz więcej</a>
                </div>
            </div>
            
            <!-- Sala 6 -->
            <div class="sale-card">
                <img src="s6.jpg" alt="Sala Prestiżowa" class="sale-image">
                <div class="sale-content">
                    <h3 class="sale-title">Sala Prestiżowa</h3>
                    <p class="sale-description">Nasza najbardziej ekskluzywna przestrzeń z eleganckim wystrojem, idealna na ważne wydarzenia.</p>
                    <a href="#" class="sale-btn">Zobacz więcej</a>
                </div>
            </div>
        </div>
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
</body>
<script src="index.js"></script>
</html>