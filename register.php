<?php
require 'auth.php';
require_guest();
require 'db.php';

$errors = [];
$name = $email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($name === '' || mb_strlen($name) < 2 || mb_strlen($name) > 50) $errors[] = 'Name must be 2-50 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) $errors[] = 'Enter a valid email (max 100 characters).';
    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password))
        $errors[] = 'Password must be at least 8 characters and contain a letter and a number.';
    if ($password !== $confirm) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'That email is already registered.';
        } else {
            try {
                $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
                $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
                flash('success', 'Account created! Please log in.');
                header('Location: login.php'); exit;
            } catch (PDOException $e) {
                $errors[] = ($e->getCode() == 23000) ? 'That email is already registered.' : 'Something went wrong. Try again.';
            }
        }
    }
}

$pageTitle = 'Register';
require 'header.php';
?>
<div class="card narrow">
  <h2>Create an account</h2>
  <?php if ($errors): ?><div class="errors"><ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="post" id="regForm">
    <?= csrf_field() ?>
    <label for="name">Name</label>
    <input type="text" id="name" name="name" value="<?= e($name) ?>" required minlength="2" maxlength="50">
    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= e($email) ?>" required maxlength="100">
    <label for="password">Password</label>
    <input type="password" id="password" name="password" required minlength="8"
           pattern="(?=.*[A-Za-z])(?=.*\d).{8,}" title="At least 8 characters with a letter and a number">
    <label for="confirm_password">Confirm password</label>
    <input type="password" id="confirm_password" name="confirm_password" required minlength="8">
    <p><button type="submit">Register</button></p>
  </form>
  <p class="meta">Already have an account? <a href="login.php">Log in</a></p>
</div>
<script>
const pw = document.getElementById('password'), cpw = document.getElementById('confirm_password');
function check(){ cpw.setCustomValidity(pw.value !== cpw.value ? 'Passwords do not match' : ''); }
pw.addEventListener('input', check); cpw.addEventListener('input', check);
</script>
<?php require 'footer.php'; ?>
