<?php
// register.php
session_start();
require_once __DIR__ . '/inc/functions.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'ユーザー名とパスワードを入力してください。';
    } else {
        // アイコンアップロード処理
        $iconPath = null;
        if (!empty($_FILES['icon']['tmp_name']) && $_FILES['icon']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $mime = mime_content_type($_FILES['icon']['tmp_name']);
            if (!isset($allowed[$mime])) {
                $error = 'アイコン画像は jpg / png / webp のみアップロードできます。';
            } else {
                $ext = $allowed[$mime];
                $filename = uniqid('icon_', true) . '.' . $ext;
                $destDir = __DIR__ . '/uploads/icons/';
                if (!is_dir($destDir)) {
                    mkdir($destDir, 0755, true);
                }
                if (move_uploaded_file($_FILES['icon']['tmp_name'], $destDir . $filename)) {
                    $iconPath = 'uploads/icons/' . $filename;
                } else {
                    $error = 'アイコンの保存に失敗しました。';
                }
            }
        }

        if ($error === '') {
            $color = ICON_PALETTE[array_rand(ICON_PALETTE)];
            $hash  = password_hash($password, PASSWORD_DEFAULT);
            try {
                $dbh = db_open();
                $sql = 'INSERT INTO users (username, password, icon_path, color) VALUES (:u, :p, :i, :c)';
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(':u', $username);
                $stmt->bindValue(':p', $hash);
                $stmt->bindValue(':i', $iconPath);
                $stmt->bindValue(':c', $color);
                $stmt->execute();
                header('Location: login.php');
                exit;
            } catch (PDOException $e) {
                $error = '登録に失敗しました。';
            }
        }
    }
}

include __DIR__ . '/inc/header.php';
?>
<h2>ユーザー登録</h2>
<?php if ($error): ?><p class="error"><?= str2html($error) ?></p><?php endif; ?>
<form method="post" action="register.php" enctype="multipart/form-data">
    <p><label>ユーザー名 <input type="text" name="username" required></label></p>
    <p><label>パスワード <input type="password" name="password" required></label></p>
    <p><label>アイコン画像（任意）<input type="file" name="icon" accept="image/jpeg,image/png,image/webp"></label></p>
    <button type="submit">登録する</button>
</form>
<?php include __DIR__ . '/inc/footer.php'; ?>