<?php
// Configurazione del DB
$config = require '../connessione_db/db_config.php';
require '../connessione_db/DB_Connect.php';
require_once '../connessione_db/functions.php';

// Connessione al DB
$db = DataBase_Connect::getDB($config);

// Verifica se l'utente è loggato come amministratore
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['ruolo']) || $_SESSION['ruolo'] != 'admin') {
    // Reindirizza alla pagina di login se l'utente non è loggato come admin
    header("Location: ../utenti/admin_accedi.php");
    exit();
}

// Recupera i dati dell'amministratore
$admin_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT id, username, email FROM amministratori WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Gestione aggiornamento profilo
$messaggio = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiorna_profilo'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    // Validazione base
    if (empty($username) || empty($email)) {
        $messaggio = '<div class="alert alert-danger">Tutti i campi sono obbligatori</div>';
    } else {
        // Verifica se lo username o l'email sono già utilizzati da un altro admin
        $stmt = $db->prepare("SELECT id FROM amministratori WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$username, $email, $admin_id]);
        $existing_admin = $stmt->fetch();

        if ($existing_admin) {
            $messaggio = '<div class="alert alert-danger">Username o email già in uso da un altro amministratore</div>';
        } else {
            // Aggiorna i dati
            $stmt = $db->prepare("UPDATE amministratori SET username = ?, email = ? WHERE id = ?");
            $risultato = $stmt->execute([$username, $email, $admin_id]);

            if ($risultato) {
                $messaggio = '<div class="alert alert-success">Profilo aggiornato con successo!</div>';
                // Aggiorna i dati dell'amministratore nella sessione
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;

                // Ricarica i dati dell'amministratore
                $stmt = $db->prepare("SELECT id, username, email FROM amministratori WHERE id = ?");
                $stmt->execute([$admin_id]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $messaggio = '<div class="alert alert-danger">Si è verificato un errore durante l\'aggiornamento</div>';
            }
        }
    }
}

// Gestione cambio password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cambia_password'])) {
    $password_attuale = $_POST['password_attuale'];
    $nuova_password = $_POST['nuova_password'];
    $conferma_password = $_POST['conferma_password'];

    // Verifica la password attuale
    $stmt = $db->prepare("SELECT password FROM amministratori WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin_data = $stmt->fetch(PDO::FETCH_ASSOC);

    // In produzione, usare password_verify
    // Per ora, confronto diretto per semplicità di implementazione
    if ($password_attuale != $admin_data['password']) { // In produzione: !password_verify($password_attuale, $admin_data['password'])
        $messaggio = '<div class="alert alert-danger">La password attuale non è corretta</div>';
    } elseif ($nuova_password !== $conferma_password) {
        $messaggio = '<div class="alert alert-danger">Le nuove password non corrispondono</div>';
    } elseif (strlen($nuova_password) < 8) {
        $messaggio = '<div class="alert alert-danger">La nuova password deve essere di almeno 8 caratteri</div>';
    } else {
        // Aggiorna la password
        // In produzione, usare password_hash
        // $password_hash = password_hash($nuova_password, PASSWORD_DEFAULT);
        $password_hash = $nuova_password; // In produzione: password_hash($nuova_password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE amministratori SET password = ? WHERE id = ?");
        $risultato = $stmt->execute([$password_hash, $admin_id]);

        if ($risultato) {
            $messaggio = '<div class="alert alert-success">Password aggiornata con successo!</div>';
        } else {
            $messaggio = '<div class="alert alert-danger">Si è verificato un errore durante l\'aggiornamento della password</div>';
        }
    }
}

// Recupera statistiche per la dashboard dell'amministratore
try {
    // Conta gli eventi totali
    $stmt = $db->query("SELECT COUNT(*) as total FROM eventi");
    $total_eventi = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Conta le guide
    $stmt = $db->query("SELECT COUNT(*) as total FROM guide");
    $total_guide = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Conta i visitatori registrati
    $stmt = $db->query("SELECT COUNT(*) as total FROM visitatori");
    $total_visitatori = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Conta gli ordini totali
    $stmt = $db->query("SELECT COUNT(*) as total FROM ordini");
    $total_ordini = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Calcola l'importo totale degli ordini
    $stmt = $db->query("SELECT SUM(importo_totale) as total FROM ordini");
    $importo_totale = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?: 0;
} catch (PDOException $e) {
    // Gestione degli errori
    error_log("Errore nella query delle statistiche: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo Amministratore - Artifex</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
<?php include '../strutture_pagina/navbar.php'; ?>

<div class="container mt-4 fadeIn">
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="bi bi-person-badge" style="font-size: 5rem; color: var(--primary);"></i>
            </div>
            <div class="profile-info">
                <h2>Admin: <?= htmlspecialchars($admin['username']) ?></h2>
                <p><i class="bi bi-envelope detail-icon"></i> <?= htmlspecialchars($admin['email']) ?></p>
                <p><i class="bi bi-shield-check detail-icon"></i> Amministratore</p>
            </div>
        </div>

        <?php if (!empty($messaggio)) echo $messaggio; ?>

        <div class="profile-tabs">
            <div class="profile-tab active" data-tab="dashboard">Dashboard</div>
            <div class="profile-tab" data-tab="info-personali">Informazioni Personali</div>
            <div class="profile-tab" data-tab="sicurezza">Sicurezza</div>
        </div>

        <div class="tab-content" id="dashboard">
            <h3 class="mb-4">Dashboard Amministratore</h3>

            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="stats-card d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="stats-content">
                            <h3><?= $total_eventi ?? 0 ?></h3>
                            <p>Eventi Totali</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="stats-card d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stats-content">
                            <h3><?= $total_guide ?? 0 ?></h3>
                            <p>Guide</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="stats-card d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="stats-content">
                            <h3><?= $total_visitatori ?? 0 ?></h3>
                            <p>Visitatori</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="stats-card d-flex align-items-center">
                        <div class="stats-icon">
                            <i class="bi bi-cart-check"></i>
                        </div>
                        <div class="stats-content">
                            <h3><?= $total_ordini ?? 0 ?></h3>
                            <p>Ordini</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="stats-card">
                        <h4><i class="bi bi-currency-euro"></i> Fatturato Totale</h4>
                        <h2 class="mt-3">€ <?= number_format($importo_totale ?? 0, 2, ',', '.') ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="info-personali" style="display: none;">
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-input" id="username" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-input" id="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" name="aggiorna_profilo" class="btn btn-primary">Aggiorna Profilo</button>
                </div>
            </form>
        </div>

        <div class="tab-content" id="sicurezza" style="display: none;">
            <h3>Cambia Password</h3>
            <form action="" method="POST">
                <div class="form-group mb-3">
                    <label for="password_attuale" class="form-label">Password Attuale</label>
                    <input type="password" class="form-input" id="password_attuale" name="password_attuale" required>
                </div>

                <div class="form-group mb-3">
                    <label for="nuova_password" class="form-label">Nuova Password</label>
                    <input type="password" class="form-input" id="nuova_password" name="nuova_password" required>
                    <small class="text-muted">La password deve contenere almeno 8 caratteri.</small>
                </div>

                <div class="form-group mb-3">
                    <label for="conferma_password" class="form-label">Conferma Nuova Password</label>
                    <input type="password" class="form-input" id="conferma_password" name="conferma_password" required>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" name="cambia_password" class="btn btn-primary">Cambia Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../strutture_pagina/footer.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    // Script per gestire i tab
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.profile-tab');
        const tabContents = document.querySelectorAll('.tab-content');

        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Rimuovi la classe active da tutti i tab
                tabs.forEach(t => t.classList.remove('active'));

                // Aggiungi la classe active al tab cliccato
                this.classList.add('active');

                // Nascondi tutti i contenuti
                tabContents.forEach(content => {
                    content.style.display = 'none';
                });

                // Mostra il contenuto selezionato
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).style.display = 'block';
            });
        });
    });
</script>
</body>
</html>