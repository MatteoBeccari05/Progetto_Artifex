<?php

$content = 'ArtiFex';
require_once '../strutture_pagina/functions_active_navbar.php';
require '../strutture_pagina/navbar.php';

// Configurazione del DB
$config = require '../connessione_db/db_config.php';
require '../connessione_db/DB_Connect.php';
require_once '../connessione_db/functions.php';

// Connessione al DB
$db = DataBase_Connect::getDB($config);

// Function to get featured tours
function getFeaturedTours($db) {
    $sql = "SELECT v.id, v.titolo, v.descrizione, v.durata_media, v.luogo 
            FROM visite v 
            LIMIT 3";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get upcoming events
function getUpcomingEvents($db) {
    $today = date('Y-m-d');

    $sql = "SELECT e.id, e.data_evento, e.ora_inizio, e.lingua, e.prezzo, 
                  e.min_partecipanti, e.max_partecipanti, v.titolo, v.luogo, 
                  g.nome, g.cognome,
                  (SELECT COUNT(*) FROM elementi_carrello ec 
                   JOIN carrelli c ON ec.id_carrello = c.id 
                   WHERE ec.id_evento = e.id AND c.stato = 'pagato') as posti_occupati
            FROM eventi e
            JOIN visite v ON e.id_visita = v.id
            JOIN guide g ON e.id_guida = g.id
            WHERE e.data_evento >= :today
            ORDER BY e.data_evento ASC
            LIMIT 3";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':today', $today);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get featured guides
function getFeaturedGuides($db) {
    $sql = "SELECT g.id, g.nome, g.cognome, g.titolo_studio, g.luogo_nascita
            FROM guide g
            LIMIT 3";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $guides = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get language skills for each guide
    foreach ($guides as &$guide) {
        $sql_languages = "SELECT lingua, livello 
                         FROM competenze_linguistiche 
                         WHERE id_guida = :id_guida";

        $stmt_languages = $db->prepare($sql_languages);
        $stmt_languages->bindParam(':id_guida', $guide['id']);
        $stmt_languages->execute();

        $guide['lingue'] = $stmt_languages->fetchAll(PDO::FETCH_ASSOC);
    }

    return $guides;
}

// Get data from the database
$featuredTours = getFeaturedTours($db);
$upcomingEvents = getUpcomingEvents($db);
$featuredGuides = getFeaturedGuides($db);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Home</title>
</head>
<body>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content fadeIn">
        <h1>Scopri l'Arte e la Storia con Artifex</h1>
        <p>Visite guidate ai più affascinanti siti di interesse storico-culturale in Italia e nel mondo</p>
        <div class="hero-cta">
            <a href="eventi.php" class="btn btn-primary">Esplora le Visite</a>
        </div>
    </div>
</section>

<!-- Featured Tours Section -->
<section class="section">
    <div class="section-header">
        <h2 class="section-title">I nostri tour guidati</h2>
        <p class="section-subtitle">Scopri i nostri itinerari più popolari selezionati dai migliori esperti del settore</p>
    </div>

    <div class="tours-grid">
        <?php foreach ($featuredTours as $tour): ?>
            <div class="tour-card">
                <div class="tour-content">
                    <span class="tour-category"><?php echo htmlspecialchars($tour['luogo']); ?></span>
                    <h3 class="tour-title"><?php echo htmlspecialchars($tour['titolo']); ?></h3>
                    <div class="tour-info">
                        <span><i class="fa-regular fa-clock"></i> <?php echo $tour['durata_media']/60; ?> ore</span>
                        <span><i class="fa-solid fa-user-group"></i> Max 20 persone</span>
                    </div>
                    <p class="tour-description"><?php echo htmlspecialchars(substr($tour['descrizione'], 0, 150)) . '...'; ?></p>
                    <div class="tour-footer">
                        <?php
                        // Get the average price for this tour
                        $stmt = $db->prepare("SELECT AVG(prezzo) as prezzo_medio FROM eventi WHERE id_visita = :id");
                        $stmt->bindParam(':id', $tour['id']);
                        $stmt->execute();
                        $avgPrice = $stmt->fetch(PDO::FETCH_ASSOC);
                        $price = $avgPrice['prezzo_medio'] ? number_format($avgPrice['prezzo_medio'], 2) : "N/A";
                        ?>
                        <span class="tour-price">€<?php echo $price; ?> / persona</span>
                        <a href="eventi.php?id=<?php echo $tour['id']; ?>" class="btn btn-primary">Dettagli</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="text-center mt-3">
        <a href="servizi.php" class="btn btn-outline">Vedi Tutte le Visite</a>
    </div>
</section>

<!-- How It Works Section -->
<section class="features">
    <div class="features-container">
        <div class="section-header">
            <h2 class="section-title">Come Funziona</h2>
            <p class="section-subtitle">Prenotare una visita guidata con Artifex è semplice e veloce</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3 class="feature-title">Scegli la Tua Visita</h3>
                <p>Sfoglia la nostra selezione di visite guidate ai più importanti siti storico-culturali e scegli quella che ti interessa.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <h3 class="feature-title">Seleziona la Data</h3>
                <p>Ogni visita ha diverse date disponibili. Seleziona quella più comoda per te e aggiungi al carrello.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <h3 class="feature-title">Prenota e Paga</h3>
                <p>Completa il checkout e ricevi il tuo biglietto con QR code direttamente via email, pronto da stampare.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-route"></i>
                </div>
                <h3 class="feature-title">Vivi l'Esperienza</h3>
                <p>Presentati all'orario stabilito e incontra la tua guida che ti accompagnerà in un viaggio attraverso arte e storia.</p>
            </div>
        </div>
    </div>
</section>



<!-- Guide Section -->
<section class="section">
    <div class="section-header">
        <h2 class="section-title">Le Nostre Guide</h2>
        <p class="section-subtitle">Professionisti esperti e multilingue per accompagnarti alla scoperta del patrimonio culturale</p>
    </div>

    <div class="tours-grid">
        <?php foreach ($featuredGuides as $index => $guide): ?>
            <div class="tour-card">
                <div class="tour-content">
                    <h3 class="tour-title"><?php echo htmlspecialchars($guide['nome'] . ' ' . $guide['cognome']); ?></h3>
                    <div class="event-details">
                        <div class="event-detail">
                            <i class="fa-solid fa-graduation-cap detail-icon"></i>
                            <span><?php echo htmlspecialchars($guide['titolo_studio']); ?></span>
                        </div>
                        <div class="event-detail">
                            <i class="fa-solid fa-language detail-icon"></i>
                            <span>
                            <?php
                            $languages = [];
                            foreach ($guide['lingue'] as $lang) {
                                $languages[] = $lang['lingua'] . ' (' . $lang['livello'] . ')';
                            }
                            echo htmlspecialchars(implode(', ', $languages));
                            ?>
                        </span>
                        </div>
                        <div class="event-detail">
                            <i class="fa-solid fa-location-dot detail-icon"></i>
                            <span><?php echo htmlspecialchars($guide['luogo_nascita']); ?></span>
                        </div>
                    </div>
                    <?php
                    // Get random description for guide
                    $descriptions = [
                        "Specializzato in arte rinascimentale e barocca, vi accompagnerà alla scoperta dei più importanti siti culturali.",
                        "Con oltre 10 anni di esperienza nel settore, è la guida ideale per scoprire i segreti dell'arte e della storia.",
                        "Esperto di arte e archeologia, rende ogni visita un'esperienza indimenticabile ricca di aneddoti e curiosità."
                    ];
                    $randomDesc = $descriptions[array_rand($descriptions)];
                    ?>
                    <p class="tour-description"><?php echo $randomDesc; ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>



<!-- Newsletter Section -->
<section class="newsletter">
    <div class="newsletter-container">
        <h2>Resta Aggiornato</h2>
        <p>Iscriviti alla nostra newsletter per ricevere in anteprima le nuove visite guidate e offerte speciali</p>
        <form class="newsletter-form" action="#" method="post">
            <input type="email" name="email" placeholder="La tua email" class="newsletter-input" required>
            <button type="submit" class="newsletter-btn">Iscriviti</button>
        </form>
    </div>
</section>

<?php
require '../strutture_pagina/footer.php';
?>


</body>
</html>