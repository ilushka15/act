<?php
// Registratie (eenvoudig): gebruikersnaam + (opt.) e-mail + wachtwoord
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/db.php';
// schema is ensured in config/db.php

$errors = [];
// no need for a separate success flag

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');
  $confirm = trim($_POST['confirm'] ?? '');

  if ($username === '' || $password === '' || $confirm === '') {
    $errors[] = 'Vul alle verplichte velden in.';
  } elseif (!preg_match('/^[A-Za-z0-9_.-]{3,32}$/', $username)) {
    $errors[] = 'Gebruikersnaam: 3-32 tekens, letters/cijfers/_ . -';
  } elseif ($password !== $confirm) {
    $errors[] = 'Wachtwoorden komen niet overeen.';
  } elseif (strlen($password) < 6) {
    $errors[] = 'Wachtwoord moet minstens 6 tekens lang zijn.';
  }
  if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Ongeldig e-mailadres.';
  }

  if (!$errors) {
    try {
      // Check unieke gebruikersnaam
      $checkU = $conn->prepare('SELECT id FROM users WHERE username = :u LIMIT 1');
      $checkU->execute([':u' => $username]);
      if ($checkU->fetch()) {
        $errors[] = 'Gebruikersnaam bestaat al.';
      }

      // Check unieke e-mail als ingevuld
      if ($email !== '') {
        $checkE = $conn->prepare('SELECT id FROM users WHERE email = :e LIMIT 1');
        $checkE->execute([':e' => $email]);
        if ($checkE->fetch()) {
          $errors[] = 'E-mailadres bestaat al.';
        }
      }

      if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($email !== '') {
          $ins = $conn->prepare('INSERT INTO users(username, email, password_hash, role) VALUES(:u, :e, :h, :r)');
          $ins->execute([':u' => $username, ':e' => $email, ':h' => $hash, ':r' => 'user']);
        } else {
          $ins = $conn->prepare('INSERT INTO users(username, password_hash, role) VALUES(:u, :h, :r)');
          $ins->execute([':u' => $username, ':h' => $hash, ':r' => 'user']);
        }
        $uid = (int)$conn->lastInsertId();

        // Eerste gebruiker wordt admin als er nog geen admin is
        $cnt = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='admin'")->fetch();
        $adminCount = (int)($cnt['c'] ?? 0);
        $role = 'user';
        if ($adminCount === 0) {
          $upd = $conn->prepare('UPDATE users SET role=\'admin\' WHERE id=:id');
          $upd->execute([':id' => $uid]);
          $role = 'admin';
        }

        $_SESSION['user_id'] = $uid;
        $_SESSION['role'] = $role;
        $_SESSION['username'] = $username;
        $success = true;
        header('Location: /activities/index.php');
        exit;
      }
    } catch (Throwable $e) {
      $errors[] = 'Databasefout: ' . htmlspecialchars($e->getMessage());
    }
  }
}
?>
<!doctype html>
<html lang="nl" data-bs-theme="light">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registreren</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>
    <?php include __DIR__ . '/../nav.php'; ?>
    <main class="py-5">
      <div class="container" style="max-width:460px;">
        <h1 class="h3 mb-4 text-center">Account aanmaken</h1>
        <?php if ($errors): ?>
          <div class="alert alert-danger small" role="alert">
            <?php foreach ($errors as $e) { echo htmlspecialchars($e) . '<br>'; } ?>
          </div>
        <?php endif; ?>
        <form method="post" novalidate class="border rounded p-4 bg-light">
          <div class="mb-3">
            <label for="username" class="form-label">Gebruikersnaam</label>
            <input type="text" class="form-control" id="username" name="username" required>
            <div class="form-text">3-32 tekens, letters/cijfers/_ . -</div>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">E-mailadres (optioneel)</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="naam@example.com">
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Wachtwoord</label>
            <input type="password" class="form-control" id="password" name="password" minlength="6" required>
          </div>
          <div class="mb-3">
            <label for="confirm" class="form-label">Bevestig wachtwoord</label>
            <input type="password" class="form-control" id="confirm" name="confirm" minlength="6" required>
          </div>
          <button type="submit" class="btn btn-primary w-100">Registreren</button>
          <div class="text-center mt-3 small">
            Al een account? <a href="/activities/admin/login.php">Inloggen</a>
          </div>
        </form>
      </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
