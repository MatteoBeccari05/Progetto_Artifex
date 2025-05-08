<?php
// Configurazione del DB
$config = require '../connessione_db/db_config.php';
require '../connessione_db/DB_Connect.php';
require_once '../connessione_db/functions.php';
require('../vendor/tecnickcom/tcpdf/tcpdf.php');

// Avvia la sessione se non è già attiva
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se l'utente è loggato
if (!isset($_SESSION['user_id'])) {
    header('Location: ../utenti/accedi_form.php');
    exit;
}

$id_biglietto = $_GET['id'];
$id_visitatore = $_SESSION['user_id'];

// Connessione al DB
$db = DataBase_Connect::getDB($config);

// Ottieni i dettagli del biglietto
$query = "SELECT b.id, b.codice_qr, e.data_evento, e.ora_inizio, e.lingua,
          v.titolo AS nome_visita, v.luogo, v.descrizione, 
          o.id AS id_ordine, vis.nome, vis.cognome, g.nome AS nome_guida, g.cognome AS cognome_guida
          FROM biglietti b
          JOIN eventi e ON b.id_evento = e.id
          JOIN visite v ON e.id_visita = v.id
          JOIN guide g ON e.id_guida = g.id
          JOIN ordini o ON b.id_ordine = o.id
          JOIN visitatori vis ON b.id_visitatore = vis.id
          WHERE b.id = ? AND b.id_visitatore = ?";
$stmt = $db->prepare($query);
$stmt->execute([$id_biglietto, $id_visitatore]);
$biglietto = $stmt->fetch(PDO::FETCH_ASSOC);

// Formatta data e ora
$data_formattata = date('d/m/Y', strtotime($biglietto['data_evento']));
$ora_formattata = date('H:i', strtotime($biglietto['ora_inizio']));

// Genera il contenuto del QR Code
$qr_content = "Biglietto: " . $biglietto['id'] . "\n";
$qr_content .= "Visita: " . $biglietto['nome_visita'] . "\n";
$qr_content .= "Luogo: " . $biglietto['luogo'] . "\n";
$qr_content .= "Data: " . $data_formattata . "\n";
$qr_content .= "Ora: " . $ora_formattata . "\n";
$qr_content .= "Lingua: " . $biglietto['lingua'] . "\n";
$qr_content .= "Guida: " . $biglietto['nome_guida'] . " " . $biglietto['cognome_guida'] . "\n";
$qr_content .= "Visitatore: " . $biglietto['nome'] . " " . $biglietto['cognome'];

// Crea il PDF
class MYPDF extends TCPDF {
    public function Header() {
        // Logo
        $image_file = '../assets/images/logo.png';
        if (file_exists($image_file)) {
            $this->Image($image_file, 10, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }

        // Titolo
        $this->SetY(15);
        $this->SetFont('helvetica', 'B', 20);
        $this->SetTextColor(44, 62, 80);
        $this->Cell(0, 15, 'ARTIFEX', 0, false, 'C', 0, '', 0, false, 'M', 'M');

        // Linea decorativa
        $this->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(52, 152, 219)));
        $this->Line(10, 30, $this->getPageWidth() - 10, 30);
    }

    public function Footer() {
        // Posizione a 15 mm dal basso
        $this->SetY(-15);
        // Font
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor(128);
        // Info piè di pagina
        $this->Cell(0, 10, 'Artifex - Tour Esclusivi e Visite Guidate', 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->Ln(4);
        $this->Cell(0, 10, 'Pagina '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Crea l'oggetto PDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Imposta le informazioni del documento
$pdf->SetCreator('Artifex');
$pdf->SetAuthor('Artifex');
$pdf->SetTitle('Biglietto - ' . $biglietto['nome_visita']);
$pdf->SetSubject('Biglietto per visita guidata');
$pdf->SetKeywords('Artifex, biglietto, visita guidata, tour');

// Imposta margini
$pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// Imposta l'auto page break
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// Imposta il fattore di scala dell'immagine
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// Aggiungi una pagina
$pdf->AddPage();

// Sfondo sfumato
$pdf->SetFillColor(240, 248, 255); // Azzurro molto chiaro
$pdf->Rect(0, 0, $pdf->getPageWidth(), $pdf->getPageHeight(), 'F');

// Imposta il colore e il font per il titolo del biglietto
$pdf->SetFont('helvetica', 'B', 24);
$pdf->SetTextColor(41, 128, 185); // Blu
$pdf->Cell(0, 20, 'BIGLIETTO DI INGRESSO', 0, 1, 'C');

// Aggiungi un'area per i dettagli della visita
$pdf->Ln(5);
$pdf->SetFillColor(255, 255, 255);
$pdf->SetLineWidth(0.1);
$pdf->SetDrawColor(189, 195, 199);

// Titolo visita
$pdf->SetFont('helvetica', 'B', 18);
$pdf->SetTextColor(44, 62, 80);
$pdf->Cell(0, 15, $biglietto['nome_visita'], 1, 1, 'C', 1);

// Dettagli visita
$pdf->SetFont('helvetica', '', 14);
$pdf->SetTextColor(52, 73, 94);

// Luogo
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(40, 10, 'Luogo:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, $biglietto['luogo'], 0, 1, 'L');

// Data e ora
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(40, 10, 'Data:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(60, 10, $data_formattata, 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(30, 10, 'Ora:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, $ora_formattata, 0, 1, 'L');

// Lingua
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(40, 10, 'Lingua:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, $biglietto['lingua'], 0, 1, 'L');

// Guida
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(40, 10, 'Guida:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, $biglietto['nome_guida'] . ' ' . $biglietto['cognome_guida'], 0, 1, 'L');

// Visitatore
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Dati Visitatore', 0, 1, 'L');

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(40, 10, 'Nome:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(60, 10, $biglietto['nome'], 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(40, 10, 'Cognome:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, $biglietto['cognome'], 0, 1, 'L');

// Numero biglietto e ordine
$pdf->Ln(5);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(40, 10, 'Biglietto N°:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(60, 10, $biglietto['id'], 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(40, 10, 'Ordine N°:', 0, 0, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, $biglietto['id_ordine'], 0, 1, 'L');

// Aggiungi QR Code
$pdf->Ln(10);
$style = array(
    'border' => true,
    'padding' => 4,
    'fgcolor' => array(0, 0, 0),
    'bgcolor' => array(255, 255, 255)
);
$pdf->write2DBarcode($qr_content, 'QRCODE,M', 75, 180, 60, 60, $style, 'N');

// Etichetta QR Code
$pdf->SetY(245);
$pdf->SetFont('helvetica', 'I', 10);
$pdf->SetTextColor(128);
$pdf->Cell(0, 10, 'Scansiona questo codice QR all\'ingresso', 0, 1, 'C');

// Note informative
$pdf->SetY(255);
$pdf->SetFont('helvetica', 'I', 9);
$pdf->SetTextColor(149, 165, 166);
$pdf->MultiCell(0, 10, 'Questo biglietto deve essere mostrato all\'ingresso, in formato digitale o cartaceo. Si prega di arrivare almeno 15 minuti prima dell\'inizio della visita. Per assistenza contattare support@artifex.it', 0, 'C', 0, 1, '', '', true);

// Elemento decorativo
$pdf->SetDrawColor(52, 152, 219);
$pdf->SetLineWidth(1);
$pdf->Line(10, 270, $pdf->getPageWidth() - 10, 270);

// Output del PDF
$pdf->Output('Biglietto_' . $id_biglietto . '.pdf', 'I');
?>