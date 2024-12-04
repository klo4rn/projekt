<?php
include('database.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$category_name]);
    echo "Kategoria dodana!";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_category'])) {
    $category_id = $_POST['category_id'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    echo "Kategoria usunięta!";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'];
    $new_name = $_POST['new_name'];
    $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
    $stmt->execute([$new_name, $category_id]);
    echo "Kategoria zaktualizowana!";
}

$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Kategorie</title>
    <link rel="stylesheet" href="styling.css">
</head>
<body>
<button class="przenies" onclick="window.location.href='admin.php'">Powrót</button>
    <h1>Zarządzanie Kategoriami</h1>
    <div class="form-container">
    <h2>Dodaj Nową kategorie</h2>
    <form action="categories.php" method="POST">
        <input type="text" name="category_name" placeholder="Nazwa kategorii" required>
        <button type="submit" name="add_category">Dodaj kategorię</button>
    </form>
</div>

    <h2>Lista Kategorii</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nazwa</th>
            <th>Akcje</th>
        </tr>
        <?php foreach ($categories as $category): ?>
            <tr>
                <td><?php echo htmlspecialchars($category['id']); ?></td>
                <td><?php echo htmlspecialchars($category['name']); ?></td>
                <td>
                    <form action="categories.php" method="POST" style="display:inline;">
                        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                        <input type="text" name="new_name" placeholder="Nowa nazwa" required>
                        <button type="submit" name="edit_category">Edytuj</button>
                    </form>

                    <form action="categories.php" method="POST" style="display:inline;">
                        <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                        <button type="submit" name="delete_category" onclick="return confirm('Czy na pewno chcesz usunąć tę kategorię?')">Usuń</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    

</body>
</html>
