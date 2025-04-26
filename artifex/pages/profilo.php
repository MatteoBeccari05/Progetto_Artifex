<?php
// Configurazione del DB
$config = require '../connessione_db/db_config.php';
require '../connessione_db/DB_Connect.php';
require_once '../connessione_db/functions.php';

// Connessione al DB
$db = DataBase_Connect::getDB($config);

// Verifica se l'utente è loggato
session_start();
if (!isset($_SESSION['user_id'])) {
    // Reindirizza alla pagina di login se l'utente non è loggato
    header("Location: login.php");
    exit();
}

// Recupera i dati dell'utente
$user_id = $_SESSION['user_id'];
$stmt = $db->prepare("SELECT id, nome, cognome, nazionalita, lingua_base, email, telefono FROM visitatori WHERE id = ?");
$stmt->execute([$user_id]);
$utente = $stmt->fetch(PDO::FETCH_ASSOC);

// Gestione aggiornamento profilo
$messaggio = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiorna_profilo'])) {
    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $nazionalita = trim($_POST['nazionalita']);
    $lingua = trim($_POST['lingua_base']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);

    // Validazione base
    if (empty($nome) || empty($cognome) || empty($email) || empty($telefono)) {
        $messaggio = '<div class="alert alert-danger">Tutti i campi sono obbligatori</div>';
    } else {
        // Verifica se l'email è già utilizzata da un altro utente
        $stmt = $db->prepare("SELECT id FROM visitatori WHERE email = ? AND id != ?");
        $stmt->execute([$email, $user_id]);
        $existing_user = $stmt->fetch();

        if ($existing_user) {
            $messaggio = '<div class="alert alert-danger">Email già in uso da un altro utente</div>';
        } else {
            // Aggiorna i dati
            $stmt = $db->prepare("UPDATE visitatori SET nome = ?, cognome = ?, nazionalita = ?, lingua_base = ?, email = ?, telefono = ? WHERE id = ?");
            $risultato = $stmt->execute([$nome, $cognome, $nazionalita, $lingua, $email, $telefono, $user_id]);

            if ($risultato) {
                $messaggio = '<div class="alert alert-success">Profilo aggiornato con successo!</div>';
                // Aggiorna i dati dell'utente
                $stmt = $db->prepare("SELECT id, nome, cognome, nazionalita, lingua_base, email, telefono FROM visitatori WHERE id = ?");
                $stmt->execute([$user_id]);
                $utente = $stmt->fetch(PDO::FETCH_ASSOC);
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
    $stmt = $db->prepare("SELECT password FROM visitatori WHERE id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($password_attuale, $user_data['password'])) {
        $messaggio = '<div class="alert alert-danger">La password attuale non è corretta</div>';
    } elseif ($nuova_password !== $conferma_password) {
        $messaggio = '<div class="alert alert-danger">Le nuove password non corrispondono</div>';
    } elseif (strlen($nuova_password) < 8) {
        $messaggio = '<div class="alert alert-danger">La nuova password deve essere di almeno 8 caratteri</div>';
    } else {
        // Aggiorna la password
        $password_hash = password_hash($nuova_password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE visitatori SET password = ? WHERE id = ?");
        $risultato = $stmt->execute([$password_hash, $user_id]);

        if ($risultato) {
            $messaggio = '<div class="alert alert-success">Password aggiornata con successo!</div>';
        } else {
            $messaggio = '<div class="alert alert-danger">Si è verificato un errore durante l\'aggiornamento della password</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo Utente - Artifex</title>
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
                <i class="bi bi-person-circle" style="font-size: 5rem; color: var(--primary);"></i>
            </div>
            <div class="profile-info">
                <h2><?= htmlspecialchars($utente['nome'] . ' ' . $utente['cognome']) ?></h2>
                <p><i class="bi bi-envelope detail-icon"></i> <?= htmlspecialchars($utente['email']) ?></p>
                <p><i class="bi bi-telephone detail-icon"></i> <?= htmlspecialchars($utente['telefono']) ?></p>
            </div>
        </div>

        <?php if (!empty($messaggio)) echo $messaggio; ?>

        <div class="profile-tabs">
            <div class="profile-tab active" data-tab="info-personali">Informazioni Personali</div>
            <div class="profile-tab" data-tab="sicurezza">Sicurezza</div>
        </div>

        <div class="tab-content" id="info-personali">
            <form action="" method="POST">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-input" id="nome" name="nome" value="<?= htmlspecialchars($utente['nome']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="cognome" class="form-label">Cognome</label>
                            <input type="text" class="form-input" id="cognome" name="cognome" value="<?= htmlspecialchars($utente['cognome']) ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="nazionalita" class="form-label">Nazionalità</label>
                            <input type="text" class="form-input" id="nazionalita" name="nazionalita" value="<?= htmlspecialchars($utente['nazionalita']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="lingua_base" class="form-label">Lingua Principale</label>
                            <select class="form-input" id="lingua_base" name="lingua_base" required>
                                <option value="italiano" <?= $utente['lingua_base'] == 'italiano' ? 'selected' : '' ?>>Italiano</option>
                                <option value="inglese" <?= $utente['lingua_base'] == 'inglese' ? 'selected' : '' ?>>Inglese</option>
                                <option value="francese" <?= $utente['lingua_base'] == 'francese' ? 'selected' : '' ?>>Francese</option>
                                <option value="spagnolo" <?= $utente['lingua_base'] == 'spagnolo' ? 'selected' : '' ?>>Spagnolo</option>
                                <option value="tedesco" <?= $utente['lingua_base'] == 'tedesco' ? 'selected' : '' ?>>Tedesco</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-input" id="email" name="email" value="<?= htmlspecialchars($utente['email']) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="telefono" class="form-label">Telefono</label>
                            <input type="tel" class="form-input" id="telefono" name="telefono" value="<?= htmlspecialchars($utente['telefono']) ?>" required>
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