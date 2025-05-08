<?php
// Configurazione del DB
$config = require '../connessione_db/db_config.php';
require '../connessione_db/DB_Connect.php';
require_once '../connessione_db/functions.php';

// Avvia la sessione se non è già attiva
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se l'utente è loggato
if (!isset($_SESSION['nome'])) {
    header('Location: login.php?redirect=carrello.php');
    exit;
}

// Connessione al DB
$db = DataBase_Connect::getDB($config);

$id_visitatore = $_SESSION['user_id'];

// Gestione della rimozione di elementi dal carrello
if (isset($_POST['rimuovi_elemento']) && is_numeric($_POST['id_elemento'])) {
    $id_elemento = (int)$_POST['id_elemento'];

    $query = "DELETE FROM elementi_carrello WHERE id = ? AND id_carrello IN (SELECT id FROM carrelli WHERE id_visitatore = ? AND stato = 'attivo')";
    $stmt = $db->prepare($query);
    $stmt->execute([$id_elemento, $id_visitatore]);

    $_SESSION['success'] = "Elemento rimosso dal carrello.";
    header('Location: carrello.php');
    exit;
}

// Gestione dell'aggiornamento della quantità
if (isset($_POST['aggiorna_quantita']) && isset($_POST['quantita']) && is_array($_POST['quantita'])) {
    foreach ($_POST['quantita'] as $id_elemento => $quantita) {
        if (is_numeric($id_elemento) && is_numeric($quantita) && $quantita > 0) {
            // Verifica disponibilità posti
            $query = "SELECT e.max_partecipanti, e.id,
                     (SELECT SUM(ec.quantita) FROM elementi_carrello ec 
                      JOIN carrelli c ON ec.id_carrello = c.id 
                      WHERE ec.id_evento = e.id AND c.stato != 'annullato' AND ec.id != ?) AS posti_prenotati
                     FROM elementi_carrello ec
                     JOIN eventi e ON ec.id_evento = e.id
                     WHERE ec.id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$id_elemento, $id_elemento]);
            $evento = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($evento) {
                $posti_prenotati = $evento['posti_prenotati'] ? $evento['posti_prenotati'] : 0;
                $posti_disponibili = $evento['max_partecipanti'] - $posti_prenotati;
                if ($quantita <= $posti_disponibili) {
                    $query = "UPDATE elementi_carrello SET quantita = ? WHERE id = ? AND id_carrello IN (SELECT id FROM carrelli WHERE id_visitatore = ? AND stato = 'attivo')";
                    $stmt = $db->prepare($query);
                    $stmt->execute([$quantita, $id_elemento, $id_visitatore]);
                } else {
                    $_SESSION['error'] = "Non ci sono abbastanza posti disponibili per alcuni eventi.";
                }
            }
        }
    }

    header('Location: carrello.php');
    exit;
}

// Ottieni il carrello attivo dell'utente
$query = "SELECT c.id FROM carrelli c WHERE c.id_visitatore = ? AND c.stato = 'attivo'";
$stmt = $db->prepare($query);
$stmt->execute([$id_visitatore]);
$carrello = $stmt->fetch(PDO::FETCH_ASSOC);

$elementi_carrello = [];
$totale = 0;

if ($carrello) {
    $id_carrello = $carrello['id'];

    // Ottieni gli elementi del carrello
    $query = "SELECT ec.id, ec.quantita, e.id AS id_evento, e.data_evento, e.ora_inizio, e.prezzo, e.lingua,
              v.titolo AS nome_visita, v.luogo, v.durata_media,
              g.nome AS guida_nome, g.cognome AS guida_cognome,
              (SELECT SUM(ec2.quantita) FROM elementi_carrello ec2 
               JOIN carrelli c2 ON ec2.id_carrello = c2.id 
               WHERE ec2.id_evento = e.id AND c2.stato != 'annullato' AND ec2.id != ec.id) AS posti_prenotati,
              e.max_partecipanti
              FROM elementi_carrello ec
              JOIN eventi e ON ec.id_evento = e.id
              JOIN visite v ON e.id_visita = v.id
              JOIN guide g ON e.id_guida = g.id
              WHERE ec.id_carrello = ?
              ORDER BY e.data_evento, e.ora_inizio";
    $stmt = $db->prepare($query);
    $stmt->execute([$id_carrello]);
    $elementi_carrello = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcola il totale
    foreach ($elementi_carrello as $elemento) {
        $totale += $elemento['prezzo'] * $elemento['quantita'];
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Il tuo Carrello - Artifex</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
<?php include '../strutture_pagina/navbar.php'; ?>

<div class="cart-container">
    <h1 class="section-title">Il tuo Carrello</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success'] ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error'] ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (empty($elementi_carrello)): ?>
        <div class="alert alert-info">
            <p>Il tuo carrello è vuoto.</p>
            <a href="servizi.php" class="btn btn-primary mt-1">Esplora i nostri tour</a>
        </div>
    <?php else: ?>
        <form method="post" action="carrello.php">
            <div class="table-responsive">
                <table class="cart-table">
                    <thead>
                    <tr>
                        <th>Tour</th>
                        <th>Data e Ora</th>
                        <th>Lingua</th>
                        <th>Guida</th>
                        <th>Prezzo Unitario</th>
                        <th>Quantità</th>
                        <th>Totale</th>
                        <th>Azioni</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($elementi_carrello as $elemento): ?>
                        <?php
                        $data_formattata = date('d/m/Y', strtotime($elemento['data_evento']));
                        $ora_formattata = date('H:i', strtotime($elemento['ora_inizio']));
                        $posti_prenotati = $elemento['posti_prenotati'] ? $elemento['posti_prenotati'] : 0;
                        $posti_disponibili = $elemento['max_partecipanti'] - $posti_prenotati;
                        $posti_max_per_utente = min($posti_disponibili + $elemento['quantita'], 10); // Limita a 10 posti max per semplicità
                        ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($elemento['nome_visita']) ?></strong><br>
                                <small style="color: #777;"><?= htmlspecialchars($elemento['luogo']) ?></small>
                            </td>
                            <td><?= $data_formattata ?> - <?= $ora_formattata ?></td>
                            <td><?= htmlspecialchars($elemento['lingua']) ?></td>
                            <td><?= htmlspecialchars($elemento['guida_nome'] . ' ' . $elemento['guida_cognome']) ?></td>
                            <td><?= number_format($elemento['prezzo'], 2) ?> €</td>
                            <td>
                                <select name="quantita[<?= $elemento['id'] ?>]" class="quantity-select">
                                    <?php for ($i = 1; $i <= $posti_max_per_utente; $i++): ?>
                                        <option value="<?= $i ?>" <?= $i == $elemento['quantita'] ? 'selected' : '' ?>><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                            <td><?= number_format($elemento['prezzo'] * $elemento['quantita'], 2) ?> €</td>
                            <td>
                                <form method="post" action="carrello.php" style="display: inline;">
                                    <input type="hidden" name="id_elemento" value="<?= $elemento['id'] ?>">
                                    <button type="submit" name="rimuovi_elemento" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="6" style="text-align: right;"><strong>Totale:</strong></td>
                        <td><strong><?= number_format($totale, 2) ?> €</strong></td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <div class="cart-actions">
                <a href="servizi.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Continua lo Shopping
                </a>
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="aggiorna_quantita" class="btn btn-outline-primary">
                        <i class="fas fa-sync-alt"></i> Aggiorna Carrello
                    </button>
                    <a href="checkout.php" class="btn btn-success">
                        <i class="fas fa-credit-card"></i> Procedi al Checkout
                    </a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>


<?php include '../strutture_pagina/footer.php'; ?>
</body>
</html>