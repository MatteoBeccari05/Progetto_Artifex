<?php
session_start();
// Configurazione del DB
$config = require '../connessione_db/db_config.php';
require '../connessione_db/DB_Connect.php';
require_once '../connessione_db/functions.php';

// Connessione al DB
$db = DataBase_Connect::getDB($config);

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $email = $_POST['email'];
    $password = $_POST['password'];

    try
    {
        // Query per ottenere i dati dell'utente dalla tabella visitatori
        $query = "SELECT * FROM visitatori WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user)
        {
            // Verifica della password
            if (password_verify($password, $user['password']))
            {
                // Creazione della sessione
                $_SESSION['email'] = $email;
                $_SESSION['nome'] = $user['nome'];
                $_SESSION['cognome'] = $user['cognome'];
                $_SESSION['nazionalita'] = $user['nazionalita'];
                $_SESSION['lingua_base'] = $user['lingua_base'];

                // Reindirizza alla home
                header("Location: ../pages/home.php");
                exit;
            }
            else
            {
                // Password errata, reindirizza alla pagina di errore
                header("Location: ../redirect/error_password.html");
                exit;
            }
        }
        else
        {
            // Utente non trovato, reindirizza alla pagina di errore
            header("Location: ../redirect/error_password.html");
            exit;
        }
    }
    catch (PDOException $e)
    {
        logError($e);
    }
}
?>
