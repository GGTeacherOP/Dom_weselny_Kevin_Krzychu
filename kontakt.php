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
    <link rel="stylesheet" href="kontakt.css">
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

    <main>
        <div class="contact-section">
            <div class="contact-form-container">
                <div class="contact-form">
                    <h2>Skontaktuj się z nami</h2>
                    <form id="contactForm">
                        <div class="form-group">
                            <label for="name">Imię i nazwisko</label>
                            <input type="text" id="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Adres e-mail</label>
                            <input type="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Numer telefonu</label>
                            <input type="tel" id="phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="subject">Temat</label>
                            <select id="subject" class="form-control" required>
                                <option value="" disabled selected>Wybierz temat</option>
                                <option value="rezerwacja">Rezerwacja</option>
                                <option value="pytanie">Pytanie ogólne</option>
                                <option value="wspolpraca">Współpraca</option>
                                <option value="reklamacja">Reklamacja</option>
                                <option value="inne">Inne</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message">Wiadomość</label>
                            <textarea id="message" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">Wyślij wiadomość</button>
                    </form>
                </div>
            </div>

            <div class="faq-container">
                <h2>Często zadawane pytania</h2>
                
                <div class="faq-item">
                    <div class="faq-question">Jak mogę zarezerwować pobyt w obiekcie?</div>
                    <div class="faq-answer">
                        <p>Rezerwacji możesz dokonać poprzez nasz formularz kontaktowy, telefonicznie lub bezpośrednio w recepcji obiektu. W formularzu wybierz temat "Rezerwacja" i podaj preferowane terminy.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">Jakie są godziny zameldowania i wymeldowania?</div>
                    <div class="faq-answer">
                        <p>Standardowe godziny zameldowania to 14:00, a wymeldowania do 12:00. Istnieje możliwość późniejszego wymeldowania za dodatkową opłatą, jeśli nie ma kolejnej rezerwacji.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">Czy obiekt jest przystosowany dla osób niepełnosprawnych?</div>
                    <div class="faq-answer">
                        <p>Tak, nasz obiekt posiada pokoje przystosowane dla osób z niepełnosprawnością ruchową, windy oraz podjazdy. Prosimy o informację przy rezerwacji o szczególnych potrzebach.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">Jakie metody płatności są akceptowane?</div>
                    <div class="faq-answer">
                        <p>Akceptujemy płatności gotówką, kartami płatniczymi (Visa, MasterCard) oraz przelewami bankowymi. Płatność online możliwa jest poprzez nasz system rezerwacyjny.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">Czy w obiekcie można organizować imprezy okolicznościowe?</div>
                    <div class="faq-answer">
                        <p>Tak, dysponujemy salą bankietową i oferujemy pełną organizację imprez. Prosimy o kontakt w celu omówienia szczegółów i dostępności terminów.</p>
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">Jaka jest polityka anulowania rezerwacji?</div>
                    <div class="faq-answer">
                        <p>Bezpłatne anulowanie możliwe jest do 48 godzin przed planowanym przyjazdem. Późniejsze anulowanie może wiązać się z opłatą w wysokości pierwszej nocy pobytu.</p>
                    </div>
                </div>
            </div>
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
<script src="kontakt.js"></script>
</html>