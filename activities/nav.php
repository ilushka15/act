<?php
// Bootstrap Album-style header (Dutch)
// Use absolute paths so links work from any subdirectory
?>
<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$isLogged = isset($_SESSION['user_id']);
$role = $_SESSION['role'] ?? 'guest';
$uname = $_SESSION['username'] ?? '';
?>
<style>
	.auth-box { background: #1f2937; border:1px solid #374151; border-radius: .65rem; padding: .4rem .85rem; display:flex; align-items:center; gap:.6rem; }
	.auth-box .divider { width:1px; height:20px; background:#374151; }
	.auth-box a { text-decoration:none; font-size:.85rem; }
	.auth-box .uname { font-size:.75rem; opacity:.85; }
	@media (max-width: 768px){
		.auth-box { flex-wrap:wrap; justify-content:flex-start; }
		.auth-box .divider { display:none; }
	}
</style>
<header data-bs-theme="dark">
	<div class="navbar navbar-dark bg-dark shadow-sm">
		<div class="container">
			<a href="/activities/index.php" class="navbar-brand d-flex align-items-center">
				<strong>Activiteiten</strong>
			</a>
			<div class="d-flex gap-3 align-items-center">
				<a class="nav-link text-white" href="/activities/index.php">Home</a>
				<a class="nav-link text-white" href="/activities/content/activiteiten.php">Activiteiten</a>
				<a class="nav-link text-white" href="/activities/content/score.php">Score</a>
				<a class="nav-link text-white" href="/activities/content/reacties.php">Reacties</a>
				<?php if ($role === 'admin'): ?>
					<a class="nav-link text-warning" href="/activities/admin/users.php">Beheer</a>
				<?php endif; ?>
				<div class="auth-box">
					<?php if ($isLogged): ?>
						<span class="uname text-white"><?= htmlspecialchars($uname) ?> (<?= htmlspecialchars($role) ?>)</span>
						<span class="divider"></span>
						<a class="text-white" href="/activities/admin/logout.php">Uitloggen</a>
					<?php else: ?>
						<a class="text-white" href="/activities/admin/login.php">Inloggen</a>
						<span class="divider"></span>
						<a class="text-white" href="/activities/admin/register.php">Registreren</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</header>

