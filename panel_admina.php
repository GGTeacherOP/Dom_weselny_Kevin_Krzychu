<?php
session_start();

// Sprawdzenie czy użytkownik jest zalogowany i ma uprawnienia admina
if (!isset($_SESSION['logged_in']) {
    header("Location: login.php");
    exit();
}

// Połączenie z bazą danych
$host = '127.0.0.1:3307';
$dbname = 'dom_weselny';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Błąd połączenia z bazą danych: " . $e->getMessage());
}

// Pobieranie danych z różnych tabel
$users = $pdo->query("SELECT * FROM uzytkownicy")->fetchAll(PDO::FETCH_ASSOC);
$reservations = $pdo->query("SELECT * FROM rezerwacje")->fetchAll(PDO::FETCH_ASSOC);
$rooms = $pdo->query("SELECT * FROM sale")->fetchAll(PDO::FETCH_ASSOC);
$finance = $pdo->query("SELECT * FROM finanse")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admina - Bursztynowy Pałac</title>
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
            background-color: #f9f9f9;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--amber);
        }
        
        .admin-title {
            color: var(--amber-darker);
            font-size: 24px;
            font-weight: 700;
        }
        
        .logout-btn {
            background: var(--amber);
            color: var(--text-dark);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: var(--amber-dark);
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .admin-table th {
            background-color: var(--amber);
            color: var(--text-dark);
            padding: 12px;
            text-align: left;
        }
        
        .admin-table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            background-color: white;
        }
        
        .admin-table tr:hover td {
            background-color: var(--amber-light);
        }
        
        .table-container {
            margin-bottom: 50px;
        }
        
        .table-title {
            color: var(--amber-darker);
            font-size: 20px;
            margin-bottom: 15px;
            padding-bottom: 5px;
            border-bottom: 2px solid var(--amber-light);
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <h1 class="admin-title">Panel Administratora</h1>
        <form action="logout.php" method="post">
            <button type="submit" class="logout-btn">Wyloguj się</button>
        </form>
    </div>

    <div class="table-container">
        <h2 class="table-title">Użytkownicy</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Imię</th>
                    <th>Nazwisko</th>
                    <th>Rola</th>
                    <th>Telefon</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['uzytkownik_id']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['imie']) ?></td>
                    <td><?= htmlspecialchars($user['nazwisko']) ?></td>
                    <td><?= htmlspecialchars($user['rola']) ?></td>
                    <td><?= htmlspecialchars($user['telefon'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="table-container">
        <h2 class="table-title">Rezerwacje</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Użytkownika</th>
                    <th>ID Sali</th>
                    <th>Data wydarzenia</th>
                    <th>Godziny</th>
                    <th>Liczba gości</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <td><?= htmlspecialchars($reservation['rezerwacja_id']) ?></td>
                    <td><?= htmlspecialchars($reservation['uzytkownik_id']) ?></td>
                    <td><?= htmlspecialchars($reservation['sala_id']) ?></td>
                    <td><?= htmlspecialchars($reservation['data_wydarzenia']) ?></td>
                    <td><?= htmlspecialchars($reservation['godzina_rozpoczecia']) ?> - <?= htmlspecialchars($reservation['godzina_zakonczenia']) ?></td>
                    <td><?= htmlspecialchars($reservation['liczba_gosci']) ?></td>
                    <td><?= htmlspecialchars($reservation['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="table-container">
        <h2 class="table-title">Sale</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nazwa</th>
                    <th>Pojemność</th>
                    <th>Powierzchnia</th>
                    <th>Cena</th>
                    <th>Dostępność weekend</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rooms as $room): ?>
                <tr>
                    <td><?= htmlspecialchars($room['sala_id']) ?></td>
                    <td><?= htmlspecialchars($room['nazwa_sali']) ?></td>
                    <td><?= htmlspecialchars($room['pojemnosc']) ?></td>
                    <td><?= htmlspecialchars($room['powierzchnia']) ?> m²</td>
                    <td><?= htmlspecialchars($room['cena_podstawowa']) ?> zł</td>
                    <td><?= $room['dostepnosc_weekend'] ? 'Tak' : 'Nie' ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="table-container">
        <h2 class="table-title">Finanse</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID transakcji</th>
                    <th>ID rezerwacji</th>
                    <th>Kwota</th>
                    <th>Data</th>
                    <th>Typ płatności</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($finance as $transaction): ?>
                <tr>
                    <td><?= htmlspecialchars($transaction['transakcja_id']) ?></td>
                    <td><?= htmlspecialchars($transaction['rezerwacja_id']) ?></td>
                    <td><?= htmlspecialchars($transaction['kwota']) ?> zł</td>
                    <td><?= htmlspecialchars($transaction['data_transakcji']) ?></td>
                    <td><?= htmlspecialchars($transaction['typ_platnosci']) ?></td>
                    <td><?= htmlspecialchars($transaction['status_platnosci']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>