<?php
// var_dump($_GET);
// exit();

// GETパラメータのチェック
if(!isset($_GET['kadai_id']) || !is_numeric($_GET['kadai_id'])){
  exit('ParamError');
}
$kadai_id = (int)$_GET['kadai_id']; // int型にキャスト

// DB接続
include('db_connect.php');
$pdo = db_connect();

// 課題情報取得
$sql = 'SELECT * FROM kadai WHERE id = :kadai_id'; // プレースホルダ
$stmt = $pdo->prepare($sql);
try {
  // SQLインジェクション対策　方法① 連想配列で渡す
  $stmt->execute([':kadai_id' => $kadai_id]);
}catch(PDOException $e) {
  // 連想配列（PHP）→JSON文字列
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
// SQLインジェクション対策　方法② vindValue()
// $stmt->bindValue(':kadai_id', $kadai_id, PDO::PARAM_INT);
// $stmt->execute();

// idが一致する課題を取得（1件のみなのでfetch）
$kadai = $stmt->fetch(PDO::FETCH_ASSOC);
if(!$kadai) {
  exit('課題が見つかりません');
}

// コメント一覧取得（投稿日降順）
$sql = 'SELECT nickname, comment, created_at FROM comment WHERE kadai_id=:kadai_id ORDER BY created_at DESC';
$stmt = $pdo->prepare($sql);

// SQLインジェクション対策　方法② vindValue()
$stmt->bindValue(':kadai_id', $kadai_id, PDO::PARAM_INT);
try {
  $stmt->execute();
}catch(PDOException $e) {
  // 連想配列（PHP）→JSON文字列
  echo json_encode(["sql error" => "{$e->getMessage()}"]);
  exit();
}
$comments = $stmt->fetchAll(PDO::FETCH_ASSOC); // カラム名をキーとした連想配列
?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>「<?= htmlspecialchars($kadai['title']) ?>」の掲示板</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <p><a href="developer.php">課題一覧に戻る</a></p>
  <h1>「<?= htmlspecialchars($kadai['title']) ?>」の掲示板</h1>

  <!-- 課題情報 -->
  <div id="info_kadai">
    <p><strong>課題タイトル：</strong><?= htmlspecialchars($kadai['title']) ?></p>
    <p>
      <strong>Repository URL：</strong>
      <a href="<?= htmlspecialchars($kadai['repositoryUrl']) ?>" target='_blank' rel="noopener norefeerer">
        <?= htmlspecialchars($kadai['repositoryUrl']) ?>
      </a>
    </p>
    <p>
      <strong>Deploy URL：</strong>
      <a href="<?= htmlspecialchars($kadai['deployUrl']) ?>" target='_blank' rel="noopener norefeerer">
        <?= htmlspecialchars($kadai['deployUrl']) ?>
      </a>
    </p>
    <!-- メモ -->
    <!-- target="_blank" : リンクを新しいタブで開く指定。 -->
    <!-- rel="noopener noreferrer" : 新しいタブに開かれたページが元のページの情報にアクセスできない＆新しいページにどこからきたかを伝えない -->
    <!-- target="_blank" だけだと、リンク先ページがJSで元ページを操作でき、フィッシング詐欺や書き換え攻撃のリスク -->
  </div>


  <!-- コメント投稿フォーム -->
  <div id="post_comment">    
    <h2>コメント投稿</h2>
    <form action="comment_create.php" method="POST">
      <div>
        <label for="nickname">ニックネーム：</label>
        <input type="text" id="nickname" name="nickname" required>
      </div>
      <div>
        <label for="comment">コメント：</label>
        <textarea id="comment" name="comment" rows="4" cols="40" required></textarea>
      </div>
      <!-- kadai_idを非表示で送信 -->
      <input type="hidden" name="kadai_id" value="<?= htmlspecialchars($kadai_id) ?>">
      <button>投稿する</button>
    </form>
  </div>

  <!-- コメント一覧表示 -->
  <div id="display_comment">
    <h2>コメント一覧</h2>
    <?php if(count($comments) === 0): ?>
      <p>まだコメントはありません</p>
    <?php else: ?>
      <?php foreach($comments as $comment): ?>
        <div class='post'>
          <!-- htmlspecialchars():XSS対策 -->
          <strong><?= htmlspecialchars($comment['nickname']) ?></strong>
          (<?= htmlspecialchars($comment['created_at'])?>)<br>
          <?= nl2br(htmlspecialchars($comment['comment'])) ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>