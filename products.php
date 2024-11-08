<?php
include('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $parameter_values = $_POST['parameters'];
    $product_image = $_FILES['product_image'];
    
    if ($product_image['error'] == 0 && in_array($product_image['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
        $upload_dir = 'uploads/';
        $file_extension = pathinfo($product_image['name'], PATHINFO_EXTENSION);
        $target_file = $upload_dir . uniqid('img_') . '.' . $file_extension;
        
        if (move_uploaded_file($product_image['tmp_name'], $target_file)) {
            $stmt = $pdo->prepare("INSERT INTO products (name, image) VALUES (?, ?)");
            $stmt->execute([$product_name, $target_file]);

            $product_id = $pdo->lastInsertId();

            $pdo->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)")
                ->execute([$product_id, $category_id]);

            foreach ($parameter_values as $parameter_id => $value) {
                $pdo->prepare("INSERT INTO product_parameters (product_id, parameter_id, value) VALUES (?, ?, ?)")
                    ->execute([$product_id, $parameter_id, $value]);
            }
            echo "Produkt dodany pomyślnie!";
        } else {
            echo "Błąd przy przesyłaniu obrazu.";
        }
    } else {
        echo "Niepoprawny format zdjęcia. Obsługiwane formaty to JPEG, PNG i GIF.";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Produktami</title>
    <link rel="stylesheet" href="styling.css">
</head>
<body>
<button class="przenies" onclick="window.location.href='admin.php'">Powrót</button>

<h1>Zarządzanie Produktami</h1>

<div class="form-container">
    <h2>Dodaj Nowy Produkt</h2>
    <form action="products.php" method="POST" enctype="multipart/form-data">
        <label for="product_name">Nazwa produktu:</label>
        <input type="text" name="product_name" id="product_name" required>

        <label for="product_image">Zdjęcie produktu:</label>
        <input type="file" name="product_image" id="product_image" accept="image/*" required>

        <label for="category_id">Kategoria:</label>
        <select name="category_id" id="category_id" required>
            <option value="">Wybierz kategorię</option>
            <?php
            $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
            foreach ($categories as $category) {
                echo "<option value='{$category['id']}'>{$category['name']}</option>";
            }
            ?>
        </select>

        <label>Parametry:</label>
        <?php
        $parameters = $pdo->query("SELECT * FROM parameters")->fetchAll();
        foreach ($parameters as $parameter) {
            echo "<label for='param_{$parameter['id']}'>{$parameter['name']}:</label>";
            echo "<input type='text' name='parameters[{$parameter['id']}]' id='param_{$parameter['id']}'><br>";
        }
        ?>
        
        <button type="submit" name="add_product">Dodaj produkt</button>
    </form>
</div>

<div class="product-list">
    <h2>Lista Produktów</h2>
    <table>
        <tr>
            <th>Nazwa</th>
            <th>Zdjęcie</th>
            <th>Kategoria</th>
            <th>Parametry</th>
        </tr>
        <?php
        $products = $pdo->query("SELECT p.id, p.name, p.image, c.name AS category_name
                                 FROM products p
                                 LEFT JOIN product_categories pc ON p.id = pc.product_id
                                 LEFT JOIN categories c ON pc.category_id = c.id")->fetchAll();

        foreach ($products as $product) {
            echo "<tr>";
            echo "<td>{$product['name']}</td>";
            echo "<td><img src='{$product['image']}' alt='{$product['name']}' style='width:100px; height:auto;'></td>";
            echo "<td>{$product['category_name']}</td>";

            $parameters = $pdo->prepare("SELECT param.name, pp.value
                                         FROM product_parameters pp
                                         JOIN parameters param ON pp.parameter_id = param.id
                                         WHERE pp.product_id = ?");
            $parameters->execute([$product['id']]);
            $parameter_values = $parameters->fetchAll();

            echo "<td>";
            foreach ($parameter_values as $parameter) {
                echo "{$parameter['name']}: {$parameter['value']}<br>";
            }
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>


   
</body>
</html>
