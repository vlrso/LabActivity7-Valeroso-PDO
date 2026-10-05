<?php
require 'auth.php';
require_auth();
require 'db.php';

$errors = [];
$title = $content = '';

// Add new post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    if (mb_strlen($title) < 3 || mb_strlen($title) > 150) $errors[] = 'Title must be 3-150 characters.';
    if (mb_strlen($content) < 5 || mb_strlen($content) > 5000) $errors[] = 'Content must be 5-5000 characters.';

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)');
        $stmt->execute([$_SESSION['user_id'], $title, $content]);
        flash('success', 'Post published!');
        header('Location: index.php'); exit;
    }
}

// News feed: most recent first
$posts = $pdo->query(
    'SELECT p.*, u.name AS author FROM posts p JOIN users u ON u.id = p.user_id
     ORDER BY p.created_at DESC, p.id DESC'
)->fetchAll();

// Load all comments in one query, grouped by post
$comments = [];
if ($posts) {
    $rows = $pdo->query(
        'SELECT c.*, u.name AS author FROM comments c JOIN users u ON u.id = c.user_id
         ORDER BY c.created_at ASC, c.id ASC'
    )->fetchAll();
    foreach ($rows as $r) $comments[$r['post_id']][] = $r;
}

$me = $_SESSION['user_id'];
$pageTitle = 'News Feed';
require 'header.php';
?>
<div class="card">
  <h3>What's on your mind?</h3>
  <?php if ($errors): ?><div class="errors"><ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <label for="title">Title</label>
    <input type="text" id="title" name="title" value="<?= e($title) ?>" required minlength="3" maxlength="150">
    <label for="content">Content</label>
    <textarea id="content" name="content" required minlength="5" maxlength="5000"><?= e($content) ?></textarea>
    <p><button type="submit">Post</button></p>
  </form>
</div>

<?php if (!$posts): ?>
  <div class="card meta">No posts yet. Be the first to post!</div>
<?php endif; ?>

<?php foreach ($posts as $p): ?>
<div class="card">
  <h3 class="post-title"><?= e($p['title']) ?></h3>
  <div class="meta">
    by <strong><?= e($p['author']) ?></strong> · <?= e(fmt_date($p['created_at'])) ?>
    <?php if ($p['updated_at']): ?><span class="tag" title="<?= e(fmt_date($p['updated_at'])) ?>">edited</span><?php endif; ?>
  </div>
  <div class="content"><?= e($p['content']) ?></div>

  <?php if ((int)$p['user_id'] === $me): ?>
  <div class="actions">
    <a class="btn secondary" href="edit_post.php?id=<?= (int)$p['id'] ?>">Edit</a>
    <form method="post" action="delete_post.php" onsubmit="return confirm('Delete this post and all its comments?')">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
      <button class="danger" type="submit">Delete</button>
    </form>
  </div>
  <?php endif; ?>

  <div class="comments">
    <strong><?= count($comments[$p['id']] ?? []) ?> comment(s)</strong>
    <?php foreach ($comments[$p['id']] ?? [] as $c): ?>
      <div class="comment">
        <div class="meta">
          <strong><?= e($c['author']) ?></strong> · <?= e(fmt_date($c['created_at'])) ?>
          <?php if ($c['updated_at']): ?><span class="tag" title="<?= e(fmt_date($c['updated_at'])) ?>">edited</span><?php endif; ?>
        </div>
        <div class="content"><?= e($c['content']) ?></div>
        <?php if ((int)$c['user_id'] === $me): ?>
        <div class="actions">
          <a class="btn secondary" href="edit_comment.php?id=<?= (int)$c['id'] ?>">Edit</a>
          <form method="post" action="delete_comment.php" onsubmit="return confirm('Delete this comment?')">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
            <button class="danger" type="submit">Delete</button>
          </form>
        </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

    <form class="comment-form" method="post" action="add_comment.php">
      <?= csrf_field() ?><input type="hidden" name="post_id" value="<?= (int)$p['id'] ?>">
      <input type="text" name="content" placeholder="Write a comment..." required minlength="1" maxlength="1000">
      <button type="submit">Comment</button>
    </form>
  </div>
</div>
<?php endforeach; ?>
<?php require 'footer.php'; ?>
