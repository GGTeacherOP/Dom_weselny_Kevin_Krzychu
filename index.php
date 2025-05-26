<?php
session_start();
require_once 'index_config.php';

// Funkcja do bezpiecznego pobierania danych
function fetchData($conn, $query, $params = []) {
    $stmt = $conn->prepare($query);
    if ($params) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Pobieranie danych o salach
$sale = fetchData($conn, "SELECT * FROM sale LIMIT 3");

// Pobieranie opinii z danymi użytkowników
$opinie = fetchData($conn, "SELECT o.*, u.imie, u.nazwisko 
                          FROM opinie o 
                          JOIN uzytkownicy u ON o.uzytkownik_id = u.uzytkownik_id 
                          WHERE o.zatwierdzona = 1 
                          LIMIT 3");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bursztynowy Pałac - Dom Weselny</title>
    <meta name="description" content="Bursztynowy Pałac - wyjątkowe miejsce na Twoje wymarzone wesele. Odkryj nasze sale, galerię i opinie zadowolonych klientów.">
    <link rel="stylesheet" href="index.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="top-border"></div>
    
    <div class="main-container">
        <header>
            <div class="img_header">
                <a href="index.php"><img class="logo_header" src="logo.png" alt="Dom Weselny"></a>
                <img class="logo_txt_header" src="logo_txt.png" alt="Bursztynowy Pałac">
            </div>
            <div class="link_header">
                <a href="sale.php">Sale</a>
                <a href="galeria.php">Galeria</a>
                <a href="kontakt.php">Kontakt</a>
                <a href="opinie.php">Opinie</a>
                
                <?php if (isset($_SESSION['logged_in'])) : ?>
                    <span style="margin-right: 15px; color: var(--amber-darker);">
                        Witaj, <?php echo htmlspecialchars($_SESSION['imie'] ?? ''); ?>!
                    </span>
                    <?php 
                    $panel_link = 'panel_klienta.php';
                    if (isset($_SESSION['role'])) {
                        switch($_SESSION['role']) {
                            case 'admin': $panel_link = 'panel.php'; break;
                            case 'manager': $panel_link = 'panel_managera.php'; break;
                            case 'kelner': $panel_link = 'panel_kelnera.php'; break;
                            case 'sprzataczka': $panel_link = 'panel_sprzataczki.php'; break;
                            case 'kucharz': $panel_link = 'panel_kucharza.php'; break;
                        }
                    }
                    ?>
                    <a href="<?php echo $panel_link; ?>" class="header-link">Mój Panel</a>
                    <a href="logout.php" class="header-link">Wyloguj</a>
                <?php else : ?>
                    <a href="login.php" class="header-link">Zaloguj się</a>
                    <a href="register.php" class="header-link">Rejestracja</a>
                <?php endif; ?>
            </div>
        </header>

        <main class="main-content">
            <section class="hero-section">
                <div class="hero-overlay">
                    <h1>Bursztynowy Pałac</h1>
                    <p>Twój wymarzony dzień w wyjątkowej scenerii</p>
                    <a href="sale.php" class="hero-btn">Poznaj nasze sale</a>
                </div>
            </section>

            <div class="content-container">
                <section class="featured-rooms">
                    <h2>Nasze Sale</h2>
                    <p class="section-subtitle">Odkryj nasze najpopularniejsze przestrzenie</p>
                    
                    <div class="rooms-grid">
                        <?php foreach ($sale as $sala) : ?>
                            <div class="room-card">
                                <img src="<?php echo htmlspecialchars($sala['zdjecie'] ?? 'default.jpg'); ?>" 
                                     alt="<?php echo htmlspecialchars($sala['nazwa_sali']); ?>">
                                <div class="room-info">
                                    <h3><?php echo htmlspecialchars($sala['nazwa_sali']); ?></h3>
                                    <p><?php echo htmlspecialchars($sala['opis']); ?></p>
                                    <a href="sale.php?id=<?php echo $sala['sala_id']; ?>" class="room-btn">Zobacz więcej</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="all-rooms-link">
                        <a href="sale.php" class="see-all-btn">Zobacz wszystkie sale</a>
                    </div>
                </section>

                <section class="contact-section">
                    <div class="contact-container">
                        <div class="contact-info">
                            <h2>Skontaktuj się z nami</h2>
                            <p>Masz pytania? Chcesz zarezerwować termin? Jesteśmy do Twojej dyspozycji!</p>
                            
                            <div class="contact-details">
                                <div class="contact-item">
                                    <i class="fas fa-phone-alt"></i>
                                    <span>+48 786 020 787</span>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>kontakt@bursztynowypalac.pl</span>
                                </div>
                                <div class="contact-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>ul. Weselna 123, 00-001 Warszawa</span>
                                </div>
                            </div>
                            
                            <a href="kontakt.php" class="contact-btn">Formularz kontaktowy</a>
                        </div>
                        
                        <div class="contact-image">
                            <img src="s1.jpeg" alt="Kontakt">
                        </div>
                    </div>
                </section>

                <section class="testimonials-section">
                    <h2>Opinie naszych klientów</h2>
                    <p class="section-subtitle">Poznaj doświadczenia innych par</p>
                    
                    
                    <div class="all-testimonials-link">
                        <a href="opinie.php" class="see-all-btn">Zobacz wszystkie opinie</a>
                    </div>
                </section>
            </div>
        </main>

        <footer class="page-footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h3 class="footer-heading">Bursztynowy Pałac</h3>
                    <p class="footer-text">Twoje wymarzone wesele w wyjątkowej scenerii. Z nami stworzysz niezapomniane chwile.</p>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-heading">Kontakt</h3>
                    <ul class="footer-links">
                        <li><a href="tel:+48786020787"><i class="fas fa-phone-alt"></i> +48 786 020 787</a></li>
                        <li><a href="mailto:kontakt@bursztynowypalac.pl"><i class="fas fa-envelope"></i> kontakt@bursztynowypalac.pl</a></li>
                        <li><a href="https://maps.google.com"><i class="fas fa-map-marker-alt"></i> ul. Weselna 123, Warszawa</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3 class="footer-heading">Godziny otwarcia</h3>
                    <p><i class="far fa-clock"></i> Pon-Pt: 10:00 - 20:00</p>
                    <p><i class="far fa-clock"></i> Sb-Nd: 11:00 - 18:00</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Bursztynowy Pałac. Wszelkie prawa zastrzeżone.</p>
            </div>
        </footer>
    </div>
</body>
</html>