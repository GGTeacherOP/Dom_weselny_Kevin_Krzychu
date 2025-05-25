<?php
session_start();

// Sprawdzenie czy użytkownik jest zalogowany
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
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

// Pobranie danych użytkownika
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM uzytkownicy WHERE uzytkownik_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Pobranie odpowiednich danych w zależności od roli
$additional_data = [];
switch ($user['rola']) {
    case 'klient':
        // Pobranie rezerwacji klienta
        $stmt = $pdo->prepare("SELECT r.*, s.nazwa_sali FROM rezerwacje r 
                              JOIN sale s ON r.sala_id = s.sala_id 
                              WHERE r.uzytkownik_id = ? 
                              ORDER BY r.data_wydarzenia DESC");
        $stmt->execute([$user_id]);
        $additional_data['rezerwacje'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;
        
    case 'admin':
        // Statystyki dla admina
        $stats = $pdo->query("
            SELECT 
                (SELECT COUNT(*) FROM uzytkownicy) as total_users,
                (SELECT COUNT(*) FROM rezerwacje) as total_reservations,
                (SELECT SUM(kwota) FROM finanse) as total_income,
                (SELECT COUNT(*) FROM zadania WHERE status != 'zakonczone') as pending_tasks
        ")->fetch(PDO::FETCH_ASSOC);
        $additional_data['stats'] = $stats;
        break;
        
    case 'manager':
        // Nadchodzące wydarzenia i zadania dla managera
        $stmt = $pdo->prepare("SELECT r.*, s.nazwa_sali, u.imie, u.nazwisko 
                              FROM rezerwacje r 
                              JOIN sale s ON r.sala_id = s.sala_id 
                              JOIN uzytkownicy u ON r.uzytkownik_id = u.uzytkownik_id
                              WHERE r.data_wydarzenia >= CURDATE() 
                              ORDER BY r.data_wydarzenia ASC 
                              LIMIT 5");
        $stmt->execute();
        $additional_data['nadchodzace_wydarzenia'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->prepare("SELECT z.*, u.imie, u.nazwisko 
                              FROM zadania z 
                              JOIN uzytkownicy u ON z.przydzielony_uzytkownik_id = u.uzytkownik_id
                              WHERE z.status != 'zakonczone' 
                              ORDER BY z.priorytet DESC, z.data_rozpoczecia ASC");
        $stmt->execute();
        $additional_data['zadania'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;
        
    case 'kelner':
    case 'kucharz':
    case 'sprzataczka':
        // Zadania przypisane do pracownika
        $stmt = $pdo->prepare("SELECT * FROM zadania 
                              WHERE przydzielony_uzytkownik_id = ? 
                              AND status != 'zakonczone' 
                              ORDER BY priorytet DESC, data_rozpoczecia ASC");
        $stmt->execute([$user_id]);
        $additional_data['moje_zadania'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Nadchodzące wydarzenia, w których pracownik jest zaangażowany
        $stmt = $pdo->prepare("SELECT r.*, s.nazwa_sali 
                              FROM rezerwacje r 
                              JOIN sale s ON r.sala_id = s.sala_id 
                              WHERE r.pracownik_id = ? 
                              AND r.data_wydarzenia >= CURDATE() 
                              ORDER BY r.data_wydarzenia ASC");
        $stmt->execute([$user_id]);
        $additional_data['moje_wydarzenia'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        break;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bursztynowy Pałac - Mój Panel</title>
    <style>
        :root {
            --amber-light: #FFE7B3;
            --amber: #FFC107;
            --amber-dark: #FFA000;
            --amber-darker: #FF8F00;
            --text-dark: #333;
            --text-light: #555;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
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
        
        .panel-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .welcome-section {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            flex: 1;
            min-width: 300px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--amber);
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-name {
            font-size: 1.5em;
            margin: 0 0 10px;
            color: var(--amber-darker);
        }
        
        .user-role {
            display: inline-block;
            padding: 5px 10px;
            background-color: var(--amber-light);
            color: var(--amber-darker);
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
        }
        
        .stats-section {
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            flex: 1;
            min-width: 300px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
        }
        
        .stat-card {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            background-color: var(--amber-light);
        }
        
        .stat-value {
            font-size: 1.8em;
            font-weight: bold;
            margin: 10px 0;
            color: var(--amber-darker);
        }
        
        .stat-label {
            font-size: 0.9em;
            color: var(--text-light);
        }
        
        .section-title {
            font-size: 1.3em;
            margin-bottom: 20px;
            color: var(--amber-darker);
            border-bottom: 2px solid var(--amber-light);
            padding-bottom: 10px;
        }
        
        .card {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: var(--amber-light);
            color: var(--amber-darker);
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }
        
        .badge-success {
            background-color: var(--success);
            color: white;
        }
        
        .badge-warning {
            background-color: var(--warning);
            color: var(--text-dark);
        }
        
        .badge-danger {
            background-color: var(--danger);
            color: white;
        }
        
        .badge-info {
            background-color: var(--info);
            color: white;
        }
        
        .badge-primary {
            background-color: var(--amber);
            color: var(--text-dark);
        }
        
        .priority-high {
            color: var(--danger);
            font-weight: bold;
        }
        
        .priority-medium {
            color: var(--warning);
        }
        
        .priority-low {
            color: var(--success);
        }
        
        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background-color: var(--amber);
            color: var(--text-dark);
        }
        
        .btn-primary:hover {
            background-color: var(--amber-dark);
            color: white;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .logout-btn {
            background-color: var(--danger);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 20px;
            display: inline-block;
        }
        
        .logout-btn:hover {
            background-color: #c82333;
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
        
        /* Responsywność */
        @media (max-width: 768px) {
            .panel-container {
                flex-direction: column;
            }
            
            .link_header {
                padding-right: 20px;
            }
            
            .header-link {
                padding-right: 10px;
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="img_header">
            <a href="index.php"><img class="logo_header" src="logo.png" alt="dom_weselny"></a>
            <img class="logo_txt_header" src="logo_txt.png" alt="dom_weselny_txt">
        </div>
        <div class="link_header">
            <a href="sale.php" class="header-link">Sale</a>
            <a href="galeria.php" class="header-link">Galeria</a>
            <a href="kontakt.php" class="header-link">Kontakt</a>
            <a href="opinie.php" class="header-link">Opinie</a>
            <a href="panel.php" class="header-link">Mój Panel</a>
        </div>
    </header>

    <main>
        <div class="panel-container">
            <div class="welcome-section">
                <img src="avatars/<?php echo $user_id; ?>.jpg" alt="Awatar" class="user-avatar" onerror="this.src='avatars/default.jpg'">
                <div class="user-info">
                    <h1 class="user-name"><?php echo htmlspecialchars($user['imie'] . ' ' . htmlspecialchars($user['nazwisko'])) ?></h1>
                    <span class="user-role"><?php echo ucfirst($user['rola']); ?></span>
                    <p>Witaj w swoim panelu! Tutaj możesz zarządzać swoimi danymi i zadaniami.</p>
                </div>
            </div>
            
            <?php if ($user['rola'] === 'admin' || $user['rola'] === 'manager'): ?>
            <div class="stats-section">
                <h2 class="section-title">Statystyki</h2>
                <div class="stats-grid">
                    <?php if ($user['rola'] === 'admin'): ?>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo $additional_data['stats']['total_users']; ?></div>
                            <div class="stat-label">Użytkowników</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo $additional_data['stats']['total_reservations']; ?></div>
                            <div class="stat-label">Rezerwacji</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo number_format($additional_data['stats']['total_income'], 2, ',', ' '); ?> zł</div>
                            <div class="stat-label">Przychód</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo $additional_data['stats']['pending_tasks']; ?></div>
                            <div class="stat-label">Zadań</div>
                        </div>
                    <?php elseif ($user['rola'] === 'manager'): ?>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo count($additional_data['nadchodzace_wydarzenia']); ?></div>
                            <div class="stat-label">Nadchodzące wydarzenia</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-value"><?php echo count($additional_data['zadania']); ?></div>
                            <div class="stat-label">Zadania do wykonania</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sekcje specyficzne dla ról -->
        <?php if ($user['rola'] === 'klient'): ?>
            <div class="card">
                <h2 class="section-title">Moje rezerwacje</h2>
                <?php if (!empty($additional_data['rezerwacje'])): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Data wydarzenia</th>
                                    <th>Sala</th>
                                    <th>Godziny</th>
                                    <th>Liczba gości</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($additional_data['rezerwacje'] as $rezerwacja): ?>
                                    <tr>
                                        <td><?php echo date('d.m.Y', strtotime($rezerwacja['data_wydarzenia'])); ?></td>
                                        <td><?php echo htmlspecialchars($rezerwacja['nazwa_sali']); ?></td>
                                        <td><?php echo substr($rezerwacja['godzina_rozpoczecia'], 0, 5) . ' - ' . substr($rezerwacja['godzina_zakonczenia'], 0, 5); ?></td>
                                        <td><?php echo $rezerwacja['liczba_gosci']; ?></td>
                                        <td>
                                            <?php 
                                                $status_class = '';
                                                if ($rezerwacja['status'] === 'potwierdzona') $status_class = 'badge-success';
                                                elseif ($rezerwacja['status'] === 'anulowana') $status_class = 'badge-danger';
                                                elseif ($rezerwacja['status'] === 'zrealizowana') $status_class = 'badge-info';
                                                else $status_class = 'badge-warning';
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($rezerwacja['status']); ?></span>
                                        </td>
                                        <td>
                                            <button class="action-btn btn-primary">Szczegóły</button>
                                            <?php if ($rezerwacja['status'] === 'oczekujaca' || $rezerwacja['status'] === 'potwierdzona'): ?>
                                                <button class="action-btn btn-secondary">Anuluj</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Nie masz jeszcze żadnych rezerwacji.</p>
                    <a href="sale.php" class="action-btn btn-primary">Zarezerwuj salę</a>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h2 class="section-title">Moje opinie</h2>
                <p>Tutaj możesz dodać opinię o zrealizowanych wydarzeniach.</p>
                <button class="action-btn btn-primary">Dodaj opinię</button>
            </div>
            
        <?php elseif ($user['rola'] === 'admin'): ?>
            <div class="card">
                <h2 class="section-title">Zarządzanie systemem</h2>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <a href="admin/users.php" class="action-btn btn-primary" style="text-align: center; text-decoration: none; display: block;">Użytkownicy</a>
                    <a href="admin/sale.php" class="action-btn btn-primary" style="text-align: center; text-decoration: none; display: block;">Sale</a>
                    <a href="admin/rezerwacje.php" class="action-btn btn-primary" style="text-align: center; text-decoration: none; display: block;">Rezerwacje</a>
                    <a href="admin/finanse.php" class="action-btn btn-primary" style="text-align: center; text-decoration: none; display: block;">Finanse</a>
                </div>
            </div>
            
            <div class="card">
                <h2 class="section-title">Ostatnie aktywności</h2>
                <p>Tutaj będzie wyświetlany log ostatnich aktywności w systemie.</p>
            </div>
            
        <?php elseif ($user['rola'] === 'manager'): ?>
            <div class="card">
                <h2 class="section-title">Nadchodzące wydarzenia</h2>
                <?php if (!empty($additional_data['nadchodzace_wydarzenia'])): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Sala</th>
                                    <th>Klient</th>
                                    <th>Godziny</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($additional_data['nadchodzace_wydarzenia'] as $wydarzenie): ?>
                                    <tr>
                                        <td><?php echo date('d.m.Y', strtotime($wydarzenie['data_wydarzenia'])); ?></td>
                                        <td><?php echo htmlspecialchars($wydarzenie['nazwa_sali']); ?></td>
                                        <td><?php echo htmlspecialchars($wydarzenie['imie'] . ' ' . htmlspecialchars($wydarzenie['nazwisko'])); ?></td>
                                        <td><?php echo substr($wydarzenie['godzina_rozpoczecia'], 0, 5) . ' - ' . substr($wydarzenie['godzina_zakonczenia'], 0, 5); ?></td>
                                        <td>
                                            <?php 
                                                $status_class = '';
                                                if ($wydarzenie['status'] === 'potwierdzona') $status_class = 'badge-success';
                                                elseif ($wydarzenie['status'] === 'anulowana') $status_class = 'badge-danger';
                                                elseif ($wydarzenie['status'] === 'zrealizowana') $status_class = 'badge-info';
                                                else $status_class = 'badge-warning';
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($wydarzenie['status']); ?></span>
                                        </td>
                                        <td>
                                            <button class="action-btn btn-primary">Szczegóły</button>
                                            <button class="action-btn btn-secondary">Edytuj</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Brak nadchodzących wydarzeń.</p>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h2 class="section-title">Zadania do wykonania</h2>
                <?php if (!empty($additional_data['zadania'])): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tytuł</th>
                                    <th>Przypisane do</th>
                                    <th>Termin</th>
                                    <th>Priorytet</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($additional_data['zadania'] as $zadanie): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($zadanie['tytul']); ?></td>
                                        <td><?php echo htmlspecialchars($zadanie['imie'] . ' ' . $zadanie['nazwisko']); ?></td>
                                        <td><?php echo $zadanie['data_zakonczenia'] ? date('d.m.Y', strtotime($zadanie['data_zakonczenia'])) : 'Brak terminu'; ?></td>
                                        <td>
                                            <?php 
                                                $priority_class = '';
                                                if ($zadanie['priorytet'] === 'wysoki') $priority_class = 'priority-high';
                                                elseif ($zadanie['priorytet'] === 'sredni') $priority_class = 'priority-medium';
                                                else $priority_class = 'priority-low';
                                            ?>
                                            <span class="<?php echo $priority_class; ?>"><?php echo ucfirst($zadanie['priorytet']); ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                                $status_class = '';
                                                if ($zadanie['status'] === 'zakonczone') $status_class = 'badge-success';
                                                elseif ($zadanie['status'] === 'anulowane') $status_class = 'badge-danger';
                                                elseif ($zadanie['status'] === 'w trakcie') $status_class = 'badge-primary';
                                                else $status_class = 'badge-warning';
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($zadanie['status']); ?></span>
                                        </td>
                                        <td>
                                            <button class="action-btn btn-primary">Szczegóły</button>
                                            <button class="action-btn btn-secondary">Edytuj</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Brak zadań do wykonania.</p>
                <?php endif; ?>
                <button class="action-btn btn-primary" style="margin-top: 15px;">Dodaj nowe zadanie</button>
            </div>
            
        <?php elseif (in_array($user['rola'], ['kelner', 'kucharz', 'sprzataczka'])): ?>
            <div class="card">
                <h2 class="section-title">Moje zadania</h2>
                <?php if (!empty($additional_data['moje_zadania'])): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tytuł</th>
                                    <th>Termin</th>
                                    <th>Priorytet</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($additional_data['moje_zadania'] as $zadanie): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($zadanie['tytul']); ?></td>
                                        <td><?php echo $zadanie['data_zakonczenia'] ? date('d.m.Y', strtotime($zadanie['data_zakonczenia'])) : 'Brak terminu'; ?></td>
                                        <td>
                                            <?php 
                                                $priority_class = '';
                                                if ($zadanie['priorytet'] === 'wysoki') $priority_class = 'priority-high';
                                                elseif ($zadanie['priorytet'] === 'sredni') $priority_class = 'priority-medium';
                                                else $priority_class = 'priority-low';
                                            ?>
                                            <span class="<?php echo $priority_class; ?>"><?php echo ucfirst($zadanie['priorytet']); ?></span>
                                        </td>
                                        <td>
                                            <?php 
                                                $status_class = '';
                                                if ($zadanie['status'] === 'zakonczone') $status_class = 'badge-success';
                                                elseif ($zadanie['status'] === 'anulowane') $status_class = 'badge-danger';
                                                elseif ($zadanie['status'] === 'w trakcie') $status_class = 'badge-primary';
                                                else $status_class = 'badge-warning';
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($zadanie['status']); ?></span>
                                        </td>
                                        <td>
                                            <button class="action-btn btn-primary">Szczegóły</button>
                                            <?php if ($zadanie['status'] === 'nowe' || $zadanie['status'] === 'w trakcie'): ?>
                                                <button class="action-btn btn-secondary" onclick="updateTaskStatus(<?php echo $zadanie['zadanie_id']; ?>, '<?php echo $zadanie['status'] === 'nowe' ? 'w trakcie' : 'zakonczone'; ?>')">
                                                    <?php echo $zadanie['status'] === 'nowe' ? 'Rozpocznij' : 'Zakończ'; ?>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Brak zadań przypisanych do Ciebie.</p>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <h2 class="section-title">Moje wydarzenia</h2>
                <?php if (!empty($additional_data['moje_wydarzenia'])): ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Sala</th>
                                    <th>Godziny</th>
                                    <th>Status</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($additional_data['moje_wydarzenia'] as $wydarzenie): ?>
                                    <tr>
                                        <td><?php echo date('d.m.Y', strtotime($wydarzenie['data_wydarzenia'])); ?></td>
                                        <td><?php echo htmlspecialchars($wydarzenie['nazwa_sali']); ?></td>
                                        <td><?php echo substr($wydarzenie['godzina_rozpoczecia'], 0, 5) . ' - ' . substr($wydarzenie['godzina_zakonczenia'], 0, 5); ?></td>
                                        <td>
                                            <?php 
                                                $status_class = '';
                                                if ($wydarzenie['status'] === 'potwierdzona') $status_class = 'badge-success';
                                                elseif ($wydarzenie['status'] === 'anulowana') $status_class = 'badge-danger';
                                                elseif ($wydarzenie['status'] === 'zrealizowana') $status_class = 'badge-info';
                                                else $status_class = 'badge-warning';
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($wydarzenie['status']); ?></span>
                                        </td>
                                        <td>
                                            <button class="action-btn btn-primary">Szczegóły</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p>Nie jesteś przypisany do żadnych nadchodzących wydarzeń.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h2 class="section-title">Moje dane</h2>
            <form>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label>Imię</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['imie']); ?>" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label>Nazwisko</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['nazwisko']); ?>" class="form-control" style="width: 100%;">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label>Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="form-control" style="width: 100%;">
                    </div>
                    <div>
                        <label>Telefon</label>
                        <input type="tel" value="<?php echo htmlspecialchars($user['telefon']); ?>" class="form-control" style="width: 100%;">
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label>Adres</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['adres']); ?>" class="form-control" style="width: 100%;">
                </div>
                
                <button type="button" class="action-btn btn-primary">Zmień hasło</button>
                <button type="submit" class="action-btn btn-primary">Zapisz zmiany</button>
            </form>
        </div>
        
        <form action="logout.php" method="post">
            <button type="submit" class="logout-btn">Wyloguj się</button>
        </form>
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
        // Funkcja do aktualizacji statusu zadania
        function updateTaskStatus(taskId, newStatus) {
            fetch('update_task.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    task_id: taskId,
                    new_status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Status zadania został zaktualizowany!');
                    location.reload();
                } else {
                    alert('Wystąpił błąd: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Wystąpił błąd podczas aktualizacji zadania');
            });
        }
        
        // Funkcja do pokazywania/ukrywania formularza zmiany hasła
        function togglePasswordForm() {
            const form = document.getElementById('passwordForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>