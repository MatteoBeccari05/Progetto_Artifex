<?php
session_start();
$config = require '../connessione_db/db_config.php';
require '../connessione_db/DB_Connect.php';
require_once '../connessione_db/functions.php';

$error_message = "";

$db = DataBase_Connect::getDB($config);

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    $username = $_POST['username'];
    $password = $_POST['password'];

    try
    {
        // Query per ottenere i dati dell'amministratore
        $query = "SELECT * FROM amministratori WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin)
        {
            if ($password == $admin['password'])
            {
                // Creazione della sessione
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['email'] = $admin['email'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['ruolo'] = 'admin'; // Imposta il ruolo come admin

                // Reindirizza alla home
                header("Location: ../pages/home.php");
                exit;
            }
            else
            {
                $error_message = "Password non corretta. Riprova.";
            }
        }
        else
        {
            $error_message = "Username non trovato. Verifica le credenziali.";
        }
    }
    catch (PDOException $e)
    {
        logError($e);
        $error_message = "Si è verificato un errore durante l'accesso. Riprova più tardi.";
    }
}

include 'admin_accedi_form.php';
?>