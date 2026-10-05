<?php
require 'auth.php';
require_auth();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = $pdo->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $_SESSION['user_id']]);
        flash($stmt->rowCount() ? 'success' : 'error', $stmt->rowCount() ? 'Comment deleted.' : 'You cannot delete this comment.');
    }
}
header('Location: index.php'); exit;
