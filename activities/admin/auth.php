<?php
// Session and auth helpers
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user_id(): ?int {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

function current_user_role(): string {
    return $_SESSION['role'] ?? 'guest';
}

function is_logged_in(): bool {
    return current_user_id() !== null;
}

function is_admin(): bool {
    return (current_user_role() === 'admin');
}

function require_login(): void {
    if (!is_logged_in()) {
        header('Location: /activities/admin/login.php');
        exit;
    }
}

function require_admin(): void {
    if (!is_admin()) {
        http_response_code(403);
        echo 'Toegang geweigerd';
        exit;
    }
}

// CSRF helper (basic)
function ensure_csrf_token(): void {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
}
function check_csrf_token(string $token): bool {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}
