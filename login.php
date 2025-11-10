<?php
// Inloggen (eenvoudig): gebruikersnaam of e-mail + wachtwoord
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/db.php'; // $conn
// schema is ensured in config/db.php

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($email === '' || $password === '') {
    $errors[] = 'Vul alle velden in.';
  }

  if (!$errors) {
    // Example users table: users(id, email, password_hash)
    try {
      // Ondersteun zowel e-mail als gebruikersnaam in hetzelfde veld
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare('SELECT id, username, password_hash, role FROM users WHERE email = :id LIMIT 1');
        $stmt->execute([':id' => $email]);
      } else {
        $stmt = $conn->prepare('SELECT id, username, password_hash, role FROM users WHERE username = :id LIMIT 1');
        $stmt->execute([':id' => $email]);
      }
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      if (!$user || !password_verify($password, $user['password_hash'])) {
        $errors[] = 'Onjuist e-mailadres of wachtwoord.';
      } else {
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['role'] = $user['role'] ?? 'user';
        $_SESSION['username'] = $user['username'] ?? '';
        header('Location: /activities/index.php');
        exit;
      }
    } catch (Throwable $e) {
      $errors[] = 'Databasefout.';
    }
  }
}
?>
<!doctype html>
<html lang="nl" data-bs-theme="light">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inloggen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>
    <?php include __DIR__ . '/../nav.php'; ?>
    <main class="py-5">
      <div class="container" style="max-width:420px;">
  <h1 class="h3 mb-4 text-center">Inloggen</h1>
        <?php if ($errors): ?>
          <div class="alert alert-danger small" role="alert">
            <?php foreach ($errors as $e) { echo htmlspecialchars($e) . '<br>'; } ?>
          </div>
        <?php endif; ?>
        <form method="post" novalidate class="border rounded p-4 bg-light">
          <div class="mb-3">
            <label for="email" class="form-label">E-mailadres</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Wachtwoord</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Inloggen</button>
        </form>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
