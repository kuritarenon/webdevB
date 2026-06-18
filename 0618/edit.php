<?php
require_once __DIR__ . '/inc/functions.php';
if (empty($_GET['id'])) {
  include __DIR__ . '/inc/header.php';
  echo '<div class="message-card error">';
  echo '<div class="icon">⚠</div>';
  echo '<p class="msg">idを指定してください。</p>';
  echo '<p class="back-link"><a href="index.php">リストへ戻る</a></p>';
  echo '</div>';
  include __DIR__ . '/inc/fotter.php';
  exit;
}
if (!preg_match('/\A\d{1,11}+\z/u', $_GET['id'])) {
  include __DIR__ . '/inc/header.php';
  echo '<div class="message-card error">';
  echo '<div class="icon">⚠</div>';
  echo '<p class="msg">idが正しくありません。</p>';
  echo '<p class="back-link"><a href="index.php">リストへ戻る</a></p>';
  echo '</div>';
  include __DIR__ . '/inc/fotter.php';
  exit;
}
$id = (int)$_GET['id'];

$dbh = db_open();
$sql = "SELECT id,title,isbn,price,publish,author FROM books WHERE id=:id";
$stml = $dbh->prepare($sql);
$stml->bindParam(":id", $id, PDO::PARAM_INT);
$stml->execute();
$result = $stml->fetch(PDO::FETCH_ASSOC);
if (!$result) {
  include __DIR__ . '/inc/header.php';
  echo '<div class="message-card error">';
  echo '<div class="icon">⚠</div>';
  echo '<p class="msg">指定したデータはありません。</p>';
  echo '<p class="back-link"><a href="index.php">リストへ戻る</a></p>';
  echo '</div>';
  include __DIR__ . '/inc/fotter.php';
  exit;
}

//取得したデータをフォームに配置
$title = str2html($result['title']);
$isbn = str2html($result['isbn']);
$price = str2html($result['price']);
$publish = str2html($result['publish']);
$author = str2html($result['author']);
$id = str2html($result['id']);

$html_form = <<<EOD
<form action="update.php" method="post">
  <p>
    <label for="title">書籍名</label>
    <input type="text" id="title" name="title" value="$title" required>
  </p>
  <p>
    <label for="isbn">ISBN</label>
    <input type="text" id="isbn" name="isbn" value="$isbn">
  </p>
  <p>
    <label for="price">価格</label>
    <input type="number" id="price" name="price" value="$price">
  </p>
  <p>
    <label for="publish">出版日</label>
    <input type="date" id="publish" name="publish" value="$publish">
  </p>
  <p>
    <label for="author">著者名</label>
    <input type="text" id="author" name="author" value="$author">
  </p>
  <p>
    <input type="hidden" name="id" value="$id">
  </p>
  <button type="submit">送信する</button>
</form>
EOD; ?>
<?php
include __DIR__ . '/inc/header.php';
echo $html_form; ?>
<p style="text-align: center; margin-top: 20px;"><a href="index.php">リストに戻る</a></p>

<?php
include __DIR__ . '/inc/fotter.php';
