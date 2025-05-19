<?php
session_start();

// Połączenie z bazą danych
$host = '127.0.0.1:3307';
$dbname = 'dom_weselny';
$username = 'root'; // Zmień na swoje dane
$password = ''; // Zmień na swoje dane

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}

// Obsługa logowania
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM uzytkownicy WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['haslo'])) {
        $_SESSION['user_id'] = $user['uzytkownik_id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['rola'];
        $_SESSION['logged_in'] = true;
        
        header("Location: index.html");
        exit();
    } else {
        $login_error = "Nieprawidłowy email lub hasło";
    }
}

// Obsługa rejestracji
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $imie = $_POST['imie'];
    $nazwisko = $_POST['nazwisko'];
    $telefon = $_POST['telefon'] ?? '';
    $adres = $_POST['adres'] ?? '';
    
    try {
        $stmt = $pdo->prepare("INSERT INTO uzytkownicy (email, haslo, imie, nazwisko, telefon, adres, rola) VALUES (?, ?, ?, ?, ?, ?, 'klient')");
        $stmt->execute([$email, $password, $imie, $nazwisko, $telefon, $adres]);
        
        // Automatyczne logowanie po rejestracji
        $user_id = $pdo->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = 'klient';
        $_SESSION['logged_in'] = true;
        
        header("Location: index.html");
        exit();
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $registration_error = "Użytkownik o podanym emailu już istnieje";
        } else {
            $registration_error = "Błąd podczas rejestracji: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bursztynowy Pałac - Logowanie</title>
    <style>
        :root {
            --amber-light: #FFE7B3;
            --amber: #FFC107;
            --amber-dark: #FFA000;
            --amber-darker: #FF8F00;
            --text-dark: #333;
            --text-light: #555;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: var(--text-dark);
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color:rgb(255, 255, 255);
        }
        
        .logo_header {
            width: 125px;
            height: 125px; 
        }
        
        .logo_txt_header {
            width: 125px;
            height: 125px;
            margin-top: 20px;
        }
        
        header {
            height: 150px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            border-bottom: 2px solid var(--amber-dark);
            color: var(--text-dark);
            word-spacing: 10px;
            background-color: white;
            margin-bottom: 40px;
        }
        
        .img_header {
            float: left;
            padding-left: 20px;
        }
        
        .link_header {
            padding-right: 100px;
            margin-top: 62px;
            float: right;
        }
        
        .header-link {
            padding-right: 15px;
            text-decoration: none;
            color: var(--text-dark);
            transition: color 0.3s;
        }
        
        .header-link:hover {
            color: var(--amber-darker);
        }
        
        .auth-container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(255, 160, 0, 0.1);
            border: 1px solid var(--amber-light);
        }
        
        .auth-tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--amber-light);
        }
        
        .auth-tab {
            padding: 15px 30px;
            cursor: pointer;
            font-weight: 600;
            text-align: center;
            flex: 1;
            transition: all 0.3s;
            font-family: 'Montserrat', sans-serif;
        }
        
        .auth-tab.active {
            color: var(--amber-darker);
            border-bottom: 3px solid var(--amber-darker);
        }
        
        .auth-form {
            display: none;
        }
        
        .auth-form.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-light);
        }
        
        .form-control {
            width: 100%;
            padding: 15px;
            border: 1px solid var(--amber-light);
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--amber-dark);
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.3);
        }
        
        .submit-btn {
            background: var(--amber);
            color: var(--text-dark);
            border: none;
            padding: 16px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            font-family: 'Montserrat', sans-serif;
        }
        
        .submit-btn:hover {
            background: var(--amber-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 160, 0, 0.3);
        }
        
        .error-message {
            color: #dc3545;
            margin-top: 10px;
            text-align: center;
        }
        
        .success-message {
            color: #28a745;
            margin-top: 10px;
            text-align: center;
        }
        
        .page-footer {
            color: var(--text-dark);
            padding: 40px 0 20px;
            font-family: 'Montserrat', sans-serif;
            border-top: 2px solid var(--amber);
            background-color: white;
            margin-top: 40px;
        }
        
        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .footer-section {
            flex: 1;
            min-width: 200px;
            margin-bottom: 20px;
            padding: 0 15px;
        }
        
        .footer-heading {
            font-size: 18px;
            margin-bottom: 15px;
            color: var(--amber-darker);
            font-weight: 600;
        }
        
        .footer-text {
            font-size: 14px;
            line-height: 1.6;
            margin: 5px 0;
        }
        
        .footer-links {
            list-style: none;
            padding: 0;
        }
        
        .footer-link {
            color: var(--text-light);
            text-decoration: none;
            font-size: 14px;
            line-height: 1.8;
            transition: color 0.3s;
        }
        
        .footer-link:hover {
            color: var(--amber-darker);
        }
    </style>
</head>
<body>
    <header>
        <div class="img_header">
            <a href="index.html"><img class="logo_header" src="logo.png" alt="dom_weselny"></a>
            <img class="logo_txt_header" src="logo_txt.png" alt="dom_weselny_txt">
        </div>
        <div class="link_header">
            <a href="sale.html" class="header-link">Sale</a>
            <a href="galeria.html" class="header-link">Galeria</a>
            <a href="kontakt.html" class="header-link">Kontakt</a>
            <a href="opinie.html" class="header-link">Opinie</a>
        </div>
    </header>

    <main>
        <div class="auth-container">
            <div class="auth-tabs">
                <div class="auth-tab active" onclick="switchTab('login')">Logowanie</div>
                <div class="auth-tab" onclick="switchTab('register')">Rejestracja</div>
            </div>
            
            <form id="loginForm" class="auth-form active" method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">Adres e-mail</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Hasło</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="submit-btn">Zaloguj się</button>
                <?php if (isset($login_error)): ?>
                    <div class="error-message"><?php echo $login_error; ?></div>
                <?php endif; ?>
            </form>
            
            <form id="registerForm" class="auth-form" method="POST" action="login.php">
                <div class="form-group">
                    <label for="reg_email">Adres e-mail</label>
                    <input type="email" id="reg_email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="reg_password">Hasło</label>
                    <input type="password" id="reg_password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="imie">Imię</label>
                    <input type="text" id="imie" name="imie" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="nazwisko">Nazwisko</label>
                    <input type="text" id="nazwisko" name="nazwisko" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="telefon">Numer telefonu</label>
                    <input type="tel" id="telefon" name="telefon" class="form-control">
                </div>
                <div class="form-group">
                    <label for="adres">Adres</label>
                    <input type="text" id="adres" name="adres" class="form-control">
                </div>
                <button type="submit" name="register" class="submit-btn">Zarejestruj się</button>
                <?php if (isset($registration_error)): ?>
                    <div class="error-message"><?php echo $registration_error; ?></div>
                <?php endif; ?>
                <?php if (isset($registration_success)): ?>
                    <div class="success-message"><?php echo $registration_success; ?></div>
                <?php endif; ?>
            </form>
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
                    <li><a href="tel:+48786020787" class="footer-link">+48 786 020 787</a></li>
                    <li><a href="mailto:kontakt@bursztynowypalac.pl" class="footer-link">kontakt@bursztynowypalac.pl</a></li>
                    <li><a href="#" class="footer-link">ul. Weselna 123, 00-001 Warszawa</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3 class="footer-heading">Godziny otwarcia</h3>
                <p class="footer-text">Pon-Pt: 10:00 - 20:00</p>
                <p class="footer-text">Sb-Nd: 11:00 - 18:00</p>
                <p class="footer-text">Zapraszamy również po godzinach po wcześniejszym umówieniu.</p>
            </div>
        </div>
    </footer>

    <script>
        function switchTab(tabName) {
            // Zmiana aktywnych zakładek
            document.querySelectorAll('.auth-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelector(`.auth-tab[onclick="switchTab('${tabName}')"]`).classList.add('active');
            
            // Zmiana aktywnych formularzy
            document.querySelectorAll('.auth-form').forEach(form => {
                form.classList.remove('active');
            });
            document.getElementById(`${tabName}Form`).classList.add('active');
        }
        
        // Jeśli wystąpił błąd rejestracji, automatycznie przełącz na zakładkę rejestracji
        <?php if (isset($registration_error) || isset($registration_success)): ?>
            document.addEventListener('DOMContentLoaded', function() {
                switchTab('register');
            });
        <?php endif; ?>
    </script>
</body>
</html>