<?php
// var_dump($_POST);
// exit();

include('db_connect.php');
include('init_user.php');

$pdo = db_connect();
$user_id = initUser($pdo);

// 入力チェック
if (
  !isset($_POST['nickname']) || $_POST['nickname'] === '' ||
  !isset($_POST['comment']) || $_POST['comment'] === '' ||
  !isset($_POST['kadai_id']) || !is_numeric($_POST['kadai_id']) 
){
  exit('ParamError');
}
$nickname = $_POST['nickname'];
$comment = $_POST['comment'];
$kadai_id = (int)$_POST['kadai_id']; // int型にキャスト

// SQL作成&実行
$sql = 'INSERT INTO comment(id, nickname, comment, created_at, kadai_id, user_id) VALUES(NULL, :nickname, :comment, now(), :kadai_id, :user_id)';
$stmt = $pdo->prepare($sql);
try { 
  // SQLインジェクション対策　方法① 連想配列で渡す
  $stmt->execute([
    ':nickname' => $nickname,
    ':comment' => $comment,
    ':kadai_id' => $kadai_id,
    ':user_id' => $user_id
  ]);
}catch(PDOException $e) {
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}

// 掲示板画面へリダイレクト
header("Location: kadai.php?kadai_id=" . $kadai_id); // phpの文字列結合はドット
exit();
