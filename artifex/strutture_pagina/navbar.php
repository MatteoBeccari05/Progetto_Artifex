<?php
require_once 'functions_active_navbar.php';
session_start();
$logged_in = isset($_SESSION['nome']); // L'utente è loggato se la variabile di sessione esiste


?>

<div class="navbar">
    <div class="nav-left">
        <a href="home.php" class="<?= isActive('home.php') ?>">Home</a>
        <a href="visualizza_prodotti.php" class="<?= isActive('visualizza.php') ?>">Visualizza Prodotti</a>

        <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'admin'): ?>
            <a href="aggiungi_prodotto.php" class="<?= isActive('aggiungi_prodotto.php') ?>">Aggiungi Prodotto</a>
            <a href="modifica_prodotto.php" class="<?= isActive('modifica_prodotto.php') ?>">Gestione Prodotti</a>
        <?php endif; ?>
    </div>
    <div class="nav-right">
        <?php if ($logged_in): ?>
            <div class="user-info">
                <img src="../images/P2.png" alt="Immagine Utente" class="user-img">
                <?php
                $nome = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Nome non disponibile';
                $cognome = isset($_SESSION['cognome']) ? $_SESSION['cognome'] : 'Cognome non disponibile';
                echo htmlspecialchars($nome) . ' ' . htmlspecialchars($cognome);
                ?>
                <a href="../utenti/logout.php" class="logout">Esci</a>
            </div>
        <?php else: ?>
            <a href="../utenti/registrazione.html" class="btn btn-outline-primary me-2">Registrati</a>
            <a href="../utenti/accedi.html" class="btn btn-outline-success">Accedi</a>
        <?php endif; ?>
    </div>
</div>

