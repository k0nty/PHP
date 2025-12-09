<?php

try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=colloquium;charset=utf8', 'root', '', [
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die($e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
    
    $id = $_POST['id'];
    $body = $_POST['body'];

    $sql = "UPDATE notes SET body = :q_body WHERE id = :q_id";

    $statement = $pdo->prepare($sql);

    $statement->execute([':q_body' => $body,':q_id' => $id]);
}

$stmt = $pdo->query("SELECT * FROM notes WHERE id = 1");
$note = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Update</title>
</head>
<body>
    <h2>Редагування</h2>
    
    <?php if ($note): ?>
    <form method="POST">
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="id" value="<?= $note['id'] ?>">
        
        <textarea name="body" rows="5" cols="40"><?= htmlspecialchars($note['body']) ?></textarea>
        <br><br>
        <button type="submit">Зберегти</button>
    </form>
    <?php else: ?>
        <p>запис не знайдено</p>
    <?php endif; ?>
</body>

</html>
