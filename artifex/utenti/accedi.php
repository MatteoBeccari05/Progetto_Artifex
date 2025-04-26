<?php
session_start();
// Configurazione del DB
$config = require '../connessione_db/db_config.php';
require '../connessione_db/DB_Connect.php';
require_once '../connessione_db/functions.php';

// Inizializza la variabile di errore
$error_message = "";

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
                $_SESSION['user_id'] = $user['id'];
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
                // Password errata, mostra messaggio nella pagina
                $error_message = "Password non corretta. Riprova.";
            }
        }
        else
        {
            // Utente non trovato, mostra messaggio nella pagina
            $error_message = "Email non trovata. Verifica l'indirizzo email o registrati.";
        }
    }
    catch (PDOException $e)
    {
        logError($e);
        $error_message = "Si è verificato un errore durante l'accesso. Riprova più tardi.";
    }
}

include 'accedi_form.php';
?>