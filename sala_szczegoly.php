<?php
session_start();
require_once 'config1.php';

$sala_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sala = [];
if ($sala_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM sale WHERE sala_id = ?");
    $stmt->execute([$sala_id]);
    $sala = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$sala) {
    header("Location: sale.php");
    exit();
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_rezerwacja'])) {
    $data_wydarzenia = trim($_POST['data_wydarzenia']);
    $godzina_rozpoczecia = trim($_POST['godzina_rozpoczecia']);
    $godzina_zakonczenia = trim($_POST['godzina_zakonczenia']);
    $liczba_gosci = intval($_POST['liczba_gosci']);
    $dodatkowe_informacje = trim($_POST['dodatkowe_informacje']);
    
    if (empty($data_wydarzenia)) $errors[] = "Data wydarzenia jest wymagana";
    if (empty($godzina_rozpoczecia)) $errors[] = "Godzina rozpoczęcia jest wymagana";
    if (empty($godzina_zakonczenia)) $errors[] = "Godzina zakończenia jest wymagana";
    if ($liczba_gosci < 1) $errors[] = "Liczba gości musi być większa od 0";
    if ($liczba_gosci > $sala['pojemnosc']) $errors[] = "Liczba gości przekracza pojemność sali";
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM rezerwacje WHERE sala_id = ? AND data_wydarzenia = ? AND (
            (? BETWEEN godzina_rozpoczecia AND godzina_zakonczenia) OR 
            (? BETWEEN godzina_rozpoczecia AND godzina_zakonczenia) OR
            (godzina_rozpoczecia BETWEEN ? AND ?) OR
            (godzina_zakonczenia BETWEEN ? AND ?)
        ) AND status != 'anulowana'");
        
        $stmt->execute([
            $sala_id,
            $data_wydarzenia,
            $godzina_rozpoczecia,
            $godzina_zakonczenia,
            $godzina_rozpoczecia,
            $godzina_zakonczenia,
            $godzina_rozpoczecia,
            $godzina_zakonczenia
        ]);
        
        if ($stmt->rowCount() > 0) {
            $errors[] = "Sala jest już zarezerwowana w podanym terminie";
        }
    }
    
    if (empty($errors)) {
        $uzytkownik_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        $status = 'oczekujaca';
        
        $stmt = $pdo->prepare("INSERT INTO rezerwacje (uzytkownik_id, sala_id, data_rezerwacji, data_wydarzenia, godzina_rozpoczecia, godzina_zakonczenia, liczba_gosci, status, dodatkowe_informacje) 
                              VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$uzytkownik_id, $sala_id, $data_wydarzenia, $godzina_rozpoczecia, $godzina_zakonczenia, $liczba_gosci, $status, $dodatkowe_informacje])) {
            $success = true;
            
            // Dodajemy JavaScript do wyświetlenia alertu
            echo '<script>
                    window.onload = function() {
                        alert("Rezerwacja została złożona pomyślnie! Wkrótce skontaktujemy się w celu potwierdzenia.");
                    }
                  </script>';
        } else {
            $errors[] = "Wystąpił błąd podczas przetwarzania rezerwacji";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($sala['nazwa_sali']) ?> - Bursztynowy Pałac</title>
    <link rel="stylesheet" href="sale.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .sala-szczegoly {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .sala-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .sala-header h1 {
            color: var(--amber-darker);
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .sala-content {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
            margin-bottom: 50px;
        }
        
        .sala-image-container {
            flex: 1;
            min-width: 300px;
        }
        
        .sala-image {
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .sala-info {
            flex: 1;
            min-width: 300px;
        }
        
        .sala-info-item {
            margin-bottom: 15px;
        }
        
        .sala-info-label {
            font-weight: bold;
            color: var(--amber-dark);
        }
        
        .rezerwacja-form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-top: 40px;
        }
        
        .form-title {
            color: var(--amber-darker);
            margin-bottom: 20px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }
        
        .btn-rezerwuj {
            background: var(--amber);
            color: var(--text-dark);
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-rezerwuj:hover {
            background: var(--amber-dark);
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .login-info {
            text-align: center;
            margin: 20px 0;
            padding: 15px;
            background: #fff3cd;
            border-radius: 6px;
        }
    </style>
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
                    <span style="margin-right: 15px; color: var(--amber-darker);">Witaj, <?= htmlspecialchars($_SESSION['imie']) ?>!</span>
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
                    <a href="<?= $panel_link ?>" class="header-link">Mój Panel</a>
                    <a href="logout.php" class="header-link">Wyloguj</a>
                <?php else: ?>
                    <a href="login.php" class="header-link">Zaloguj się</a>
                    <a href="register.php" class="header-link">Rejestracja</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="sala-szczegoly">
            <div class="sala-header">
                <h1><?= htmlspecialchars($sala['nazwa_sali']) ?></h1>
                <p>Zarezerwuj tę salę na swoją wyjątkową okazję</p>
            </div>
            
            <div class="sala-content">
                <div class="sala-image-container">
                    <img src="<?= htmlspecialchars($sala['zdjecie'] ?? 'default_sala.jpg') ?>" alt="<?= htmlspecialchars($sala['nazwa_sali']) ?>" class="sala-image">
                </div>
                
                <div class="sala-info">
                    <div class="sala-info-item">
                        <span class="sala-info-label">Pojemność:</span>
                        <span><?= htmlspecialchars($sala['pojemnosc']) ?> osób</span>
                    </div>
                    
                    <div class="sala-info-item">
                        <span class="sala-info-label">Powierzchnia:</span>
                        <span><?= htmlspecialchars($sala['powierzchnia']) ?> m²</span>
                    </div>
                    
                    <div class="sala-info-item">
                        <span class="sala-info-label">Cena podstawowa:</span>
                        <span><?= number_format($sala['cena_podstawowa'], 2, ',', ' ') ?> PLN</span>
                    </div>
                    
                    <div class="sala-info-item">
                        <span class="sala-info-label">Dostępność w weekendy:</span>
                        <span><?= $sala['dostepnosc_weekend'] ? 'Tak' : 'Nie' ?></span>
                    </div>
                    
                    <div class="sala-info-item">
                        <span class="sala-info-label">Wyposażenie:</span>
                        <span><?= htmlspecialchars($sala['wyposazenie']) ?></span>
                    </div>
                    
                    <div class="sala-info-item">
                        <span class="sala-info-label">Opis:</span>
                        <p><?= htmlspecialchars($sala['opis']) ?></p>
                    </div>
                </div>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Rezerwacja została złożona pomyślnie! Wkrótce skontaktujemy się w celu potwierdzenia.
                </div>
            <?php elseif (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="rezerwacja-form">
                <h2 class="form-title">Formularz rezerwacji</h2>
                
                <?php if (!isset($_SESSION['logged_in'])): ?>
                    <div class="login-info">
                        <p>Aby zarezerwować salę, musisz być zalogowany. <a href="login.php">Zaloguj się</a> lub <a href="register.php">zarejestruj</a> jeśli nie masz jeszcze konta.</p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="sala_szczegoly.php?id=<?= $sala_id ?>">
                    <div class="form-group">
                        <label for="data_wydarzenia" class="form-label">Data wydarzenia*</label>
                        <input type="date" id="data_wydarzenia" name="data_wydarzenia" class="form-control" 
                               value="<?= isset($_POST['data_wydarzenia']) ? htmlspecialchars($_POST['data_wydarzenia']) : '' ?>" 
                               min="<?= date('Y-m-d') ?>" required <?= !isset($_SESSION['logged_in']) ? 'disabled' : '' ?>>
                    </div>
                    
                    <div class="form-group">
                        <label for="godzina_rozpoczecia" class="form-label">Godzina rozpoczęcia*</label>
                        <input type="time" id="godzina_rozpoczecia" name="godzina_rozpoczecia" class="form-control" 
                               value="<?= isset($_POST['godzina_rozpoczecia']) ? htmlspecialchars($_POST['godzina_rozpoczecia']) : '16:00' ?>" 
                               required <?= !isset($_SESSION['logged_in']) ? 'disabled' : '' ?>>
                    </div>
                    
                    <div class="form-group">
                        <label for="godzina_zakonczenia" class="form-label">Godzina zakończenia*</label>
                        <input type="time" id="godzina_zakonczenia" name="godzina_zakonczenia" class="form-control" 
                               value="<?= isset($_POST['godzina_zakonczenia']) ? htmlspecialchars($_POST['godzina_zakonczenia']) : '02:00' ?>" 
                               required <?= !isset($_SESSION['logged_in']) ? 'disabled' : '' ?>>
                    </div>
                    
                    <div class="form-group">
                        <label for="liczba_gosci" class="form-label">Liczba gości*</label>
                        <input type="number" id="liczba_gosci" name="liczba_gosci" class="form-control" 
                               value="<?= isset($_POST['liczba_gosci']) ? htmlspecialchars($_POST['liczba_gosci']) : '' ?>" 
                               min="1" max="<?= $sala['pojemnosc'] ?>" required <?= !isset($_SESSION['logged_in']) ? 'disabled' : '' ?>>
                        <small>Maksymalna liczba gości: <?= $sala['pojemnosc'] ?></small>
                    </div>
                    
                    <div class="form-group">
                        <label for="dodatkowe_informacje" class="form-label">Dodatkowe informacje</label>
                        <textarea id="dodatkowe_informacje" name="dodatkowe_informacje" class="form-control" 
                                  rows="4" <?= !isset($_SESSION['logged_in']) ? 'disabled' : '' ?>><?= isset($_POST['dodatkowe_informacje']) ? htmlspecialchars($_POST['dodatkowe_informacje']) : '' ?></textarea>
                    </div>
                    
                    <?php if (isset($_SESSION['logged_in'])): ?>
                        <button type="submit" name="submit_rezerwacja" class="btn-rezerwuj">Zarezerwuj</button>
                    <?php else: ?>
                        <button type="button" class="btn-rezerwuj" disabled>Zaloguj się, aby zarezerwować</button>
                    <?php endif; ?>
                </form>
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
    </div>
    
    <script>
        // Skrypt do ustawienia minimalnej daty na dzisiaj
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.getElementById('data_wydarzenia');
            
            if (dateInput && !dateInput.min) {
                dateInput.min = today;
            }
            
            if (dateInput.value && dateInput.value < today) {
                dateInput.value = today;
            }
        });
    </script>
</body>
</html>
</body>
</html>