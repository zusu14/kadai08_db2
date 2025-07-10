<?php
include('db_connect.php');
include('init_user.php');
$pdo=db_connect();
$user_id=(int)initUser($pdo);

// POSTチェック
if(
  !isset($_POST['id']) || !is_numeric($_POST['id']) ||
  !isset($_POST['kadai_id']) || !is_numeric($_POST['kadai_id']) ||
  !isset($_POST['nickname']) || $_POST['nickname'] === '' ||
  !isset($_POST['comment']) || $_POST['comment'] === '' 
) {
  exit('ParamError');
}

$id = (int)$_POST['id'];
$kadai_id = (int)$_POST['kadai_id'];
$nickname = $_POST['nickname'];
$comment = $_POST['comment'];

// サーバー側でのバリデーション
$sql = 'SELECT * FROM comment WHERE id = :id AND user_id = :user_id';
$stmt = $pdo->prepare($sql);
$stmt->bindValue('id', $id, PDO::PARAM_INT);
$stmt->bindValue('user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$existing = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$existing){
  exit('不正なアクセスです。');
}

// 更新処理
$sql = 'UPDATE comment SET nickname = :nickname, comment = :comment, updated_at = NOW() WHERE id = :id';
$stmt=$pdo->prepare($sql);
$stmt->bindValue(':nickname', $nickname, PDO::PARAM_STR);
$stmt->bindValue(':comment', $comment, PDO::PARAM_STR);
$stmt->bindValue(':id', $id, PDO::PARAM_INT);

// 
try {
  $stmt->execute();
} catch (PDOException $e) {
  echo json_encode(["sql error" => $e->getMessage()]);
  exit();
}

// kadai.phpに戻る
header("Location: kadai.php?kadai_id={$kadai_id}");
exit();
?>