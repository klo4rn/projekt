<?php
include('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $stmt = $db->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$category_name]);
    echo "Kategoria dodana!";
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Kategorie</title>
    <link rel="stylesheet" href="styling.css">
</head>
<body>
    <h1>Zarządzanie Kategoriami</h1>
    <form action="categories.php" method="POST">
        <input type="text" name="category_name" placeholder="Nazwa kategorii" required>
        <button type="submit" name="add_category">Dodaj kategorię</button>
    </form>
</body>
</html>
