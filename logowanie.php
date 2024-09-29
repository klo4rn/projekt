 <?php
require_once 'database.php';

$login = $_POST['login'];
$hasło = $_POST['hasło'];

$result = $pdo->query("SELECT hasło FROM konto_uzytkownika WHERE login = '$login'");

if ($result->rowCount() > 0) {
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $hasło_z_bazy = $row['hasło'];

    if (password_verify($hasło, $hasło_z_bazy)) {
        header("Location: index.html");
        exit;
    } else {
        echo  'Podane hasło jest błędne.';
    }
} else {
    echo 'Podany login jest błędny.';
}
?> 
