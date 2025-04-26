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

// Verifica se il form è stato inviato
if ($_SERVER["REQUEST_METHOD"] == "POST")
{
    // Prendi i dati dal form
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $nazionalita = $_POST['nazionalita'];
    $lingua_base = $_POST['lingua_base'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];
    $password = $_POST['password'];

    // Hash della password
    $hash = password_hash($password, PASSWORD_DEFAULT);

    try
    {
        // Verifica se l'email esiste già nel database
        $query_check = "SELECT COUNT(*) FROM visitatori WHERE email = :email";
        $stmt_check = $db->prepare($query_check);
        $stmt_check->bindParam(':email', $email);
        $stmt_check->execute();

        // Controlla il numero di risultati
        $existingUser = $stmt_check->fetchColumn();

        if ($existingUser > 0)
        {
            // Imposta il messaggio di errore invece di fare il redirect
            $error_message = "L'email inserita è già registrata nel sistema. Utilizzare un'altra email o effettuare il login.";
        }
        else
        {
            // Query SQL per inserire i dati nel database
            $query = "INSERT INTO visitatori (nome, cognome, nazionalita, lingua_base, email, telefono, password) 
                      VALUES (:nome, :cognome, :nazionalita, :lingua_base, :email, :telefono, :password)";

            // Prepara la query
            $stmt = $db->prepare($query);

            // Lega i parametri
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':cognome', $cognome);
            $stmt->bindParam(':nazionalita', $nazionalita);
            $stmt->bindParam(':lingua_base', $lingua_base);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':password', $hash);

            // Esegui la query
            $stmt->execute();

            // Reindirizza alla pagina principale
            header("Location: ../pages/home.php");
            exit;
        }
    }
    catch (PDOException $e)
    {
        logError($e);
        $error_message = "Si è verificato un errore durante la registrazione. Riprova più tardi.";
    }
}
include 'registrazione_form.php';
?>