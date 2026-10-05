<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? 'Blog Site') ?></title>
<style>
*{box-sizing:border-box}
body{font-family:system-ui,sans-serif;background:#f3f4f6;margin:0;color:#1f2937}
nav{background:#1f2937;color:#fff;padding:12px 24px;display:flex;justify-content:space-between;align-items:center}
nav a{color:#fff;text-decoration:none;margin-left:16px}
nav .brand{font-weight:700;margin:0}
.container{max-width:720px;margin:24px auto;padding:0 16px}
.card{background:#fff;border-radius:8px;padding:20px;margin-bottom:16px;box-shadow:0 1px 3px rgba(0,0,0,.1)}
label{display:block;font-weight:600;margin:12px 0 4px}
input[type=text],input[type=email],input[type=password],textarea{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px;font:inherit}
textarea{min-height:90px;resize:vertical}
button,.btn{background:#2563eb;color:#fff;border:0;padding:8px 16px;border-radius:6px;cursor:pointer;font:inherit;text-decoration:none;display:inline-block}
button.danger{background:#dc2626}
.btn.secondary{background:#6b7280}
.alert{padding:10px 14px;border-radius:6px;margin-bottom:16px}
.alert.success{background:#d1fae5;color:#065f46}
.alert.error{background:#fee2e2;color:#991b1b}
.errors{background:#fee2e2;color:#991b1b;padding:10px 14px;border-radius:6px;margin-bottom:12px}
.errors ul{margin:0;padding-left:18px}
.meta{font-size:.85rem;color:#6b7280}
.tag{background:#fef3c7;color:#92400e;font-size:.75rem;padding:1px 8px;border-radius:999px;margin-left:6px}
.post-title{margin:0 0 4px}
.content{white-space:pre-wrap;margin:10px 0}
.actions{display:flex;gap:8px;align-items:center}
.actions form{display:inline}
.actions button,.actions .btn{padding:4px 10px;font-size:.85rem}
.comments{border-top:1px solid #e5e7eb;margin-top:14px;padding-top:10px}
.comment{background:#f9fafb;border-radius:6px;padding:10px;margin-bottom:8px}
.comment-form{display:flex;gap:8px;margin-top:10px}
.comment-form input{flex:1}
.narrow{max-width:420px;margin:48px auto}
</style>
</head>
<body>
<nav>
  <a class="brand" href="index.php">📝 Blog Site</a>
  <div>
  <?php if (is_logged_in()): ?>
    <span>Hi, <?= e($_SESSION['user_name']) ?></span>
    <a href="logout.php">Logout</a>
  <?php else: ?>
    <a href="login.php">Login</a><a href="register.php">Register</a>
  <?php endif; ?>
  </div>
</nav>
<div class="container">
<?php show_flash(); ?>
