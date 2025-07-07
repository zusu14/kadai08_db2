<?php
// DB接続
include('db_connect.php');
$pdo = db_connect();

// 課題一覧取得
$sql = 'SELECT id, title FROM kadai ORDER by id ASC';
$stmt = $pdo->prepare($sql);
try {
  $stmt->execute();
}catch(PDOException $e) {
  // 連想配列（PHP）→JSON文字列
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

$kadai_list = $stmt->fetchAll(PDO::FETCH_ASSOC); // カラム名をキーにした連想配列で取得

?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>課題一覧</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>課題一覧</h1>
  <ul>
    <?php foreach($kadai_list as $kadai): ?>
      <li>
        <!-- 個別掲示板へのリンク -->
        <!-- htmlspecialchars():XSS対策 -->
        <a href="kadai.php?kadai_id=<?= htmlspecialchars($kadai['id']) ?>">
          <?= htmlspecialchars($kadai['title']) ?>
        </a>
      </li>  
      <?php endforeach; ?>
  </ul>
</body>
</html>