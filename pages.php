<?php
require_once 'database.php';

if (isset($_POST['add_page'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $query = $pdo->prepare("INSERT INTO pages (title, content) VALUES (:title, :content)");
    $query->bindValue(':title', $title);
    $query->bindValue(':content', $content);
    $query->execute();

    header("Location: pages.php");
    exit();
}

if (isset($_POST['edit_page'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    $query = $pdo->prepare("UPDATE pages SET title = :title, content = :content WHERE id = :id");
    $query->bindValue(':title', $title);
    $query->bindValue(':content', $content);
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();

    header("Location: pages.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $query = $pdo->prepare("DELETE FROM pages WHERE id = :id");
    $query->bindValue(':id', $id, PDO::PARAM_INT);
    $query->execute();

    header("Location: pages.php");
    exit();
}

$pages_result = $pdo->query("SELECT * FROM pages");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Zarządzanie Stronami Informacyjnymi</title>
    <link rel="stylesheet" href="styling.css">
</head>
<body>

<h1>Zarządzanie Stronami Informacyjnymi</h1>

<div class="form-container">
    <h2>Dodaj Nową Stronę</h2>
    <form action="pages.php" method="POST">
        <label for="title">Tytuł:</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Treść:</label>
        <textarea id="content" name="content" rows="5" required></textarea>

        <button type="submit" name="add_page">Dodaj Stronę</button>
    </form>
</div>

<h2>Lista Stron Informacyjnych</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Tytuł</th>
            <th>Treść</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($page = $pages_result->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo $page['id']; ?></td>
                <td><?php echo htmlspecialchars($page['title']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($page['content'])); ?></td>
                <td>
                    <a href="pages.php?edit=<?php echo $page['id']; ?>">Edytuj</a> |
                    <a href="pages.php?delete=<?php echo $page['id']; ?>" onclick="return confirm('Czy na pewno chcesz usunąć tę stronę?');">Usuń</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php if (isset($_GET['edit'])): ?>
    <?php
        $id = $_GET['edit'];
        $query = $pdo->prepare("SELECT * FROM pages WHERE id = :id");
        $query->bindValue(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $page = $query->fetch(PDO::FETCH_ASSOC);
    ?>
    <div class="form-container">
        <h2>Edytuj Stronę</h2>
        <form action="pages.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $page['id']; ?>">
            
            <label for="title">Tytuł:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($page['title']); ?>" required>
            
            <label for="content">Treść:</label>
            <textarea id="content" name="content" rows="5" required><?php echo htmlspecialchars($page['content']); ?></textarea>
            
            <button type="submit" name="edit_page">Zapisz Zmiany</button>
        </form>
    </div>
<?php endif; ?>

</body>
</html>
