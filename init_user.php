<?php
function initUser(PDO $pdo){
  // セッション開始（$_SESSIONにアクセスするために必要）
  session_start();

  if(!isset($_SESSION['user_id'])){
    $pdo = db_connect();
  
    // UUID生成
    // ランダムな16byteのバイナリ数->16進数で表した32桁の文字列
    $uuid = bin2hex(random_bytes(16));
  
    // DBに新規userとして登録
    $sql = 'INSERT INTO user (uuid) VALUES (:uuid)';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':uuid', $uuid, PDO::PARAM_STR);
    $stmt->execute();
  
    $_SESSION['user_id'] = $pdo->lastInsertId();
  }
  return $_SESSION['user_id']; // stringで返されているっぽい
}

