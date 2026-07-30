<?php
session_start();
require_once __DIR__ . '/inc/functions.php';

$error = '';

if (!empty($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['username']) || empty($_POST['password'])) {
        unset($_SESSION['login']);
        $error = 'ユーザー名とパスワードを入力してください。';
    } else {
        try {
            $dbh = db_open();
            $sql = 'SELECT password FROM users WHERE username = :username';
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':username', $_POST['username'], PDO::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                unset($_SESSION['login']);
                $error = 'ログインに失敗しました。';
            } elseif (password_verify($_POST['password'], $result['password'])) {
                session_regenerate_id(true);
                $_SESSION['login'] = true;
                // echo 'SESSION_ID=' . session_id();
                // echo '<br>LOGIN=' . (int) !empty($_SESSION['login']);
                header('Location: index.php');
                exit;
            } else {
                unset($_SESSION['login']);
                $error = 'ログインに失敗しました。';
            }
        } catch (PDOException $e) {
            unset($_SESSION['login']);
            $error = 'エラー: ' . str2html($e->getMessage());
        }
    }
}

include __DIR__ . '/inc/header.php';
?>
<form method="post" action="login.php">
    <p>
        <label for="username">ユーザー名:</label>
        <input type="text" id="username" name="username" required>
    </p>
    <p>
        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required>
    </p>
    <?php if ($error !== ''): ?>
        <p><?php echo str2html($error); ?></p>
    <?php endif; ?>
    <input type="submit" value="ログイン">
</form>

<?php include __DIR__ . '/inc/footer.php';
