<?php
// Session + helper functions shared by all pages.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function is_logged_in() { return isset($_SESSION['user_id']); }

// AUTHENTICATED pages: redirect guests to login
function require_auth() {
    if (!is_logged_in()) { header('Location: login.php'); exit; }
}

// GUEST pages: redirect logged-in users to home
function require_guest() {
    if (is_logged_in()) { header('Location: index.php'); exit; }
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function verify_csrf() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403); exit('Invalid CSRF token.');
    }
}

function flash($type, $msg) { $_SESSION['flash'] = [$type, $msg]; }
function show_flash() {
    if (!empty($_SESSION['flash'])) {
        [$t, $m] = $_SESSION['flash']; unset($_SESSION['flash']);
        echo '<div class="alert ' . e($t) . '">' . e($m) . '</div>';
    }
}

function fmt_date($d) { return date('M j, Y g:i A', strtotime($d)); }
