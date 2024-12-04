<?php
include('database.php');

// Obsługa dodawania produktu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $parameter_values = $_POST['parameters'] ?? []; // Domyślna wartość
    $product_image = $_FILES['product_image'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if ($product_image['error'] == 0 && in_array($product_image['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
        $upload_dir = 'uploads/';
        $file_extension = pathinfo($product_image['name'], PATHINFO_EXTENSION);
        $target_file = $upload_dir . uniqid('img_') . '.' . $file_extension;

        if (move_uploaded_file($product_image['tmp_name'], $target_file)) {
            $stmt = $pdo->prepare("INSERT INTO products (name, image, cena, ilosc) VALUES (?, ?, ?, ?)");
            $stmt->execute([$product_name, $target_file, $price, $quantity]);

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

// Obsługa edycji produktu
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    $parameters = $_POST['parameters'] ?? []; // Domyślna wartość

    if (!empty($_FILES['product_image']['name'])) {
        $product_image = $_FILES['product_image'];
        if ($product_image['error'] == 0 && in_array($product_image['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
            $upload_dir = 'uploads/';
            $file_extension = pathinfo($product_image['name'], PATHINFO_EXTENSION);
            $target_file = $upload_dir . uniqid('img_') . '.' . $file_extension;

            if (move_uploaded_file($product_image['tmp_name'], $target_file)) {
                $pdo->prepare("UPDATE products SET name = ?, image = ?, cena = ?, ilosc = ? WHERE id = ?")
                    ->execute([$product_name, $target_file, $price, $quantity, $product_id]);
            }
        }
    } else {
        $pdo->prepare("UPDATE products SET name = ?, cena = ?, ilosc = ? WHERE id = ?")
            ->execute([$product_name, $price, $quantity, $product_id]);
    }

    $pdo->prepare("UPDATE product_categories SET category_id = ? WHERE product_id = ?")
        ->execute([$category_id, $product_id]);

    $pdo->prepare("DELETE FROM product_parameters WHERE product_id = ?")
        ->execute([$product_id]);

    foreach ($parameters as $parameter_id => $value) {
        $pdo->prepare("INSERT INTO product_parameters (product_id, parameter_id, value) VALUES (?, ?, ?)")
            ->execute([$product_id, $parameter_id, $value]);
    }

    echo "Produkt zaktualizowany!";
}

// Pobieranie listy produktów
$products = $pdo->query("SELECT p.id, p.name, p.image, c.name AS category_name, p.cena, p.ilosc, pc.category_id
                         FROM products p
                         LEFT JOIN product_categories pc ON p.id = pc.product_id
                         LEFT JOIN categories c ON pc.category_id = c.id")->fetchAll();

// Pobieranie parametrów
$all_parameters = $pdo->query("SELECT * FROM parameters")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Produktami</title>
    <link rel="stylesheet" href="styling.css">
    <script>
        function openEditPopup(product, parameters) {
            const popup = document.getElementById('editPopup');
            popup.style.display = 'block';

            document.getElementById('edit_product_id').value = product.id;
            document.getElementById('edit_product_name').value = product.name;
            document.getElementById('edit_price').value = product.cena;
            document.getElementById('edit_quantity').value = product.ilosc;
            document.getElementById('edit_category_id').value = product.category_id;

            const parametersContainer = document.getElementById('edit_parameters_container');
            parametersContainer.innerHTML = ''; // Wyczyść stare parametry

            parameters.forEach(param => {
                const label = document.createElement('label');
                label.innerText = param.name;
                const input = document.createElement('input');
                input.type = 'text';
                input.name = `parameters[${param.parameter_id}]`;
                input.value = param.value || '';
                parametersContainer.appendChild(label);
                parametersContainer.appendChild(input);
                parametersContainer.appendChild(document.createElement('br'));
            });
        }

        function closeEditPopup() {
            document.getElementById('editPopup').style.display = 'none';
        }
    </script>
</head>
<body>
    <button class="przenies" onclick="window.location.href='admin.php'">Powrót</button>

    <h1>Zarządzanie Produktami</h1>

    <!-- Dodawanie produktu -->
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

            <label for="price">Cena:</label>
            <input type="number" name="price" id="price" step="0.01" required>

            <label for="quantity">Ilość:</label>
            <input type="number" name="quantity" id="quantity" required>

            <label>Parametry:</label>
            <?php
            foreach ($all_parameters as $parameter) {
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
                <th>Cena</th>
                <th>Ilość</th>
                <th>Parametry</th>
                <th>Akcje</th>
            </tr>
            <?php
            foreach ($products as $product) {
                $parameters = $pdo->prepare("SELECT param.name, param.id AS parameter_id, pp.value
                                             FROM product_parameters pp
                                             JOIN parameters param ON pp.parameter_id = param.id
                                             WHERE pp.product_id = ?");
                $parameters->execute([$product['id']]);
                $parameter_values = $parameters->fetchAll();

                echo "<tr>";
                echo "<td>{$product['name']}</td>";
                echo "<td><img src='{$product['image']}' alt='{$product['name']}' style='width:100px; height:auto;'></td>";
                echo "<td>{$product['category_name']}</td>";
                echo "<td>{$product['cena']} PLN</td>";
                echo "<td>{$product['ilosc']}</td>";

                echo "<td>";
                foreach ($parameter_values as $parameter) {
                    echo "{$parameter['name']}: {$parameter['value']}<br>";
                }
                echo "</td>";

                echo "<td>";
                echo "<button onclick='openEditPopup(" . json_encode($product) . ", " . json_encode($parameter_values) . ")'>Edytuj</button>";
                echo "<form action='products.php' method='POST' style='display:inline;'>
                        <input type='hidden' name='product_id' value='{$product['id']}'>
                        <button type='submit' name='delete_product'>Usuń</button>
                      </form>";
                echo "</td>";
                echo "</tr>";
            }
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
            $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
            $stmt->execute();
    }

          ?>
        </table>
    </div>

    <div id="editPopup" style="display:none; position:fixed; top:10%; left:25%; width:50%; background:white; border:1px solid #ccc; padding:20px;">
        <h2>Edytuj Produkt</h2>
        <form action="products.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="edit_product_id">

            <label for="edit_product_name">Nazwa produktu:</label>
            <input type="text" name="product_name" id="edit_product_name" required>

            <label for="edit_product_image">Zdjęcie produktu:</label>
            <input type="file" name="product_image" id="edit_product_image" accept="image/*">

            <label for="edit_category_id">Kategoria:</label>
            <select name="category_id" id="edit_category_id" required>
                <option value="">Wybierz kategorię</option>
                <?php
                foreach ($categories as $category) {
                    echo "<option value='{$category['id']}'>{$category['name']}</option>";
                }
                ?>
            </select>

            <label for="edit_price">Cena:</label>
            <input type="number" name="price" id="edit_price" step="0.01" required>

            <label for="edit_quantity">Ilość:</label>
            <input type="number" name="quantity" id="edit_quantity" required>

            <label>Parametry:</label>
            <div id="edit_parameters_container">
               
            </div>

            <button type="submit" name="edit_product">Zapisz zmiany</button>
            <button type="button" onclick="closeEditPopup()">Anuluj</button>
        </form>
    </div>
</body>
</html>
