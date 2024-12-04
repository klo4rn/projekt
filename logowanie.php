<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Logowanie</title>
    <link rel="stylesheet" href="logowanie.css">
</head>
<body>
    <div class="okienko">
        <div class="baner">
        </div>
        <h3>LOGOWANIE</h3>

        <?php
session_start(); 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once 'database.php'; 

    $login = $_POST['login'];
    $haslo = $_POST['haslo']; 

    $stmt = $pdo->prepare("SELECT id, haslo FROM konto_uzytkownika WHERE login = :login"); 
    $stmt->execute(['login' => $login]);

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $haslo_z_bazy = $row['haslo']; 

        if (password_verify($haslo, $haslo_z_bazy)) {
            $_SESSION['zalogowany_uzytkownik'] = [
                'id' => $row['id'],
                'login' => $login
            ]; 
            header("Location: index.php");
            exit;
        } else {
            echo '<p style="color:red;">Podane hasło jest błędne.</p>'; 
        }
    } else {
        echo '<p style="color:red;">Podany login jest błędny.</p>';
    }
}
?>

        <form action="" method="post"> 
            <label>Login</label><br>
            <input type="text" placeholder="Podaj login" name="login" required><br>
            <label>Hasło</label><br>
            <input type="password" placeholder="Podaj hasło" name="haslo" id="haslo" required> 
            <img src="oko.png" id="oczko" alt="Pokaż hasło"><br>
            <input type="submit" name="loguj" value="Loguj" id="loguj">
        </form>

        <a href="zmiana.php">Nie pamiętasz hasła?</a> 
        <h3>Jeśli nie masz konta, zarejestruj się</h3>
        <button id="rejestracja" onclick="przenies()">Zarejestruj się</button>
    </div>
</body>
<script>
    function przenies(){
        window.location.href = "rejestracja.php";
    }

    document.getElementById('oczko').addEventListener('click', function() {
        var input = document.getElementById('haslo'); 
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
    });
</script>
</html>


