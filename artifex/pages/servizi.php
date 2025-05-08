<?php
// Configurazione del DB
$config = require '../connessione_db/db_config.php';
require '../connessione_db/DB_Connect.php';
require_once '../connessione_db/functions.php';

// Connessione al DB
$db = DataBase_Connect::getDB($config);

// Ottieni tutte le visite disponibili
$query = "SELECT v.id, v.titolo, v.descrizione, v.durata_media, v.luogo, 
          COUNT(DISTINCT e.id) AS num_eventi, MIN(e.prezzo) AS prezzo_min 
          FROM visite v 
          LEFT JOIN eventi e ON v.id = e.id_visita 
          WHERE e.data_evento >= CURDATE() 
          GROUP BY v.id 
          ORDER BY v.titolo";

$stmt = $db->prepare($query);
$stmt->execute();
$visite = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artifex - Servizi Turistici</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
<?php include '../strutture_pagina/navbar.php'; ?>

<!-- Featured Tours Section -->
<section id="tours" class="section">
    <div class="section-header">
        <h2 class="section-title">Tour Disponibili</h2>
        <p class="section-subtitle">Esplora la nostra selezione di tour guidati progettati per offrirti un'esperienza indimenticabile</p>
    </div>

    <div class="tours-grid">
        <?php foreach ($visite as $visita): ?>
            <div class="tour-card fadeIn">
                <div class="tour-content">
                    <span class="tour-category"><?= htmlspecialchars($visita['luogo']) ?></span>
                    <h3 class="tour-title"><?= htmlspecialchars($visita['titolo']) ?></h3>
                    <div class="tour-info">
                        <span><i class="fas fa-clock"></i> <?= $visita['durata_media'] ?> minuti</span>
                        <span><i class="fas fa-calendar"></i> <?= $visita['num_eventi'] ?> eventi</span>
                    </div>
                    <p class="tour-description">
                        <?= htmlspecialchars(substr($visita['descrizione'], 0, 120)) ?>...
                    </p>
                    <div class="tour-footer">
                        <?php if ($visita['num_eventi'] > 0): ?>
                            <span class="tour-price"><?= number_format($visita['prezzo_min'], 2) ?> €</span>
                        <?php else: ?>
                            <span class="tour-price">Nessun evento programmato</span>
                        <?php endif; ?>
                        <a href="eventi.php?id=<?= $visita['id'] ?>" class="btn btn-primary">Dettagli</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter">
    <div class="newsletter-container">
        <h2>Resta Aggiornato</h2>
        <p>Iscriviti alla nostra newsletter per ricevere aggiornamenti sui nuovi tour ed offerte speciali.</p>
        <form class="newsletter-form">
            <input type="email" placeholder="La tua email" class="newsletter-input" required>
            <button type="submit" class="newsletter-btn">Iscriviti</button>
        </form>
    </div>
</section>

<?php include '../strutture_pagina/footer.php'; ?>
</body>
</html>