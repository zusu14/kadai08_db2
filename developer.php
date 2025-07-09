<?php
// var_dump($_GET);
// exit();

// DB接続
include('db_connect.php');
$pdo = db_connect();

// developer_idの検証
if(!isset($_GET['id']) || !is_numeric($_GET['id'])){
  exit('ParamError');
}
$developer_id = (int)$_GET['id']; // キャスト

// 開発者名の取得
$sql = 'SELECT name FROM developer WHERE id = :developer_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':developer_id', $developer_id, PDO::PARAM_INT);
$stmt->execute();
$developer = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$developer){
  exit('該当する開発者が見つかりません');
}

// 課題一覧取得
$sql = 'SELECT id, title FROM kadai WHERE developer_id = :developer_id ORDER by id ASC';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':developer_id', $developer_id, PDO::PARAM_INT);
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
  <title><?= htmlspecialchars($developer['name']) ?>の課題一覧</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1><?= htmlspecialchars($developer['name']) ?>の課題一覧</h1>
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