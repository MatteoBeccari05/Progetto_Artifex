<?php

$content = 'About Us';
require_once '../strutture_pagina/functions_active_navbar.php';
require '../strutture_pagina/navbar.php';

// Configurazione del DB
$config = require '../connessione_db/db_config.php';
require '../connessione_db/DB_Connect.php';
require_once '../connessione_db/functions.php';

// Connessione al DB
$db = DataBase_Connect::getDB($config);

// Function to get some statistics
function getStatsAboutUs($db) {
    // Get number of tours
    $stmt = $db->prepare("SELECT COUNT(*) as tour_count FROM visite");
    $stmt->execute();
    $tourCount = $stmt->fetch(PDO::FETCH_ASSOC)['tour_count'];

    // Get number of guides
    $stmt = $db->prepare("SELECT COUNT(*) as guide_count FROM guide");
    $stmt->execute();
    $guideCount = $stmt->fetch(PDO::FETCH_ASSOC)['guide_count'];

    // Get number of locations
    $stmt = $db->prepare("SELECT COUNT(DISTINCT luogo) as location_count FROM visite");
    $stmt->execute();
    $locationCount = $stmt->fetch(PDO::FETCH_ASSOC)['location_count'];

    // Get number of languages offered
    $stmt = $db->prepare("SELECT COUNT(DISTINCT lingua) as language_count FROM competenze_linguistiche");
    $stmt->execute();
    $languageCount = $stmt->fetch(PDO::FETCH_ASSOC)['language_count'];

    return [
        'tour_count' => $tourCount,
        'guide_count' => $guideCount,
        'location_count' => $locationCount,
        'language_count' => $languageCount
    ];
}

$stats = getStatsAboutUs($db);

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Chi Siamo - ArtiFex</title>
</head>
<body>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content fadeIn">
        <h1>Chi Siamo</h1>
        <p>Scopri la nostra storia, la nostra missione e le persone che rendono possibile ArtiFex</p>
    </div>
</section>

<!-- Our Story Section -->
<section class="section">
    <div class="section-header">
        <h2 class="section-title">La Nostra Storia</h2>
        <p class="section-subtitle">Come è nato ArtiFex e il nostro percorso fino ad oggi</p>
    </div>

    <div class="about-content">
        <div class="about-text">
            <p>Fondato nel 2018 da un gruppo di storici dell'arte e guide turistiche appassionate, ArtiFex è nato con l'obiettivo di rendere il patrimonio culturale italiano accessibile a tutti attraverso esperienze di visita guidata di alta qualità.</p>

            <p>Il nome "ArtiFex" deriva dal latino e significa "artista" o "artigiano", rappresentando perfettamente la nostra missione: creare esperienze culturali su misura, realizzate con la cura e l'attenzione di un artigiano.</p>

            <p>Ciò che è iniziato come un piccolo progetto a Roma si è rapidamente espanso, e oggi offriamo visite guidate in numerose città italiane e in alcune delle destinazioni culturali più importanti d'Europa.</p>

            <p>Nel corso degli anni, abbiamo collaborato con decine di musei, siti archeologici e istituzioni culturali, costruendo una rete di professionisti qualificati e appassionati che condividono il nostro amore per l'arte, la storia e la cultura.</p>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stats-card">
                <div class="stats-content">
                    <i class="fa-solid fa-route stats-icon"></i>
                    <h3><?php echo $stats['tour_count']; ?></h3>
                    <p>Visite Guidate</p>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-content">
                    <i class="fa-solid fa-user-tie stats-icon"></i>
                    <h3><?php echo $stats['guide_count']; ?></h3>
                    <p>Guide Esperte</p>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-content">
                    <i class="fa-solid fa-location-dot stats-icon"></i>
                    <h3><?php echo $stats['location_count']; ?></h3>
                    <p>Località</p>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-content">
                    <i class="fa-solid fa-language stats-icon"></i>
                    <h3><?php echo $stats['language_count']; ?></h3>
                    <p>Lingue Disponibili</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section class="features">
    <div class="features-container">
        <div class="section-header">
            <h2 class="section-title">La Nostra Missione</h2>
            <p class="section-subtitle">I valori che guidano il nostro lavoro ogni giorno</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-book-open"></i>
                </div>
                <h3 class="feature-title">Conoscenza</h3>
                <p>Condividere la conoscenza culturale in modo accessibile e coinvolgente per tutti i tipi di pubblico, dai principianti agli esperti.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h3 class="feature-title">Inclusività</h3>
                <p>Rendere l'arte e la cultura accessibili a persone di tutte le età, background e abilità, con particolare attenzione all'accessibilità.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-medal"></i>
                </div>
                <h3 class="feature-title">Eccellenza</h3>
                <p>Garantire la massima qualità in ogni visita guidata, con guide esperte e appassionate che offrono esperienze indimenticabili.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-landmark"></i>
                </div>
                <h3 class="feature-title">Conservazione</h3>
                <p>Contribuire alla conservazione e valorizzazione del patrimonio culturale attraverso la sensibilizzazione e una parte dei nostri ricavi.</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="section">
    <div class="section-header">
        <h2 class="section-title">Il Nostro Team</h2>
        <p class="section-subtitle">Le persone che rendono possibile ArtiFex</p>
    </div>

    <div class="tours-grid">
        <div class="tour-card">
            <div class="tour-content">
                <h3 class="tour-title">Maria Rossi</h3>
                <div class="event-details">
                    <div class="event-detail">
                        <i class="fa-solid fa-graduation-cap detail-icon"></i>
                        <span>Fondatrice e CEO</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-graduation-cap detail-icon"></i>
                        <span>Laurea in Storia dell'Arte</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-language detail-icon"></i>
                        <span>Italiano, Inglese, Francese</span>
                    </div>
                </div>
                <p class="tour-description">Storica dell'arte con oltre 15 anni di esperienza nel settore culturale. Ha fondato ArtiFex con la visione di rendere il patrimonio culturale accessibile a tutti.</p>
            </div>
        </div>

        <div class="tour-card">
            <div class="tour-content">
                <h3 class="tour-title">Marco Bianchi</h3>
                <div class="event-details">
                    <div class="event-detail">
                        <i class="fa-solid fa-graduation-cap detail-icon"></i>
                        <span>Direttore Operativo</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-graduation-cap detail-icon"></i>
                        <span>MBA in Gestione Culturale</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-language detail-icon"></i>
                        <span>Italiano, Inglese, Spagnolo</span>
                    </div>
                </div>
                <p class="tour-description">Con esperienza nella gestione di istituzioni culturali, Marco supervisiona tutte le operazioni di ArtiFex, garantendo esperienze di alta qualità per ogni cliente.</p>
            </div>
        </div>

        <div class="tour-card">
            <div class="tour-content">
                <h3 class="tour-title">Giulia Verdi</h3>
                <div class="event-details">
                    <div class="event-detail">
                        <i class="fa-solid fa-graduation-cap detail-icon"></i>
                        <span>Responsabile Guide</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-graduation-cap detail-icon"></i>
                        <span>Dottorato in Archeologia</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-language detail-icon"></i>
                        <span>Italiano, Inglese, Tedesco</span>
                    </div>
                </div>
                <p class="tour-description">Giulia seleziona e forma le nostre guide, assicurandosi che ogni membro del team abbia le conoscenze e le capacità necessarie per offrire visite guidate eccezionali.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section">
    <div class="section-header">
        <h2 class="section-title">Cosa Dicono di Noi</h2>
        <p class="section-subtitle">Le opinioni di chi ha vissuto l'esperienza ArtiFex</p>
    </div>

    <div class="testimonials-grid">
        <div class="testimonial-card">
            <div class="testimonial-content">
                "La visita guidata con ArtiFex al Colosseo è stata un'esperienza straordinaria. La nostra guida era estremamente preparata e ha saputo rendere la storia romana accessibile e affascinante anche per i nostri figli. Consigliatissimo!"
            </div>
            <div class="testimonial-author">
                <div class="author-info">
                    <h4>Famiglia Martini</h4>
                    <p>Tour del Colosseo e Foro Romano</p>
                </div>
            </div>
        </div>

        <div class="testimonial-card">
            <div class="testimonial-content">
                "Ho partecipato alla visita guidata agli Uffizi con ArtiFex e sono rimasto impressionato dalla profondità delle conoscenze e dalla passione della guida. Molto più che una semplice visita guidata, è stata una vera e propria lezione di storia dell'arte."
            </div>
            <div class="testimonial-author">
                <div class="author-info">
                    <h4>Giorgio P.</h4>
                    <p>Tour della Galleria degli Uffizi</p>
                </div>
            </div>
        </div>

        <div class="testimonial-card">
            <div class="testimonial-content">
                "Come insegnante, apprezzo particolarmente l'approccio educativo di ArtiFex. Ho organizzato una visita per la mia classe a Pompei e i ragazzi sono rimasti entusiasti. La guida ha saputo coinvolgerli con aneddoti interessanti e attività interattive."
            </div>
            <div class="testimonial-author">
                <div class="author-info">
                    <h4>Prof.ssa Lucia T.</h4>
                    <p>Tour scolastico di Pompei</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter">
    <div class="newsletter-container">
        <h2>Resta Aggiornato</h2>
        <p>Iscriviti alla nostra newsletter per ricevere in anteprima le nuove visite guidate e offerte speciali</p>
        <form class="newsletter-form" action="#" method="post">
            <input type="email" name="email" placeholder="La tua email" class="newsletter-input" required>
            <button type="submit" class="newsletter-btn">Iscriviti</button>
        </form>
    </div>
</section>

<?php
require '../strutture_pagina/footer.php';
?>


</body>
</html>