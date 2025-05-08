<?php
// Configurazione del DB
$config = require '../connessione_db/db_config.php';
require '../connessione_db/DB_Connect.php';
require_once '../connessione_db/functions.php';

// Connessione al DB
$db = DataBase_Connect::getDB($config);

// Controllo che l'ID della visita sia presente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: servizi.php');
    exit;
}

$id_visita = (int)$_GET['id'];

// Ottieni i dettagli della visita
$query = "SELECT * FROM visite WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id_visita]);
$visita = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$visita) {
    header('Location: servizi.php');
    exit;
}

// Ottieni gli eventi futuri per questa visita
$query = "SELECT e.*, g.nome AS guida_nome, g.cognome AS guida_cognome,
          (SELECT SUM(ec.quantita) FROM elementi_carrello ec 
           JOIN carrelli c ON ec.id_carrello = c.id 
           WHERE ec.id_evento = e.id AND c.stato != 'annullato') AS posti_prenotati
          FROM eventi e
          JOIN guide g ON e.id_guida = g.id
          WHERE e.id_visita = ? AND e.data_evento >= CURDATE()
          ORDER BY e.data_evento, e.ora_inizio";
$stmt = $db->prepare($query);
$stmt->execute([$id_visita]);
$eventi = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($visita['titolo']) ?> - Artifex</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
<?php include '../strutture_pagina/navbar.php'; ?>

<!-- Dettaglio Visita Section -->
<section id="tour-detail" class="section">
    <div class="container">
        <div class="text-center mb-3">
            <a href="home.php" class="btn btn-outline">Home</a> &gt;
            <a href="servizi.php" class="btn btn-outline">Tour Guidati</a> &gt;
            <span><?= htmlspecialchars($visita['titolo']) ?></span>
        </div>

        <div class="tour-detail-wrapper" style="display: flex; gap: 2rem; flex-wrap: wrap;">
            <!-- Dettagli del Tour -->
            <div class="tour-detail-main" style="flex: 1; min-width: 300px;">
                <h1 class="section-title"><?= htmlspecialchars($visita['titolo']) ?></h1>
                <h3 class="mb-2"><?= htmlspecialchars($visita['luogo']) ?></h3>

                <div class="tour-info mb-2">
                    <span><i class="fas fa-clock"></i> Durata: <?= $visita['durata_media'] ?> minuti</span>
                </div>

                <div class="tour-card">
                    <div class="tour-content">
                        <h4>Descrizione</h4>
                        <p class="tour-description"><?= nl2br(htmlspecialchars($visita['descrizione'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- Eventi Programmati -->
            <div class="tour-events-sidebar" style="width: 350px;">
                <div class="tour-card" style="position: sticky; top: 20px;">
                    <div class="tour-content">
                        <h3 class="tour-title">Eventi Programmati</h3>

                        <?php if (count($eventi) > 0): ?>
                            <div class="events-list" style="display: flex; flex-direction: column; gap: 1rem; margin-top: 1rem;">
                                <?php foreach ($eventi as $evento): ?>
                                    <?php
                                    $posti_prenotati = $evento['posti_prenotati'] ? $evento['posti_prenotati'] : 0;
                                    $posti_disponibili = $evento['max_partecipanti'] - $posti_prenotati;
                                    $data_formattata = date('d/m/Y', strtotime($evento['data_evento']));
                                    $ora_formattata = date('H:i', strtotime($evento['ora_inizio']));
                                    ?>
                                    <div class="event-item fadeIn" style="border: 1px solid var(--light); border-radius: 8px; padding: 1rem; background-color: white;">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <div>
                                                <h4 class="mb-1"><?= $data_formattata ?> - <?= $ora_formattata ?></h4>
                                                <p class="mb-1">Lingua: <?= htmlspecialchars($evento['lingua']) ?></p>
                                                <p class="mb-1">Guida: <?= htmlspecialchars($evento['guida_nome'] . ' ' . $evento['guida_cognome']) ?></p>
                                                <p>Posti disponibili: <?= $posti_disponibili ?>/<?= $evento['max_partecipanti'] ?></p>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-weight: 600; font-size: 1.25rem; color: var(--primary); margin-bottom: 0.5rem;">
                                                    <?= number_format($evento['prezzo'], 2) ?> €
                                                </div>
                                                <?php if (isset($_SESSION['nome'])): ?>
                                                    <?php if ($posti_disponibili > 0): ?>
                                                        <form action="aggiungi_carrello.php" method="post">
                                                            <input type="hidden" name="id_evento" value="<?= $evento['id'] ?>">
                                                            <button type="submit" class="btn btn-primary">Prenota</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <button class="btn btn-outline" disabled>Esaurito</button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <a href="../utenti/accedi_form.php?redirect=dettaglio_visita.php?id=<?= $id_visita ?>" class="btn btn-outline">Accedi per prenotare</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div style="padding: 1rem; border-radius: 8px; background-color: var(--light); margin-top: 1rem;">
                                <p>Non ci sono eventi programmati per questa visita al momento.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../strutture_pagina/footer.php'; ?>
</body>
</html>