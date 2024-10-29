<?php
require_once 'database.php';

if (isset($_POST['add_page'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $query = $db->prepare("INSERT INTO pages (title, content) VALUES (?, ?)");
    $query->bind_param('ss', $title, $content);
    $query->execute();

    header("Location: pages.php");
    exit();
}

if (isset($_POST['edit_page'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    $query = $db->prepare("UPDATE pages SET title = ?, content = ? WHERE id = ?");
    $query->bind_param('ssi', $title, $content, $id);
    $query->execute();

    header("Location: pages.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $query = $db->prepare("DELETE FROM pages WHERE id = ?");
    $query->bind_param('i', $id);
    $query->execute();

    header("Location: pages.php");
    exit();
}

$pages_result = $db->query("SELECT * FROM pages");

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
        <?php while ($page = $pages_result->fetch_assoc()): ?>
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
        $query = $db->prepare("SELECT * FROM pages WHERE id = ?");
        $query->bind_param('i', $id);
        $query->execute();
        $result = $query->get_result();
        $page = $result->fetch_assoc();
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
