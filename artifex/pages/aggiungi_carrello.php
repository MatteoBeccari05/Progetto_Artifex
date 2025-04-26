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
    header('Location: ../utenti/accedi_form.php');
    exit;
}

// Verifica se è stato inviato l'ID dell'evento
if (!isset($_POST['id_evento']) || !is_numeric($_POST['id_evento'])) {
    $_SESSION['error'] = "Evento non specificato.";
    header('Location: servizi.php');
    exit;
}

// Connessione al DB
$db = DataBase_Connect::getDB($config);

$id_visitatore = $_SESSION['user_id'];
$id_evento = (int)$_POST['id_evento'];

try
{
    // Inizia una transazione
    $db->beginTransaction();

    // Verifica se l'evento esiste e se ci sono posti disponibili
    $query = "SELECT e.*, 
              (SELECT SUM(ec.quantita) FROM elementi_carrello ec 
               JOIN carrelli c ON ec.id_carrello = c.id 
               WHERE ec.id_evento = e.id AND c.stato != 'annullato') AS posti_prenotati
              FROM eventi e
              WHERE e.id = ? AND e.data_evento >= CURDATE()";
    $stmt = $db->prepare($query);
    $stmt->execute([$id_evento]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evento) {
        throw new Exception("Evento non trovato o non più disponibile.");
    }

    $posti_prenotati = $evento['posti_prenotati'] ? $evento['posti_prenotati'] : 0;
    $posti_disponibili = $evento['max_partecipanti'] - $posti_prenotati;

    if ($posti_disponibili <= 0)
    {
        throw new Exception("Ci dispiace, ma l'evento è al completo.");
    }

    // Verifica se esiste già un carrello attivo per questo utente
    $query = "SELECT id FROM carrelli WHERE id_visitatore = ? AND stato = 'attivo'";
    $stmt = $db->prepare($query);
    $stmt->execute([$id_visitatore]);
    $carrello = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$carrello) {
        // Crea un nuovo carrello se non esiste
        $query = "INSERT INTO carrelli (id_visitatore, stato) VALUES (?, 'attivo')";
        $stmt = $db->prepare($query);
        $stmt->execute([$id_visitatore]);
        $id_carrello = $db->lastInsertId();
    } else {
        $id_carrello = $carrello['id'];
    }

    // Verifica se l'evento è già nel carrello
    $query = "SELECT id, quantita FROM elementi_carrello WHERE id_carrello = ? AND id_evento = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$id_carrello, $id_evento]);
    $elemento = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($elemento) {
        // Se l'elemento esiste già, aumenta la quantità
        $nuova_quantita = $elemento['quantita'] + 1;

        if ($nuova_quantita > $posti_disponibili)
        {
            throw new Exception("Non ci sono abbastanza posti disponibili per questo evento.");
        }

        $query = "UPDATE elementi_carrello SET quantita = ? WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$nuova_quantita, $elemento['id']]);
    } else {
        // Altrimenti, crea un nuovo elemento nel carrello
        $query = "INSERT INTO elementi_carrello (id_carrello, id_evento, quantita) VALUES (?, ?, 1)";
        $stmt = $db->prepare($query);
        $stmt->execute([$id_carrello, $id_evento]);
    }

    // Commit della transazione
    $db->commit();
    $_SESSION['success'] = "Evento aggiunto al carrello con successo!";

    // Reindirizza alla pagina del carrello
    header('Location: carrello.php');
    exit;

}
catch (Exception $e)
{
    // Rollback in caso di errore
    if ($db->inTransaction())
    {
        $db->rollBack();
    }
    $_SESSION['error'] = $e->getMessage();

    // Ottieni l'ID della visita per reindirizzare alla pagina di dettaglio
    $query = "SELECT id_visita FROM eventi WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$id_evento]);
    $visita = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($visita)
    {
        header('Location: dettaglio_visita.php?id=' . $visita['id_visita']);
    }
    else
    {
        header('Location: servizi.php');
    }
    exit;
}