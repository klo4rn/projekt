<?php
session_start();
require_once 'database.php';

if (!isset($_SESSION['zalogowany_uzytkownik'])) {
    header("Location: logowanie.php");
    exit;
}
$is_logged_in = isset($_SESSION['zalogowany_uzytkownik']);
$user_id = $_SESSION['zalogowany_uzytkownik']['id'];
$czy_admin = $is_logged_in && $_SESSION['zalogowany_uzytkownik']['id'] == 1;
$stmt_user = $pdo->prepare("SELECT * FROM konto_uzytkownika WHERE id = :id");
$stmt_user->execute([':id' => $user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['update'])) {
    $email = $_POST['email'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error_message = "Hasła się nie zgadzają!";
    } else {
      
        $password = $new_password ? password_hash($new_password, PASSWORD_DEFAULT) : $user['haslo'];

        $stmt_update = $pdo->prepare("UPDATE konto_uzytkownika SET email = :email, adres = :address, data_urodzenia = :dob, haslo = :password WHERE id = :id");
        $stmt_update->execute([':email' => $email, ':address' => $address, ':dob' => $dob, ':password' => $password, ':id' => $user_id]);

        $_SESSION['zalogowany_uzytkownik']['email'] = $email; 

        header("Location: panel_uzytkownika.php"); 
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel użytkownika</title>
    <link rel="stylesheet" href="panel.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>WhiskyStore</h1>
        </div>
        <div class="user-panel">
            <a href="kosz.php" class="btn">Kosz</a>
            <?php if ($is_logged_in): ?>
                <span>Witaj, <?php echo htmlspecialchars($_SESSION['zalogowany_uzytkownik']['login']); ?></span>
                <?php if ($czy_admin): ?>
                    <a href="admin.php" class="btn">Panel administracyjny</a>
                <?php else: ?>
                    <a href="panel_uzytkownika.php" class="btn">Panel użytkownika</a>
                <?php endif; ?>
                <a href="wyloguj.php" class="btn">Wyloguj</a>
            <?php else: ?>
                <a href="logowanie.php" class="btn">Zaloguj się</a>
                <a href="rejestracja.php" class="btn">Zarejestruj się</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="panel-container">
        <div class="sidebar">
            <ul>
                <li><a href="#dane">Dane</a></li>
                <li><a href="#zamowienia">Historia zamówień</a></li>
            </ul>
        </div>

        <div class="content">
            <div id="dane" class="tab">
                <h2>Twoje dane</h2>
                <?php if (isset($error_message)): ?>
                    <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <form action="" method="POST">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                    <label for="address">Adres:</label>
                    <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($user['adres']); ?>" required>

                    <label for="dob">Data urodzenia:</label>
                    <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($user['data_urodzenia']); ?>" required>

                    <label for="new_password">Nowe hasło:</label>
                    <input type="password" name="new_password" id="new_password">

                    <label for="confirm_password">Potwierdź nowe hasło:</label>
                    <input type="password" name="confirm_password" id="confirm_password">

                    <button type="submit" name="update" class="btn">Zapisz zmiany</button>
                </form>
            </div>

            <div id="zamowienia" class="tab">
                <h2>Historia zamówień</h2>
                <?php
                $stmt_orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
                $stmt_orders->execute([':user_id' => $user_id]);
                $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);
                ?>
                <?php if (empty($orders)): ?>
                    <p>Nie masz jeszcze żadnych zamówień.</p>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Data zamówienia</th>
                                <th>Status</th>
                                <th>Łączna cena</th>
                                <th>Imię i nazwisko</th>
                                <th>Adres</th>
                                <th>Numer kontaktowy</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                                    <td><?php echo htmlspecialchars($order['total_price']); ?> PLN</td>
                                    <td><?php echo htmlspecialchars($order['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['address']); ?></td>
                                    <td><?php echo htmlspecialchars($order['contact_number']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 WhiskyStore. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
