<?php
session_start();
require_once 'database.php';

$stmt = $pdo->prepare("SELECT id, name FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';

$query = "SELECT p.id, p.name, p.cena, p.image FROM products p 
          LEFT JOIN product_categories pc ON p.id = pc.product_id
          LEFT JOIN categories c ON c.id = pc.category_id WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND p.name LIKE :search";
    $params[':search'] = "%$search%";
}

if (!empty($category)) {
    $query .= " AND c.id = :category";
    $params[':category'] = $category;
}

if (!empty($min_price)) {
    $query .= " AND p.cena >= :min_price";
    $params[':min_price'] = $min_price;
}

if (!empty($max_price)) {
    $query .= " AND p.cena <= :max_price";
    $params[':max_price'] = $max_price;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$zalogowany = isset($_SESSION['zalogowany_uzytkownik']);
$czy_admin = $zalogowany && $_SESSION['zalogowany_uzytkownik']['id'] == 1;
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produkty - WhiskyStore</title>
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
        <h2>Wszystkie produkty</h2>

        <form method="GET" style="text-align: center; margin-bottom: 20px;">
            <input 
                type="text" 
                name="search" 
                placeholder="Wyszukaj produkt..." 
                value="<?php echo htmlspecialchars($search); ?>" 
                style="padding: 8px; width: 200px; margin-right: 10px;">
            
            <select name="category" style="padding: 8px; margin-right: 10px;">
                <option value="">Wszystkie kategorie</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" 
                        <?php echo $category == $cat['id'] ? 'selected' : ''; ?> >
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input 
                type="number" 
                name="min_price" 
                placeholder="Cena od" 
                value="<?php echo htmlspecialchars($min_price); ?>" 
                style="padding: 8px; width: 100px; margin-right: 10px;">

            <input 
                type="number" 
                name="max_price" 
                placeholder="Cena do" 
                value="<?php echo htmlspecialchars($max_price); ?>" 
                style="padding: 8px; width: 100px; margin-right: 10px;">

            <button type="submit" style="padding: 8px 12px;">Filtruj</button>
        </form>

        <div class="products">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <a href="produkt.php?id=<?php echo $product['id']; ?>">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p>Cena: <?php echo htmlspecialchars($product['cena']); ?> PLN</p>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align: center; font-size: 18px;">Brak produktów spełniających kryteria wyszukiwania.</p>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 WhiskyStore. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>
