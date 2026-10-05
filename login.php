<?php
require 'auth.php';
require_guest();
require 'db.php';

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
    if ($password === '') $errors[] = 'Password is required.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id, name, password FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = (int)$user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: index.php'); exit;
        }
        $errors[] = 'Invalid email or password.';
    }
}

$pageTitle = 'Login';
require 'header.php';
?>
<div class="card narrow">
  <h2>Log in</h2>
  <?php if ($errors): ?><div class="errors"><ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="post">
    <?= csrf_field() ?>
    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= e($email) ?>" required maxlength="100">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required>
    <p><button type="submit">Login</button></p>
  </form>
  <p class="meta">No account yet? <a href="register.php">Register</a></p>
</div>
<?php require 'footer.php'; ?>
