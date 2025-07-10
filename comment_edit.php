<?php
include('db_connect.php');
include('init_user.php');
$pdo=db_connect();
$user_id=(int)initUser($pdo);

// GETパラメータチェック
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  exit('ParamErroe');
}
$comment_id = (int)$_GET['id'];

// コメント取得
$sql = 'SELECT * FROM comment WHERE id = :id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':id', $comment_id, PDO::PARAM_INT);
$stmt->execute();
$comment = $stmt->fetch(PDO::FETCH_ASSOC);
// セッションのuser_idと一致しなかったら処理終了
if(!$comment || (int)$comment['user_id'] !== $user_id) {
  exit('権限がありません');
}
// var_dump($comment);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>コメントの編集</title>
</head>
<body>
  <h1>コメント編集</h1>
  <form action="comment_update.php" method="POST">
    <label for="">ニックネーム：</label>
    <input type="text" name="nickname" value="<?= htmlspecialchars($comment['nickname']) ?>" required><br>

    <label for="">コメント：</label>
    <textarea name="comment" id="" rows="5" cols="40" required><?= htmlspecialchars($comment['comment']) ?></textarea>

    <!-- 隠し項目 -->
    <input type="hidden" name="id" value="<?= $comment['id'] ?>">
    <input type="hidden" name="kadai_id" value="<?= $comment['kadai_id'] ?>">
    
    <button type="submit">更新</button>
  </form>
  <p><a href="kadai.php?kadai_id=<?= $comment['kadai_id'] ?>">戻る</a></p>
</body>
</html>
