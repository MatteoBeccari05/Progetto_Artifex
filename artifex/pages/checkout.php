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
if (!isset($_SESSION['user_id'])) {
    header('Location: ../utenti/accedi_form.php');
    exit;
}

// Connessione al DB
$db = DataBase_Connect::getDB($config);

$id_visitatore = $_SESSION['user_id'];

// Ottieni il carrello attivo dell'utente
$query = "SELECT c.id FROM carrelli c WHERE c.id_visitatore = ? AND c.stato = 'attivo'";
$stmt = $db->prepare($query);
$stmt->execute([$id_visitatore]);
$carrello = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$carrello) {
    $_SESSION['error'] = "Il tuo carrello è vuoto. Aggiungi qualche visita prima di procedere al checkout.";
    header('Location: carrello.php');
    exit;
}

$id_carrello = $carrello['id'];

// Ottieni gli elementi del carrello
$query = "SELECT ec.id, ec.quantita, e.id AS id_evento, e.data_evento, e.ora_inizio, e.prezzo, e.lingua,
          v.titolo AS nome_visita, v.luogo
          FROM elementi_carrello ec
          JOIN eventi e ON ec.id_evento = e.id
          JOIN visite v ON e.id_visita = v.id
          WHERE ec.id_carrello = ?
          ORDER BY e.data_evento, e.ora_inizio";
$stmt = $db->prepare($query);
$stmt->execute([$id_carrello]);
$elementi_carrello = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($elementi_carrello)) {
    $_SESSION['error'] = "Il tuo carrello è vuoto. Aggiungi qualche visita prima di procedere al checkout.";
    header('Location: carrello.php');
    exit;
}

// Calcola il totale
$totale = 0;
foreach ($elementi_carrello as $elemento) {
    $totale += $elemento['prezzo'] * $elemento['quantita'];
}

// Ottieni i dati del visitatore
$query = "SELECT nome, cognome, email FROM visitatori WHERE id = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id_visitatore]);
$visitatore = $stmt->fetch(PDO::FETCH_ASSOC);

// Gestione dell'invio del form di checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conferma_ordine'])) {
    try {
        // Inizia una transazione
        $db->beginTransaction();

        // Verifica nuovamente la disponibilità dei posti
        foreach ($elementi_carrello as $elemento) {
            $query = "SELECT e.max_partecipanti,
                     (SELECT COUNT(*) FROM elementi_carrello ec 
                      JOIN carrelli c ON ec.id_carrello = c.id 
                      WHERE ec.id_evento = e.id AND c.stato != 'annullato' AND c.id != ?) AS posti_prenotati
                     FROM eventi e
                     WHERE e.id = ?";
            $stmt = $db->prepare($query);
            $stmt->execute([$id_carrello, $elemento['id_evento']]);
            $evento = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($evento) {
                $posti_disponibili = $evento['max_partecipanti'] - $evento['posti_prenotati'];
                if ($elemento['quantita'] > $posti_disponibili) {
                    throw new Exception("Ci dispiace, ma non ci sono più posti disponibili per alcuni eventi nel tuo carrello.");
                }
            }
        }

        // Segna il carrello come pagato
        $query = "UPDATE carrelli SET stato = 'pagato' WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$id_carrello]);

        // Crea un nuovo ordine
        $query = "INSERT INTO ordini (id_visitatore, id_carrello, importo_totale) VALUES (?, ?, ?)";
        $stmt = $db->prepare($query);
        $stmt->execute([$id_visitatore, $id_carrello, $totale]);
        $id_ordine = $db->lastInsertId();

        // Crea i biglietti per ogni evento prenotato
        foreach ($elementi_carrello as $elemento) {
            for ($i = 0; $i < $elemento['quantita']; $i++) {
                // Genera un codice QR univoco (qui una semplice stringa casuale)
                $codice_qr = md5($id_ordine . '_' . $elemento['id_evento'] . '_' . $i . '_' . time());

                $query = "INSERT INTO biglietti (id_ordine, id_evento, id_visitatore, codice_qr) 
                          VALUES (?, ?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$id_ordine, $elemento['id_evento'], $id_visitatore, $codice_qr]);
            }
        }

        // Commit della transazione
        $db->commit();

        // Salva l'ID dell'ordine nella sessione e reindirizza alla pagina di conferma
        $_SESSION['ordine_completato'] = $id_ordine;
        header('Location: conferma_ordine.php');
        exit;

    } catch (Exception $e) {
        // Rollback in caso di errore
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $_SESSION['error'] = $e->getMessage();
        header('Location: checkout.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Artifex</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../style/style.css">

</head>
<body>
<?php include '../strutture_pagina/navbar.php'; ?>

<div class="checkout-container">
    <h1 class="section-title">Checkout</h1>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= $_SESSION['error'] ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="checkout-grid">
        <div class="checkout-main">
            <div class="checkout-card">
                <div class="checkout-card-header">
                    <h3 class="mb-0">Riepilogo dell'ordine</h3>
                </div>
                <div class="checkout-card-body">
                    <div style="overflow-x: auto;">
                        <table class="checkout-table">
                            <thead>
                            <tr>
                                <th>Tour</th>
                                <th>Data e Ora</th>
                                <th>Lingua</th>
                                <th>Quantità</th>
                                <th style="text-align: right;">Prezzo</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($elementi_carrello as $elemento): ?>
                                <?php
                                $data_formattata = date('d/m/Y', strtotime($elemento['data_evento']));
                                $ora_formattata = date('H:i', strtotime($elemento['ora_inizio']));
                                ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($elemento['nome_visita']) ?></strong><br>
                                        <small style="color: #777;"><?= htmlspecialchars($elemento['luogo']) ?></small>
                                    </td>
                                    <td><?= $data_formattata ?> - <?= $ora_formattata ?></td>
                                    <td><?= htmlspecialchars($elemento['lingua']) ?></td>
                                    <td><?= $elemento['quantita'] ?></td>
                                    <td style="text-align: right;"><?= number_format($elemento['prezzo'] * $elemento['quantita'], 2) ?> €</td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: right;"><strong>Totale:</strong></td>
                                <td style="text-align: right;"><strong><?= number_format($totale, 2) ?> €</strong></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="checkout-card">
                <div class="checkout-card-header">
                    <h3 class="mb-0">Dati Personali</h3>
                </div>
                <div class="checkout-card-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Nome</label>
                            <p><?= htmlspecialchars($visitatore['nome']) ?></p>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cognome</label>
                            <p><?= htmlspecialchars($visitatore['cognome']) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <p><?= htmlspecialchars($visitatore['email']) ?></p>
                    </div>
                </div>
            </div>

            <div class="checkout-card">
                <div class="checkout-card-header">
                    <h3 class="mb-0">Metodo di Pagamento</h3>
                </div>
                <div class="checkout-card-body">
                    <form id="payment-form" method="post" action="checkout.php">
                        <!-- Simulazione di form di pagamento -->
                        <div class="form-group">
                            <label for="card-number" class="form-label">Numero di Carta</label>
                            <input type="text" class="form-input" id="card-number" placeholder="**** **** **** ****" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiry" class="form-label">Data di Scadenza</label>
                                <input type="text" class="form-input" id="expiry" placeholder="MM/AA" required>
                            </div>
                            <div class="form-group">
                                <label for="cvv" class="form-label">CVV</label>
                                <input type="text" class="form-input" id="cvv" placeholder="123" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="cardholder" class="form-label">Nome sulla Carta</label>
                            <input type="text" class="form-input" id="cardholder" required>
                        </div>

                        <div class="btn-group">
                            <button type="submit" name="conferma_ordine" class="btn btn-success">
                                <i class="fas fa-lock"></i> Conferma e Paga <?= number_format($totale, 2) ?> €
                            </button>
                            <a href="carrello.php" class="btn btn-outline">
                                <i class="fas fa-arrow-left"></i> Torna al Carrello
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="checkout-sidebar">
            <div class="checkout-card" style="position: sticky; top: 20px;">
                <div class="checkout-card-header">
                    <h3 class="mb-0">Riepilogo</h3>
                </div>
                <div class="checkout-card-body">
                    <div class="summary-item">
                        <span>Totale Eventi:</span>
                        <span><?= count($elementi_carrello) ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Totale Biglietti:</span>
                        <span><?= array_sum(array_column($elementi_carrello, 'quantita')) ?></span>
                    </div>
                    <div class="summary-divider"></div>
                    <div class="summary-item">
                        <strong>Totale:</strong>
                        <strong><?= number_format($totale, 2) ?> €</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php include '../strutture_pagina/footer.php'; ?>

</body>
</html>