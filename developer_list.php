<?php
// DB接続
include('db_connect.php');
$pdo = db_connect();

// 開発者一覧取得
$sql = 'SELECT id, name FROM developer ORDER BY id ASC';
$stmt = $pdo->prepare($sql);

try {
  $stmt->execute();
}catch(PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$developers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>開発者一覧</title>
  <link rel="stylesheet" href="developer_style.css">
</head>
<body>
  <ul>
    <?php foreach($developers as $developer): ?>
      <li>
        <!-- Single Developer View へのリンク -->
        <!-- htmlspecialchars():XSS対策 -->
        <a href="developer.php?id=<?= htmlspecialchars($developer['id']) ?>">
          <?= htmlspecialchars($developer['name']) ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</body>
</html>