<?php
session_start();
require_once 'database.php';

$stmt = $pdo->prepare("SELECT id, name, cena, image FROM products LIMIT 5");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$zalogowany = isset($_SESSION['zalogowany_uzytkownik']);
$czy_admin = $zalogowany && $_SESSION['zalogowany_uzytkownik']['id'] == 1;

$stmt = $pdo->prepare("SELECT image, name FROM products LIMIT 3");
$stmt->execute();
$carousel_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sklep z Whisky</title>
    <link rel="stylesheet" href="glowna.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>WhiskyStore</h1>
        </div>
        <div class="user-panel">
        <a href="kosz.php" class="btn">Kosz</a>

            <?php if ($zalogowany): ?>
                <span>Witaj, <?php echo htmlspecialchars($_SESSION['zalogowany_uzytkownik']['login']); ?></span>
                <?php if ($czy_admin): ?>
                    <a href="admin.php" class="btn">Panel administracyjny</a>
                <?php else: ?>
                    <a href="panel_uzytkownika.php" class="btn">Panel użytkownika</a>
                    <a href="kosz.php" class="btn">Kosz</a>
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

    <section class="carousel">
        <div class="carousel-container">
            <?php foreach ($carousel_products as $index => $product): ?>
                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="product-list">
        <h2>BESTSELLERY</h2>
        <div class="products">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <a href="produkt.php?id=<?php echo $product['id']; ?>">
                        <img  src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>Cena: <?php echo htmlspecialchars($product['cena']); ?> PLN</p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 WhiskyStore. Wszystkie prawa zastrzeżone.</p>
    </footer>

    <script>
        let currentIndex = 0;
        const items = document.querySelectorAll('.carousel-item');
        const totalItems = items.length;

        function showNextItem() {
            items[currentIndex].classList.remove('active');
            currentIndex = (currentIndex + 1) % totalItems;
            items[currentIndex].classList.add('active');
        }

        setInterval(showNextItem, 3000);
    </script>
</body>
</html>
