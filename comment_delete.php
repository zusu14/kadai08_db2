<?php
include('db_connect.php');
include('init_user.php');
$pdo=db_connect();
$user_id=(int)initUser($pdo);

// GETパラメータチェック
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  exit('ParamError');
}

$comment_id = (int)$_GET['id'];

// コメント情報を取得
$sql = 'SELECT * FROM comment WHERE id=:id AND user_id=:user_id';
$stmt=$pdo->prepare($sql);
$stmt->bindValue(':id', $comment_id, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$comment = $stmt->fetch(PDO::FETCH_ASSOC);

// サーバー側のバリデーション
if (!$comment) {
  exit('不正なアクセスです。');
}

// 削除SQL
$sql = 'DELETE FROM comment WHERE id=:id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue('id', $comment_id, PDO::PARAM_INT);

try {
  $stmt->execute();
}catch(PDOException $e) {
  echo json_encode(["sql error" => $e->getMessage()]);
  exit();
}

// 元のkadaiージに戻る
$kadai_id = $comment['kadai_id'];
header("Location: kadai.php?kadai_id={$kadai_id}");
exit();

?>