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
    <link rel="stylesheet" href="opinie-style.css">
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

    <h1>Opinie naszych klientów</h1>
    
    <div class="dodaj-opinie">
        <h2>Dodaj swoją opinię</h2>
        <form id="form-opinia">
            <input type="text" id="autor" placeholder="Twoje imię" required>
            <div class="gwiazdki-ocena" id="gwiazdki-ocena">
                <span data-value="1">☆</span>
                <span data-value="2">☆</span>
                <span data-value="3">☆</span>
                <span data-value="4">☆</span>
                <span data-value="5">☆</span>
            </div>
            <input type="hidden" id="ocena" value="0">
            <textarea id="tresc" placeholder="Twoja opinia..." required></textarea>
            <button type="submit">Dodaj opinię</button>
        </form>
    </div>

    <br><br>

    <div class="opinie-container" id="opinie-container">
        <!-- Przykładowe opinie -->
        <div class="opinia">
            <div class="opinia-header">
                <span class="autor">{Z BAZY DANYCH}</span>
                <span class="data">{Z BAZY DANYCH}</span>
            </div>
            <div class="gwiazdki">★★★★★ {Z BAZY DANYCH}</div>
            <p>{Z BAZY DANYCH}</p>
        </div>
        <div class="opinia">
            <div class="opinia-header">
                <span class="autor">{Z BAZY DANYCH}</span>
                <span class="data">{Z BAZY DANYCH}</span>
            </div>
            <div class="gwiazdki">★★★★★ {Z BAZY DANYCH}</div>
            <p>{Z BAZY DANYCH}</p>
        </div>
        <div class="opinia">
            <div class="opinia-header">
                <span class="autor">{Z BAZY DANYCH}</span>
                <span class="data">{Z BAZY DANYCH}</span>
            </div>
            <div class="gwiazdki">★★★★★ {Z BAZY DANYCH}</div>
            <p>{Z BAZY DANYCH}</p>
        </div>
        <div class="opinia">
            <div class="opinia-header">
                <span class="autor">{Z BAZY DANYCH}</span>
                <span class="data">{Z BAZY DANYCH}</span>
            </div>
            <div class="gwiazdki">★★★★★ {Z BAZY DANYCH}</div>
            <p>{Z BAZY DANYCH}</p>
        </div>
        <div class="opinia">
            <div class="opinia-header">
                <span class="autor">{Z BAZY DANYCH}</span>
                <span class="data">{Z BAZY DANYCH}</span>
            </div>
            <div class="gwiazdki">★★★★★ {Z BAZY DANYCH}</div>
            <p>{Z BAZY DANYCH}</p>
        </div>
        <div class="opinia">
            <div class="opinia-header">
                <span class="autor">{Z BAZY DANYCH}</span>
                <span class="data">{Z BAZY DANYCH}</span>
            </div>
            <div class="gwiazdki">★★★★★ {Z BAZY DANYCH}</div>
            <p>{Z BAZY DANYCH}</p>
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
<script src="script-opinie.js"></script>
</html>