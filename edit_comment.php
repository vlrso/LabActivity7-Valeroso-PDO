<?php
require 'auth.php';
require_auth();
require 'db.php';

$id = filter_var($_GET['id'] ?? $_POST['id'] ?? '', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM comments WHERE id = ? AND user_id = ?'); // ownership enforced
$stmt->execute([$id, $_SESSION['user_id']]);
$comment = $stmt->fetch();
if (!$comment) { flash('error', 'Comment not found or you are not allowed to edit it.'); header('Location: index.php'); exit; }

$errors = [];
$content = $comment['content'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $content = trim($_POST['content'] ?? '');
    if ($content === '' || mb_strlen($content) > 1000) $errors[] = 'Comment must be 1-1000 characters.';

    if (!$errors) {
        if ($content !== $comment['content']) {
            $stmt = $pdo->prepare('UPDATE comments SET content = ?, updated_at = NOW() WHERE id = ? AND user_id = ?');
            $stmt->execute([$content, $id, $_SESSION['user_id']]);
            flash('success', 'Comment updated.');
        }
        header('Location: index.php'); exit;
    }
}

$pageTitle = 'Edit Comment';
require 'header.php';
?>
<div class="card">
  <h3>Edit comment</h3>
  <?php if ($errors): ?><div class="errors"><ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$id ?>">
    <label for="content">Comment</label>
    <textarea id="content" name="content" required maxlength="1000"><?= e($content) ?></textarea>
    <p><button type="submit">Save changes</button> <a class="btn secondary" href="index.php">Cancel</a></p>
  </form>
</div>
<?php require 'footer.php'; ?>
