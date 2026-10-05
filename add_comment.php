<?php
require 'auth.php';
require_auth();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }
verify_csrf();

$postId  = filter_var($_POST['post_id'] ?? '', FILTER_VALIDATE_INT);
$content = trim($_POST['content'] ?? '');

if (!$postId) { flash('error', 'Invalid post.'); header('Location: index.php'); exit; }
if ($content === '' || mb_strlen($content) > 1000) {
    flash('error', 'Comment must be 1-1000 characters.'); header('Location: index.php'); exit;
}

$stmt = $pdo->prepare('SELECT id FROM posts WHERE id = ?');
$stmt->execute([$postId]);
if (!$stmt->fetch()) { flash('error', 'Post not found.'); header('Location: index.php'); exit; }

$stmt = $pdo->prepare('INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)');
$stmt->execute([$postId, $_SESSION['user_id'], $content]);
flash('success', 'Comment added.');
header('Location: index.php'); exit;
