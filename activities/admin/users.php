<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/db.php'; // schema auto ensured
require_admin();

// Role update handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = (int)($_POST['uid'] ?? 0);
    $role = $_POST['role'] ?? 'user';
    if (!in_array($role, ['user','admin'], true)) { $role = 'user'; }
    $stmt = $conn->prepare('UPDATE users SET role = :role WHERE id = :id');
    $stmt->execute([':role' => $role, ':id' => $uid]);
    header('Location: users.php');
    exit;
}

$users = $conn->query('SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="nl" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Gebruikersbeheer</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<?php include __DIR__ . '/../nav.php'; ?>
<main class="py-5">
  <div class="container">
    <h1 class="h4 mb-4">Gebruikersbeheer</h1>
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Gebruikersnaam</th>
            <th>E-mail</th>
            <th>Rol</th>
            <th>Aangemaakt</th>
            <th>Actie</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><?= (int)$u['id'] ?></td>
            <td><?= htmlspecialchars($u['username']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><span class="badge bg-<?php echo $u['role']==='admin'?'warning text-dark':'secondary'; ?>"><?= htmlspecialchars($u['role']) ?></span></td>
            <td class="small text-muted"><?= htmlspecialchars($u['created_at']) ?></td>
            <td>
              <form method="post" class="d-inline-flex gap-2">
                <input type="hidden" name="uid" value="<?= (int)$u['id'] ?>">
                <select name="role" class="form-select form-select-sm" style="width:120px">
                  <option value="user" <?= $u['role']==='user'?'selected':''; ?>>user</option>
                  <option value="admin" <?= $u['role']==='admin'?'selected':''; ?>>admin</option>
                </select>
                <button class="btn btn-sm btn-primary" type="submit">Opslaan</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>