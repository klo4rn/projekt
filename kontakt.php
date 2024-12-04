<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontakt - WhiskyStore</title>
    <link rel="stylesheet" href="glowna.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>WhiskyStore</h1>
        </div>
        <div class="user-panel">
            <a href="kosz.php" class="btn">Kosz</a>
            <?php 
            session_start();
            if (isset($_SESSION['zalogowany_uzytkownik'])): ?>
                <span>Witaj, <?php echo htmlspecialchars($_SESSION['zalogowany_uzytkownik']['login']); ?></span>
                <?php if ($_SESSION['zalogowany_uzytkownik']['id'] == 1): ?>
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

    <div class="nav-buttons">
        <button onclick="window.location.href='index.php'">Główna</button>
        <button onclick="window.location.href='produkty.php'">Produkty</button>
        <button onclick="window.location.href='kontakt.php'">Kontakt</button>
    </div>

    <section class="product-list">
        <h2>Kontakt z nami</h2>
        <div style="text-align: center; margin-bottom: 20px;">
            <p><strong>Adres:</strong> WhiskyStore Sp. z o.o., ul. Szklana 15, 00-123 Warszawa</p>
            <p><strong>Telefon:</strong> +48 123 456 789</p>
            <p><strong>Email:</strong> kontakt@whiskystore.pl</p>
            <p><strong>Godziny otwarcia:</strong> Pon-Pt 9:00 - 17:00</p>
        </div>
    </section>

    <section style="padding: 20px; text-align: center;">
        <h2>Znajdź nas na mapie</h2>
        <div style="margin: 0 auto; max-width: 600px; height: 400px;">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2436.603755522531!2d21.012228315805657!3d52.22967517975692!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x471ecc6698f1a2fb%3A0x63180b6e0b7a2f35!2sPalace%20of%20Culture%20and%20Science!5e0!3m2!1sen!2spl!4v1607361156245!5m2!1sen!2spl" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 WhiskyStore. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
