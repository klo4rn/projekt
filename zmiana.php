<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Zmiana hasła</title>
    <link rel="stylesheet" href="logowanie.css">
</head>
<body>
    <div class="okienko">
        <div class="baner"></div>
        <h3>ZMIANA HASŁA</h3>

        <?php
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        $komunikat = '';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            require_once 'database.php';

            $login = $_POST['login'];
            $pytanie = $_POST['przypomnienie'];
            $odpowiedz = $_POST['pytanie'];
            $nhaslo = $_POST['nhaslo'];
            $starylogin = $pdo->query("SELECT login from konto_uzytkownika where login ='$login'");
            $starepytanie = $pdo->query("SELECT id_pytania_pomocniczego from konto_uzytkownika where login ='$login'");
$staraodpowiedz = $pdo->query("SELECT odpowiedz from konto_uzytkownika where login = '$login'");
 foreach($starylogin as $staryloginrow)
 {
    $sl = $staryloginrow["login"];
 };
 foreach($starepytanie as $starepytanierow)
 {
    $sp = $starepytanierow["id_pytania_pomocniczego"];
 }
 foreach($staraodpowiedz as $staraodpowiedzrow){
    $so = $staraodpowiedzrow["odpowiedz"];

 }
 echo $so;
 echo $sl;
 echo $sp;
 echo $odpowiedz;
 echo $login;
 echo $pytanie;
            if ($login !== $sl || $pytanie != $sp || $odpowiedz != $so) {
                $komunikat = "Dane są złe.";
            } else {
                $haslo_hash = password_hash($nhaslo, PASSWORD_DEFAULT);
                $zmiana = $pdo->query("UPDATE konto_uzytkownika SET haslo = '$haslo_hash' WHERE login = '$login'");
                header("Location: logowanie.php");
                exit;
            }
        }
        ?>

        <?php if ($komunikat): ?>
            <p style="color:red;"><?php echo $komunikat; ?></p>
        <?php endif; ?>

        <form action="" method="post">
            <label>Login</label><br>
            <input type="text" placeholder="Podaj login" name="login" required><br>
            <label>Wybierz pytanie, które wybrałeś podczas rejestracji</label>
            <select name="przypomnienie" required>
                <option value="1">Twój pierwszy koncert?</option>
                <option value="2">Imię pierwszej miłości?</option>
                <option value="3">Imię pani, której nienawidziłeś w podstawówce?</option>
                <option value="4">Twoja pierwsza praca?</option>
            </select><br>
            <label>Odpowiedź na pytanie, które podałeś przy rejestracji</label><br>
            <input type="text" placeholder="Podaj odpowiedź" name="pytanie" required><br>
            <label>Nowe hasło</label><br>
            <input type="password" placeholder="Podaj hasło" name="nhaslo" id="nhaslo" required>
            <img src="oko.png" id="oczko" alt="Pokaż hasło"><br>
            <input type="submit" name="zmiana" value="Zmień hasło">
        </form>
    </div>

    <script>
        document.getElementById('oczko').addEventListener('click', function() {
            var input = document.getElementById('nhaslo');
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        });
    </script>
</body>
</html>
