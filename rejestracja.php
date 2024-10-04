<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Rejestracja</title>
    <link rel="stylesheet" href="rejestracja.css">
</head>
<body>
    <div class="okienko">
        <div class="baner">
        </div>
        <h3>ZAREJESTRUJ SIĘ</h3>

        <?php
        
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            require_once 'database.php'; 

            $login = $_POST['login'];
            $haslo = $_POST['haslo']; 
            $email = $_POST['email'];
            $adres = $_POST['adres'];
            $data = $_POST['data_uro'];
            $pytanie = $_POST['przypomnienie'];
            $odpowiedz = $_POST['pytanie'];
            $komunikat = '';

            
            $loginzbazy = $pdo->prepare("SELECT login FROM konto_uzytkownika WHERE login = :login");
            $loginzbazy->execute(['login' => $login]);

            if ($loginzbazy->fetchColumn()) {
                $komunikat = "Dany login jest zajęty.";
            } elseif (empty($login) || empty($haslo) || empty($email) || empty($adres) || empty($odpowiedz)) {
                $komunikat = "Nie wypełniłeś wszystkiego.";
            } elseif ($data > date('Y-m-d')) {
                $komunikat = "Zła data.";
            } elseif (strpos($email, '@') === false) {
                $komunikat = "Błędny email.";
            } else {
                
                $haslo_hash = password_hash($haslo, PASSWORD_DEFAULT);

                
                $dodawanie = $pdo->prepare("INSERT INTO konto_uzytkownika (login, haslo, email, adres, data_urodzenia, id_pytania_pomocniczego, odpowiedz) 
                    VALUES (:login, :haslo, :email, :adres, :data, :pytanie, :odpowiedz)");
                $dodawanie->execute([
                    'login' => $login,
                    'haslo' => $haslo_hash,
                    'email' => $email,
                    'adres' => $adres,
                    'data' => $data,
                    'pytanie' => $pytanie,
                    'odpowiedz' => $odpowiedz
                ]);

                header("Location: logowanie.php");
                exit;
            }
        }
        ?>

        <form action="" method="post"> 
            <label>Login</label><br>
            <input type="text" placeholder="Podaj login" name="login" required><br>
            <label>Haslo</label><br>
            <input type="password" placeholder="Podaj haslo" name="haslo" id="haslo" required><br> 
            <img src="oko.png" id="oczko" alt="Pokaż hasło"><br>
            <label>Email</label><br>
            <input type="text" placeholder="Podaj email" name="email" required><br>
            <label>Adres</label><br>
            <input type="text" placeholder="Podaj adres" name="adres" required><br>
            <label>Data urodzenia</label><br>
            <input type="date" placeholder="Podaj datę urodzenia" name="data_uro" id="data_uro" required><br>
            <label>Pytanie do przypomnienia hasła</label>
            <select name="przypomnienie" required>
                <option value="1">Twój pierwszy koncert?</option>
                <option value="2">Imię pierwszej miłości?</option>
                <option value="3">Imię pani, której nienawidziłeś w podstawówce?</option>
                <option value="4">Twoja pierwsza praca?</option>
            </select><br>
            <label>Podaj odpowiedź na pytanie, które wybrałeś</label><br>
            <input type="text" placeholder="Podaj odpowiedź" name="pytanie" required><br>

            <?php if (!empty($komunikat)): ?>
                <p style="color:red;"><?php echo $komunikat; ?></p>
            <?php endif; ?>

            <input type="submit" name="zarejestruj" value="Zarejestruj" id="zarejestruj">
        </form>
    </div>
    <script>
        document.getElementById('oczko').addEventListener('click', function() {
            var input = document.getElementById('haslo'); 
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        });
    </script>
</body>
</html>
