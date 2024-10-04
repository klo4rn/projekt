<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Zmiana hasła</title>
    <link rel="stylesheet" href="przypominanie.css">
</head>
<body>
    <div class="okienko">
        <div class="baner"></div>
        <h3>ZMIANA HASŁA</h3>

        <?php
        
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            require_once 'database.php'; 

            $login = $_POST['login'];
            $pytanie = $_POST['przypomnienie'];
            $odpowiedz = $_POST['pytanie'];
            $nhaslo = $_POST['nhaslo']; 

            
            $starylogin = $pdo->prepare("SELECT login FROM konto_uzytkownika WHERE login = :login");
            $starylogin->execute(['login' => $login]);
            $sl = $starylogin->fetchColumn();

            $starepytanie = $pdo->prepare("SELECT id_pytania_pomocniczego FROM konto_uzytkownika WHERE login = :login");
            $starepytanie->execute(['login' => $login]);
            $sp = $starepytanie->fetchColumn();

            $staraodpowiedz = $pdo->prepare("SELECT odpowiedz FROM konto_uzytkownika WHERE login = :login");
            $staraodpowiedz->execute(['login' => $login]);
            $so = $staraodpowiedz->fetchColumn();

           
            if (empty($login) || empty($nhaslo) || empty($odpowiedz)) {
                echo "Nie wypełniłeś wszystkiego.";
            } elseif ($login != $sl || $pytanie != $sp || $odpowiedz != $so) {
                echo "Dane są złe.";
            } else {
                
                $haslo_hash = password_hash($nhaslo, PASSWORD_DEFAULT);

                
                $zmiana = $pdo->prepare("UPDATE konto_uzytkownika SET haslo = :haslo WHERE login = :login");
                $zmiana->execute(['haslo' => $haslo_hash, 'login' => $login]);

                header("Location: logowanie.php");
                exit;
            }
        }
        ?>

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

            <label>Nowe haslo</label><br>
            <input type="password" placeholder="Podaj haslo" name="nhaslo" id="nhaslo" required>
            <img src="oko.png" id="oczko" alt="Pokaż hasło"><br>
            <input type="submit" name="przypomnienie" value="Zmień hasło">
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
