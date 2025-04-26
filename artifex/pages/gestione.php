<?php
// Configurazione del DB
$config = require '../connessione_db/db_config.php';
require '../connessione_db/DB_Connect.php';
require_once '../connessione_db/functions.php';

// Connessione al DB
$db = DataBase_Connect::getDB($config);

// Inizializzazione delle variabili per i messaggi
$success_message = "";
$error_message = "";

// Recupero delle guide dal database per la dropdown
$stmt_guide = $db->prepare("SELECT id, nome, cognome FROM guide ORDER BY cognome, nome");
$stmt_guide->execute();
$guide = $stmt_guide->fetchAll(PDO::FETCH_ASSOC);

// Recupero delle visite dal database per la dropdown
$stmt_visite = $db->prepare("SELECT id, titolo FROM visite ORDER BY titolo");
$stmt_visite->execute();
$visite = $stmt_visite->fetchAll(PDO::FETCH_ASSOC);

// Gestione form di aggiunta guida
if (isset($_POST['addGuida']))
{
    try
    {
        $stmt = $db->prepare("INSERT INTO guide (nome, cognome, data_nascita, luogo_nascita, titolo_studio) 
                             VALUES (:nome, :cognome, :data_nascita, :luogo_nascita, :titolo_studio)");

        $stmt->bindParam(':nome', $_POST['nome']);
        $stmt->bindParam(':cognome', $_POST['cognome']);
        $stmt->bindParam(':data_nascita', $_POST['data_nascita']);
        $stmt->bindParam(':luogo_nascita', $_POST['luogo_nascita']);
        $stmt->bindParam(':titolo_studio', $_POST['titolo_studio']);

        $stmt->execute();

        $guida_id = $db->lastInsertId();

        // Aggiunta delle competenze linguistiche
        if (isset($_POST['lingue']) && isset($_POST['livelli']) && is_array($_POST['lingue']) && is_array($_POST['livelli']))
        {
            $stmt_comp = $db->prepare("INSERT INTO competenze_linguistiche (id_guida, lingua, livello) VALUES (:id_guida, :lingua, :livello)");

            foreach ($_POST['lingue'] as $key => $lingua)
            {
                if (!empty($lingua) && isset($_POST['livelli'][$key]))
                {
                    $stmt_comp->bindParam(':id_guida', $guida_id);
                    $stmt_comp->bindParam(':lingua', $lingua);
                    $stmt_comp->bindParam(':livello', $_POST['livelli'][$key]);
                    $stmt_comp->execute();
                }
            }
        }

        $success_message = "Guida aggiunta con successo!";

        // Aggiornamento della lista delle guide
        $stmt_guide->execute();
        $guide = $stmt_guide->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e)
    {
        $error_message = "Errore durante l'aggiunta della guida: " . $e->getMessage();
    }
}

// Gestione form di aggiunta visita
if (isset($_POST['addVisita']))
{
    try
    {
        $stmt = $db->prepare("INSERT INTO visite (titolo, descrizione, durata_media, luogo) VALUES (:titolo, :descrizione, :durata_media, :luogo)");

        $stmt->bindParam(':titolo', $_POST['titolo']);
        $stmt->bindParam(':descrizione', $_POST['descrizione']);
        $stmt->bindParam(':durata_media', $_POST['durata_media']);
        $stmt->bindParam(':luogo', $_POST['luogo']);
        $stmt->execute();

        $success_message = "Visita aggiunta con successo!";

        // Aggiornamento della lista delle visite
        $stmt_visite->execute();
        $visite = $stmt_visite->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (PDOException $e)
    {
        $error_message = "Errore durante l'aggiunta della visita: " . $e->getMessage();
    }
}

// Gestione form di aggiunta evento
if (isset($_POST['addEvento']))
{
    try {
        $stmt = $db->prepare("INSERT INTO eventi (id_visita, id_guida, data_evento, ora_inizio, lingua, 
                                                prezzo, min_partecipanti, max_partecipanti) 
                             VALUES (:id_visita, :id_guida, :data_evento, :ora_inizio, :lingua, 
                                    :prezzo, :min_partecipanti, :max_partecipanti)");

        $stmt->bindParam(':id_visita', $_POST['id_visita']);
        $stmt->bindParam(':id_guida', $_POST['id_guida']);
        $stmt->bindParam(':data_evento', $_POST['data_evento']);
        $stmt->bindParam(':ora_inizio', $_POST['ora_inizio']);
        $stmt->bindParam(':lingua', $_POST['lingua']);
        $stmt->bindParam(':prezzo', $_POST['prezzo']);
        $stmt->bindParam(':min_partecipanti', $_POST['min_partecipanti']);
        $stmt->bindParam(':max_partecipanti', $_POST['max_partecipanti']);

        $stmt->execute();

        $success_message = "Evento aggiunto con successo!";
    }
    catch (PDOException $e)
    {
        $error_message = "Errore durante l'aggiunta dell'evento: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Amministrazione - Artifex</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
<?php
require '../strutture_pagina/navbar.php';
?>

<div class="admin-container">
    <h1>Gestione Eventi e Visite</h1>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div class="admin-tabs">
        <div class="admin-tab active" data-tab="guide">Guide</div>
        <div class="admin-tab" data-tab="visite">Visite</div>
        <div class="admin-tab" data-tab="eventi">Eventi</div>
    </div>

    <div class="tab-content active" id="guide-tab">
        <div class="admin-content">
            <h2>Aggiungi Nuova Guida</h2>
            <form action="" method="post">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="nome">Nome</label>
                        <input type="text" id="nome" name="nome" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cognome">Cognome</label>
                        <input type="text" id="cognome" name="cognome" class="form-input" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="data_nascita">Data di Nascita</label>
                        <input type="date" id="data_nascita" name="data_nascita" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="luogo_nascita">Luogo di Nascita</label>
                        <input type="text" id="luogo_nascita" name="luogo_nascita" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="titolo_studio">Titolo di Studio</label>
                    <input type="text" id="titolo_studio" name="titolo_studio" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Competenze Linguistiche</label>
                    <div id="lingue-container">
                        <div class="language-fields">
                            <div class="language-row">
                                <input type="text" name="lingue[]" class="form-input" placeholder="Lingua (es. Italiano)" required>
                                <select name="livelli[]" class="form-input" required>
                                    <option value="">Seleziona livello</option>
                                    <option value="normale">Normale</option>
                                    <option value="avanzato">Avanzato</option>
                                    <option value="madrelingua">Madrelingua</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary add-language-btn" id="add-language">
                        <i class="fas fa-plus"></i> Aggiungi Lingua
                    </button>
                </div>

                <div class="form-group">
                    <button type="submit" name="addGuida" class="btn btn-primary">Aggiungi Guida</button>
                </div>
            </form>
        </div>
    </div>

    <div class="tab-content" id="visite-tab">
        <div class="admin-content">
            <h2>Aggiungi Nuova Visita</h2>
            <form action="" method="post">
                <div class="form-group">
                    <label class="form-label" for="titolo">Titolo</label>
                    <input type="text" id="titolo" name="titolo" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="descrizione">Descrizione</label>
                    <textarea id="descrizione" name="descrizione" class="form-input" rows="5" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="durata_media">Durata Media (minuti)</label>
                        <input type="number" id="durata_media" name="durata_media" class="form-input" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="luogo">Luogo</label>
                        <input type="text" id="luogo" name="luogo" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" name="addVisita" class="btn btn-primary">Aggiungi Visita</button>
                </div>
            </form>
        </div>
    </div>

    <div class="tab-content" id="eventi-tab">
        <div class="admin-content">
            <h2>Aggiungi Nuovo Evento</h2>
            <form action="" method="post">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="id_visita">Visita</label>
                        <select id="id_visita" name="id_visita" class="form-input" required>
                            <option value="">Seleziona visita</option>
                            <?php foreach ($visite as $visita): ?>
                                <option value="<?php echo $visita['id']; ?>"><?php echo htmlspecialchars($visita['titolo']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="id_guida">Guida</label>
                        <select id="id_guida" name="id_guida" class="form-input" required>
                            <option value="">Seleziona guida</option>
                            <?php foreach ($guide as $guida): ?>
                                <option value="<?php echo $guida['id']; ?>"><?php echo htmlspecialchars($guida['cognome'] . ' ' . $guida['nome']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="data_evento">Data Evento</label>
                        <input type="date" id="data_evento" name="data_evento" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="ora_inizio">Ora Inizio</label>
                        <input type="time" id="ora_inizio" name="ora_inizio" class="form-input" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="lingua">Lingua</label>
                        <input type="text" id="lingua" name="lingua" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="prezzo">Prezzo</label>
                        <input type="number" id="prezzo" name="prezzo" class="form-input" step="0.01" min="0" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="min_partecipanti">Minimo Partecipanti</label>
                        <input type="number" id="min_partecipanti" name="min_partecipanti" class="form-input" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="max_partecipanti">Massimo Partecipanti</label>
                        <input type="number" id="max_partecipanti" name="max_partecipanti" class="form-input" min="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" name="addEvento" class="btn btn-primary">Aggiungi Evento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
require '../strutture_pagina/footer.php';
?>

<script>
    // Gestione tabs
    document.querySelectorAll('.admin-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            // Rimuove la classe active da tutti i tab
            document.querySelectorAll('.admin-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

            // Aggiunge la classe active al tab cliccato
            tab.classList.add('active');
            document.getElementById(tab.getAttribute('data-tab') + '-tab').classList.add('active');
        });
    });

    // Aggiunta dinamica di campi per le lingue
    document.getElementById('add-language').addEventListener('click', () => {
        const container = document.getElementById('lingue-container');
        const template = `
                <div class="language-fields">
                    <div class="language-row">
                        <input type="text" name="lingue[]" class="form-input" placeholder="Lingua (es. Italiano)" required>
                        <select name="livelli[]" class="form-input" required>
                            <option value="">Seleziona livello</option>
                            <option value="normale">Normale</option>
                            <option value="avanzato">Avanzato</option>
                            <option value="madrelingua">Madrelingua</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-outline-danger btn-sm remove-language">
                        <i class="fas fa-times"></i> Rimuovi
                    </button>
                </div>
            `;

        // Aggiunta del template al container
        const div = document.createElement('div');
        div.innerHTML = template;
        container.appendChild(div.firstElementChild);

        // Aggiunta event listener per rimuovere il campo lingua
        document.querySelectorAll('.remove-language').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.target.closest('.language-fields').remove();
            });
        });
    });
</script>
</body>
</html>