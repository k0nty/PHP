<?php

require 'db.php';

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
    
    $id = $_POST['id'];
    $body = $_POST['body'];

    $query = "UPDATE notes SET body = q_body WHERE id = q_id";

    $db->query($query, ['q_body' => $body,'q_id' => $id]);
}

$note = $db->query("SELECT * FROM notes WHERE id = 1")->fetch();

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Тест Update</title>
</head>
<body>
    <h2>Редагування запису</h2>
    <form method="POST">
        <input type="hidden" name="_method" value="PUT">
        <input type="hidden" name="id" value="1">
        
        <textarea name="body" rows="5" cols="30"><?= htmlspecialchars($note['body']) ?></textarea>
        <br><br>
        <button type="submit">Оновити</button>
    </form>
</body>
</html>