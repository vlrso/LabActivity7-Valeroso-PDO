<?php
require 'auth.php';
require_auth();
require 'db.php';

$id = filter_var($_GET['id'] ?? $_POST['id'] ?? '', FILTER_VALIDATE_INT);
$stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ? AND user_id = ?'); // ownership enforced
$stmt->execute([$id, $_SESSION['user_id']]);
$post = $stmt->fetch();
if (!$post) { flash('error', 'Post not found or you are not allowed to edit it.'); header('Location: index.php'); exit; }

$errors = [];
$title = $post['title'];
$content = $post['content'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (mb_strlen($title) < 3 || mb_strlen($title) > 150) $errors[] = 'Title must be 3-150 characters.';
    if (mb_strlen($content) < 5 || mb_strlen($content) > 5000) $errors[] = 'Content must be 5-5000 characters.';

    if (!$errors) {
        if ($title !== $post['title'] || $content !== $post['content']) {
            $stmt = $pdo->prepare('UPDATE posts SET title = ?, content = ?, updated_at = NOW() WHERE id = ? AND user_id = ?');
            $stmt->execute([$title, $content, $id, $_SESSION['user_id']]);
            flash('success', 'Post updated.');
        }
        header('Location: index.php'); exit;
    }
}

$pageTitle = 'Edit Post';
require 'header.php';
?>
<div class="card">
  <h3>Edit post</h3>
  <?php if ($errors): ?><div class="errors"><ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$id ?>">
    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="<?= e($title) ?>" required minlength="3" maxlength="150">
    <label for="content">Content</label>
    <textarea id="content" name="content" required minlength="5" maxlength="5000"><?= e($content) ?></textarea>
    <p><button type="submit">Save changes</button> <a class="btn secondary" href="index.php">Cancel</a></p>
  </form>
</div>
<?php require 'footer.php'; ?>
