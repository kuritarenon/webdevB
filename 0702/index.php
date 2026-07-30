<?php
require_once __DIR__ . '/login_check.php';
require_once __DIR__ . '/inc/functions.php';
include __DIR__ . '/inc/header.php';
try {
    $dbh = db_open();
    $sql = 'SELECT * FROM books';
    $statment = $dbh->query($sql);
?>
    <div class="table-container">
        <table>
            <tr>
                <th>更新</th>
                <th>書籍名</th>
                <th>ISBN</th>
                <th>価格</th>
                <th>出版日</th>
                <th>著者名</th>
            </tr>
            <?php while ($row = $statment->fetch()): ?>
                <tr>
                    <td>
                        <a href="edit.php?id=<?php echo (int) $row['id']; ?>">更新</a>
                    </td>
                    <td><?php echo str2html($row['title']); ?></td>
                    <td><?php echo str2html($row['isbn']); ?></td>
                    <td><?php echo str2html($row['price']); ?></td>
                    <td><?php echo str2html($row['publish']); ?></td>
                    <td><?php echo str2html($row['author']); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
<?php
} catch (PDOException $e) {
    echo '<div class="message-card error">';
    echo '<div class="icon">⚠</div>';
    echo '<p class="msg">エラーが発生しました: ' . str2html($e->getMessage()) . '</p>';
    echo '</div>';
    exit;
}
include __DIR__ . '/inc/fotter.php';
?>