<?php
session_start();
require_once 'config.php';

// Obsługa dodawania opinii
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_opinion'])) {
    $ocena = intval($_POST['ocena']);
    $tresc = trim($_POST['tresc']);
    
    if (isset($_SESSION['uzytkownik_id'])) {
        $uzytkownik_id = $_SESSION['uzytkownik_id'];

        // Dodajemy opinię bez sprawdzania rezerwacji
        $sql = "INSERT INTO opinie (uzytkownik_id, rezerwacja_id, ocena, tresc, zatwierdzona) 
                VALUES (?, NULL, ?, ?, 1)"; // zatwierdzona=1 aby od razu pokazać
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $uzytkownik_id, $ocena, $tresc);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Dziękujemy za Twoją opinię!";
        } else {
            $_SESSION['error'] = "Błąd podczas dodawania opinii: " . $conn->error;
        }
    } else {
        $_SESSION['error'] = "Musisz być zalogowany, aby dodać opinię.";
    }
    
    header("Location: opinie.php");
    exit();
}

// Pobieramy zatwierdzone opinie
$sql = "SELECT o.*, u.imie, u.nazwisko FROM opinie o
        JOIN uzytkownicy u ON o.uzytkownik_id = u.uzytkownik_id
        WHERE o.zatwierdzona = 1 ORDER BY o.data_dodania DESC";
$result_opinie = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Opinie - Bursztynowy Pałac</title>
    <link rel="stylesheet" href="opinie-style.css">
    <script src="script-opinie.js" defer></script>
</head>
<body>
    <div class="top-border"></div>
    
    <div class="main-container">
        <header>
            <div class="img_header">
                <a href="index.php"><img class="logo_header" src="logo.png" alt="Logo"></a>
                <img class="logo_txt_header" src="logo_txt.png" alt="Nazwa firmy">
            </div>
            <div class="link_header">
                <a href="sale.php">Sale</a>
                <a href="galeria.php">Galeria</a>
                <a href="kontakt.php">Kontakt</a>
                <a href="opinie.php">Opinie</a>
                
                <?php if (isset($_SESSION['logged_in'])) : ?>
                    <span class="welcome-msg">Witaj, <?= htmlspecialchars($_SESSION['imie']) ?>!</span>
                    <a href="<?= 
                        match($_SESSION['role']) {
                            'admin' => 'panel_admina.php',
                            'manager' => 'panel_managera.php',
                            'klient' => 'panel_klienta.php',
                            'kelner' => 'panel_kelnera.php',
                            'sprzataczka' => 'panel_sprzataczki.php',
                            'kucharz' => 'panel_kucharza.php',
                            default => 'panel_klienta.php'
                        } 
                    ?>">Mój Panel</a>
                    <a href="logout.php">Wyloguj</a>
                <?php else : ?>
                    <a href="login.php">Zaloguj się</a>
                    <a href="register.php">Rejestracja</a>
                <?php endif; ?>
            </div>
        </header>

        <?php if (isset($_SESSION['message'])) : ?>
            <div class="alert success"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])) : ?>
            <div class="alert error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <h1>Opinie naszych klientów</h1>
        
        <?php if (isset($_SESSION['logged_in'])) : ?>
        <div class="dodaj-opinie">
            <h2>Dodaj swoją opinię</h2>
            <form id="form-opinia" method="POST" action="opinie.php">
                <div class="gwiazdki-ocena" id="gwiazdki-ocena">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <span data-value="<?= $i ?>">☆</span>
                    <?php endfor; ?>
                </div>
                <input type="hidden" id="ocena" name="ocena" value="0" required>
                <textarea id="tresc" name="tresc" placeholder="Twoja opinia..." required minlength="10" maxlength="500"></textarea>
                <button type="submit" name="submit_opinion" id="submit-opinion">
                    <span class="btn-text">Dodaj opinię</span>
                    <span class="btn-loading" style="display:none;">
                        <i class="fas fa-spinner fa-spin"></i> Wysyłanie...
                    </span>
                </button>
            </form>
        </div>
        <?php else : ?>
            <div class="info-box">
                <a href="login.php">Zaloguj się</a>, aby dodać opinię.
            </div>
        <?php endif; ?>

        <div class="opinie-container">
            <?php if ($result_opinie->num_rows > 0) : ?>
                <?php while($opinia = $result_opinie->fetch_assoc()) : ?>
                    <div class="opinia">
                        <div class="opinia-header">
                            <span class="autor"><?= htmlspecialchars($opinia['imie'] . ' ' . $opinia['nazwisko']) ?></span>
                            <span class="data"><?= date('d.m.Y', strtotime($opinia['data_dodania'])) ?></span>
                        </div>
                        <div class="gwiazdki">
                            <?= str_repeat('★', $opinia['ocena']) . str_repeat('☆', 5 - $opinia['ocena']) ?>
                        </div>
                        <p><?= nl2br(htmlspecialchars($opinia['tresc'])) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p class="no-reviews">Brak opinii do wyświetlenia.</p>
            <?php endif; ?>
        </div>

        <footer class="page-footer">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Bursztynowy Pałac</h3>
                    <p>Twoje wymarzone wesele w wyjątkowej scenerii.</p>
                </div>
                <div class="footer-section">
                    <h3>Kontakt</h3>
                    <ul>
                        <li><i class="fas fa-phone"></i> +48 123 456 789</li>
                        <li><i class="fas fa-envelope"></i> kontakt@example.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> ul. Weselna 1, 00-001 Warszawa</li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Godziny otwarcia</h3>
                    <p>Pon-Pt: 10:00-20:00</p>
                    <p>Sb-Nd: 11:00-18:00</p>
                </div>
            </div>
        </footer>
    </div>
</body>
s
</html>