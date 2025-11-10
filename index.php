<!doctype html>
<html lang="nl" data-bs-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Activiteiten</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
        <body>
            <?php
                // Haal de laatste 4 activiteiten op (stil de output van db.php)
                $laatste = [];
                try {
                    ob_start();
                    require __DIR__ . '/config/db.php';
                    ob_end_clean();
                    if (isset($conn)) {
                        $stmt = $conn->query("SELECT id, title, description, created_at FROM activities ORDER BY created_at DESC LIMIT 4");
                        if ($stmt) { $laatste = $stmt->fetchAll(PDO::FETCH_ASSOC); }
                    }
                } catch (Throwable $e) {
                    $laatste = [];
                }
            ?>

            <?php include __DIR__ . '/nav.php'; ?>
            <main class="pt-0">
                <div class="container">
                </div>
                <!-- Fullscreen hero (minus navbar height) -->
                <section class="hero-dark" style="min-height: calc(50vh - 56px); display:flex; align-items:center; background:linear-gradient(135deg,#e2e6ea,#cfd4da);">
                    <div class="container py-5 text-center">
                        <div class="col-lg-8 px-0 mx-auto">
                            <h1 class="display-4 fw-bold mb-3 text-dark ">Welkom bij Activiteiten</h1>
                            <p class="lead mb-3 text-dark">
                                Dit platform helpt je om activiteiten te registreren, te volgen en te delen met anderen. 
                                Bekijk prestaties in <strong>Score</strong>, laat feedback achter via <strong>Reacties</strong> 
                                en beheer alles eenvoudig in het <strong>beheerpaneel</strong>.
                            </p>
                            <p class="mb-0 text-dark-50">
                                De interface is gebouwd met Bootstrap 5 (Album-stijl) voor een consistente en snelle gebruikerservaring.
                            </p>
                        </div>
                    </div>
                </section>

                <div class="container">
                    <div class="d-flex align-items-center justify-content-between mb-3 mt-5">
                        <h2 class="h4 mb-0">Laatste activiteiten</h2>
                    </div>

                    <div class="row row-cols-1 row-cols-md-4 g-4">
                                    <?php if (!empty($laatste)) : ?>
                            <?php foreach ($laatste as $a): ?>
                                <div class="col">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body">
                                            <h3 class="h6 card-title mb-2"><?php echo htmlspecialchars($a['title'] ?? 'Activiteit'); ?></h3>
                                            <p class="card-text small text-body-secondary">
                                                <?php
                                                    $desc = trim((string)($a['description'] ?? ''));
                                                                if (!function_exists('__cut_text')) {
                                                                    function __cut_text(string $s, int $limit = 180): string {
                                                                        if (function_exists('mb_strimwidth')) {
                                                                            return mb_strimwidth($s, 0, $limit, '…', 'UTF-8');
                                                                        }
                                                                        return (strlen($s) > $limit) ? substr($s, 0, $limit - 2) . '…' : $s;
                                                                    }
                                                                }
                                                                if ($desc === '') { echo 'Geen beschrijving beschikbaar.'; }
                                                                else { echo htmlspecialchars(__cut_text($desc, 180)); }
                                                ?>
                                            </p>
                                        </div>
                                        <div class="card-footer bg-white small text-muted">
                                            <?php echo isset($a['created_at']) ? htmlspecialchars(date('d.m.Y H:i', strtotime($a['created_at']))) : '';
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                                <div class="col">
                                    <div class="card h-100 shadow-sm">
                                        <div class="card-body">
                                            <h3 class="h6 card-title mb-2">Nog geen activiteiten</h3>
                                            <p class="card-text small text-body-secondary">Zodra er activiteiten worden toegevoegd, verschijnen de vier meest recente hier.</p>
                                        </div>
                                    </div>
                                </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        </body>
</html>