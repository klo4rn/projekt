<?php
session_start();
require_once 'database.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id <= 0) {
    header("Location: glowna.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, cena, image FROM products WHERE id = :id");
$stmt->execute([':id' => $product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: index.php");
    exit;
}

$stmt_params = $pdo->prepare("SELECT name, value FROM product_parameters 
                              INNER JOIN parameters ON product_parameters.parameter_id = parameters.id 
                              WHERE product_id = :product_id");
$stmt_params->execute([':product_id' => $product_id]);
$parameters = $stmt_params->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produkt: <?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="produkt.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>WhiskyStore</h1>
        </div>
        <div class="user-panel">
            <a href="kosz.php" class="btn">Kosz</a>
            <?php if (isset($_SESSION['zalogowany_uzytkownik'])): ?>
                <span>Witaj, <?php echo htmlspecialchars($_SESSION['zalogowany_uzytkownik']['login']); ?></span>
                <a href="wyloguj.php" class="btn">Wyloguj</a>
            <?php else: ?>
                <a href="logowanie.php" class="btn">Zaloguj się</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="nav-buttons">
        <a href="glowna.php" class="btn">Główna</a>
        <a href="produkty.php" class="btn">Produkty</a>
        <a href="kontakt.php" class="btn">Kontakt</a>
    </div>

    <div class="product-details">
        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
        <div class="product-info">
            <div class="zdjecie">
                <img width=25% height=25% src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            </div>
            <div class="product-description">
                <p><strong>Cena:</strong> <?php echo htmlspecialchars($product['cena']); ?> PLN</p>
                <h3>Parametry produktu:</h3>
                <ul>
                    <?php foreach ($parameters as $param): ?>
                        <li><strong><?php echo htmlspecialchars($param['name']); ?>:</strong> <?php echo htmlspecialchars($param['value']); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button class="btn">Dodaj do kosza</button>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 WhiskyStore. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
