<?php
require_once 'functions_active_navbar.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$logged_in = isset($_SESSION['nome']) || isset($_SESSION['username']); // L'utente è loggato se la variabile di sessione esiste
$is_admin = isset($_SESSION['ruolo']) && $_SESSION['ruolo'] == 'admin'; // Verifica se è un admin
?>

<div class="navbar">
    <div class="nav-left">
        <a href="home.php" class="<?= isActive('home.php') ?>">Home</a>
        <a href="servizi.php" class="<?= isActive('servizi.php') ?>">Tour Guidati</a>
        <a href="about.php" class="<?= isActive('about.php') ?>">Chi siamo</a>

        <?php if ($is_admin): ?>
            <!-- Sezioni visibili solo agli amministratori -->
            <a href="gestione.php" class="<?= isActive('gestione.php') ?>">Gestione</a>
        <?php endif; ?>
    </div>
    <div class="nav-right">
        <?php if ($logged_in): ?>
            <div class="user-info">
                <?php if ($is_admin): ?>
                    <!-- Info amministratore -->
                    <a href="../pages/admin_profilo.php" class="<?= isActive('admin_profilo.php') ?>">
                        Admin: <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                <?php else: ?>
                    <!-- Info visitatore -->
                    <a href="../pages/profilo.php" class="<?= isActive('profilo.php') ?>">
                        <?php
                        $nome = isset($_SESSION['nome']) ? $_SESSION['nome'] : 'Nome non disponibile';
                        $cognome = isset($_SESSION['cognome']) ? $_SESSION['cognome'] : 'Cognome non disponibile';
                        echo htmlspecialchars($nome) . ' ' . htmlspecialchars($cognome);
                        ?>
                    </a>
                <?php endif; ?>
                <a href="../utenti/logout.php" class="logout">Esci</a>
            </div>
        <?php else: ?>
            <a href="../utenti/registrazione_form.php" class="btn btn-outline-primary me-2">Registrati</a>
            <a href="../utenti/accedi_form.php" class="btn btn-outline-success">Accedi</a>
            <a href="../utenti/admin_accedi_form.php" class="btn btn-outline-danger">Area Admin</a>
        <?php endif; ?>
    </div>
</div>