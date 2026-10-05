<?php
require 'auth.php';
require_auth();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    if ($id) {
        // Comments are removed automatically via ON DELETE CASCADE
        $stmt = $pdo->prepare('DELETE FROM posts WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $_SESSION['user_id']]);
        flash($stmt->rowCount() ? 'success' : 'error', $stmt->rowCount() ? 'Post deleted.' : 'You cannot delete this post.');
    }
}
header('Location: index.php'); exit;
