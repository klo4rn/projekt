<?php
include('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $category_ids = $_POST['category_ids']; 
    $parameter_values = $_POST['parameters'];
    $product_image = $_FILES['product_image'];
    
    if ($product_image['error'] == 0 && in_array($product_image['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
        $upload_dir = 'uploads/';
        $file_extension = pathinfo($product_image['name'], PATHINFO_EXTENSION);
        $target_file = $upload_dir . uniqid('img_') . '.' . $file_extension;
        
        if (move_uploaded_file($product_image['tmp_name'], $target_file)) {
            
            $stmt = $db->prepare("INSERT INTO products (name, image) VALUES (?, ?)");
            $stmt->execute([$product_name, $target_file]);

            $product_id = $db->lastInsertId();
            
            foreach ($category_ids as $category_id) {
                $db->prepare("INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)")
                   ->execute([$product_id, $category_id]);
            }

            foreach ($parameter_values as $parameter_id => $value) {
                $db->prepare("INSERT INTO product_parameters (product_id, parameter_id, value) VALUES (?, ?, ?)")
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
    <h1>Zarządzanie Produktami</h1>
   
    <form action="products.php" method="POST" enctype="multipart/form-data">
        <label for="product_name">Nazwa produktu:</label>
        <input type="text" name="product_name" id="product_name" required>

        <label for="product_image">Zdjęcie produktu:</label>
        <input type="file" name="product_image" id="product_image" accept="image/*" required>

        <label>Kategorie:</label>
        <?php
        $categories = $pdo->query("SELECT * FROM categories")->fetchAll();
        foreach ($categories as $category) {
            echo "<input type='checkbox' name='category_ids[]' value='{$category['id']}'> {$category['name']}<br>";
        }
        ?>

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
</body>
</html>
