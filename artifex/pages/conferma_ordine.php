<?php
// Configurazione del DB
$config = require '../connessione_db/db_config.php';
require '../connessione_db/DB_Connect.php';
require_once '../connessione_db/functions.php';

// Avvia la sessione se non è già attiva
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// Verifica se c'è un ordine completato nella sessione
if (!isset($_SESSION['ordine_completato'])) {
    header('Location: profilo.php');
    exit;
}

$id_ordine = $_SESSION['ordine_completato'];
unset($_SESSION['ordine_completato']); // Pulisci la sessione

// Connessione al DB
$db = DataBase_Connect::getDB($config);

$id_visitatore = $_SESSION['user_id'];

// Ottieni i dettagli dell'ordine
$query = "SELECT o.*, c.data_creazione 
          FROM ordini o 
          JOIN carrelli c ON o.id_carrello = c.id 
          WHERE o.id = ? AND o.id_visitatore = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id_ordine, $id_visitatore]);
$ordine = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ordine) {
    header('Location: profilo.php');
    exit;
}

// Ottieni l'elenco dei biglietti emessi
$query = "SELECT b.id, b.codice_qr, e.data_evento, e.ora_inizio, e.lingua,
          v.titolo AS nome_visita, v.luogo
          FROM biglietti b
          JOIN eventi e ON b.id_evento = e.id
          JOIN visite v ON e.id_visita = v.id
          WHERE b.id_ordine = ?
          ORDER BY e.data_evento, e.ora_inizio";
$stmt = $db->prepare($query);
$stmt->execute([$id_ordine]);
$biglietti = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Invia email di conferma (simulato)
if ($ordine && !empty($biglietti)) {
    // In un'applicazione reale, qui si invierebbe un'email con i dettagli dell'ordine
    // e i biglietti allegati o link per scaricarli
    $email_inviata = true;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conferma Ordine - Artifex</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
<?php include '../strutture_pagina/navbar.php'; ?>

<section class="section">
    <div class="container">
        <div style="max-width: 800px; margin: 0 auto;">
            <div class="tour-card mb-3" style="border-top: 4px solid #2ecc71;">
                <div class="tour-content">
                    <div style="display: flex; align-items: center; margin-bottom: 1rem; color: #2ecc71;">
                        <i class="fas fa-check-circle" style="font-size: 1.5rem; margin-right: 0.5rem;"></i>
                        <h2 class="tour-title" style="margin: 0; color: #2ecc71;">Ordine Confermato</h2>
                    </div>

                    <p style="font-size: 1.2rem;">Grazie per il tuo ordine! La tua prenotazione è stata confermata.</p>
                    <p>Il numero del tuo ordine è: <strong>#<?= $id_ordine ?></strong></p>
                    <p>Data dell'ordine: <?= date('d/m/Y H:i', strtotime($ordine['data_ordine'])) ?></p>
                    <p>Importo totale: <strong><?= number_format($ordine['importo_totale'], 2) ?> €</strong></p>

                    <?php if (isset($email_inviata)): ?>
                        <div style="background-color: var(--light); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                            <i class="fas fa-envelope"></i> Abbiamo inviato una conferma via email con i dettagli del tuo ordine.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="tour-card">
                <div class="tour-content">
                    <h3 class="tour-title">I Tuoi Biglietti</h3>
                    <p>Di seguito trovi l'elenco dei biglietti che hai acquistato:</p>

                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
                        <?php foreach ($biglietti as $biglietto): ?>
                            <?php
                            $data_formattata = date('d/m/Y', strtotime($biglietto['data_evento']));
                            $ora_formattata = date('H:i', strtotime($biglietto['ora_inizio']));
                            ?>
                            <div class="fadeIn" style="border: 1px solid var(--light); border-radius: 8px; padding: 1rem; background-color: white;">
                                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                                    <div>
                                        <h4 style="margin-bottom: 0.5rem;"><?= htmlspecialchars($biglietto['nome_visita']) ?></h4>
                                        <p style="margin-bottom: 0.5rem;"><?= htmlspecialchars($biglietto['luogo']) ?> - <?= $data_formattata ?> <?= $ora_formattata ?></p>
                                        <p style="margin-bottom: 0;">Lingua: <?= htmlspecialchars($biglietto['lingua']) ?></p>
                                    </div>
                                    <div>
                                        <a href="biglietto.php?id=<?= $biglietto['id'] ?>" class="btn btn-outline-primary" target="_blank">
                                            <i class="fas fa-file-pdf"></i> Scarica Biglietto
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <a href="profilo.php" class="btn btn-primary">
                            <i class="fas fa-user"></i> Vai al Tuo Profilo
                        </a>
                        <a href="servizi.php" class="btn btn-outline">
                            <i class="fas fa-search"></i> Esplora Altri Tour
                        </a>
                    </div>
                </div>
            </div>
        </div>
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