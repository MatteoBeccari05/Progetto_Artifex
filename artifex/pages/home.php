<?php

$content = 'ArtiFex';
require_once '../strutture_pagina/functions_active_navbar.php';
require '../strutture_pagina/navbar.php';
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
    <title>Home</title>
</head>
<body>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content fadeIn">
        <h1>Scopri l'Arte e la Storia con Artifex</h1>
        <p>Visite guidate ai più affascinanti siti di interesse storico-culturale in Italia e nel mondo</p>
        <div class="hero-cta">
            <a href="eventi.php" class="btn btn-primary">Esplora le Visite</a>
            <a href="about.php" class="btn btn-outline">Chi Siamo</a>
        </div>
    </div>
</section>

<!-- Featured Tours Section -->
<section class="section">
    <div class="section-header">
        <h2 class="section-title">Le Nostre Visite Guidate</h2>
        <p class="section-subtitle">Scopri i nostri itinerari più popolari selezionati dai migliori esperti del settore</p>
    </div>

    <div class="tours-grid">
        <!-- Tour Card 1 -->
        <div class="tour-card">
            <img src="../images/vatican.jpg" alt="Musei Vaticani" class="tour-image">
            <div class="tour-content">
                <span class="tour-category">Roma</span>
                <h3 class="tour-title">Musei Vaticani e Cappella Sistina</h3>
                <div class="tour-info">
                    <span><i class="fa-regular fa-clock"></i> 3 ore</span>
                    <span><i class="fa-solid fa-user-group"></i> Max 20 persone</span>
                </div>
                <p class="tour-description">Un viaggio attraverso secoli di arte e storia nei prestigiosi Musei Vaticani, culminando con la visita alla magnifica Cappella Sistina.</p>
                <div class="tour-footer">
                    <span class="tour-price">€35 / persona</span>
                    <a href="dettaglio_visita.php?id=1" class="btn btn-primary">Dettagli</a>
                </div>
            </div>
        </div>

        <!-- Tour Card 2 -->
        <div class="tour-card">
            <img src="../images/pompeii.jpg" alt="Pompei" class="tour-image">
            <div class="tour-content">
                <span class="tour-category">Napoli</span>
                <h3 class="tour-title">Sito Archeologico di Pompei</h3>
                <div class="tour-info">
                    <span><i class="fa-regular fa-clock"></i> 4 ore</span>
                    <span><i class="fa-solid fa-user-group"></i> Max 15 persone</span>
                </div>
                <p class="tour-description">Esplora l'antica città romana perfettamente conservata sotto le ceneri dell'eruzione del Vesuvio del 79 d.C.</p>
                <div class="tour-footer">
                    <span class="tour-price">€45 / persona</span>
                    <a href="dettaglio_visita.php?id=2" class="btn btn-primary">Dettagli</a>
                </div>
            </div>
        </div>

        <!-- Tour Card 3 -->
        <div class="tour-card">
            <img src="../images/uffizi.jpg" alt="Galleria degli Uffizi" class="tour-image">
            <div class="tour-content">
                <span class="tour-category">Firenze</span>
                <h3 class="tour-title">Galleria degli Uffizi</h3>
                <div class="tour-info">
                    <span><i class="fa-regular fa-clock"></i> 2.5 ore</span>
                    <span><i class="fa-solid fa-user-group"></i> Max 12 persone</span>
                </div>
                <p class="tour-description">Una delle più celebri pinacoteche al mondo con capolavori di Botticelli, Leonardo, Michelangelo, Raffaello e molti altri.</p>
                <div class="tour-footer">
                    <span class="tour-price">€40 / persona</span>
                    <a href="dettaglio_visita.php?id=3" class="btn btn-primary">Dettagli</a>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-3">
        <a href="visite.php" class="btn btn-outline">Vedi Tutte le Visite</a>
    </div>
</section>

<!-- How It Works Section -->
<section class="features">
    <div class="features-container">
        <div class="section-header">
            <h2 class="section-title">Come Funziona</h2>
            <p class="section-subtitle">Prenotare una visita guidata con Artifex è semplice e veloce</p>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3 class="feature-title">Scegli la Tua Visita</h3>
                <p>Sfoglia la nostra selezione di visite guidate ai più importanti siti storico-culturali e scegli quella che ti interessa.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-calendar-check"></i>
                </div>
                <h3 class="feature-title">Seleziona la Data</h3>
                <p>Ogni visita ha diverse date disponibili. Seleziona quella più comoda per te e aggiungi al carrello.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <h3 class="feature-title">Prenota e Paga</h3>
                <p>Completa il checkout e ricevi il tuo biglietto con QR code direttamente via email, pronto da stampare.</p>
            </div>

            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-route"></i>
                </div>
                <h3 class="feature-title">Vivi l'Esperienza</h3>
                <p>Presentati all'orario stabilito e incontra la tua guida che ti accompagnerà in un viaggio attraverso arte e storia.</p>
            </div>
        </div>
    </div>
</section>

<!-- Upcoming Events Section -->
<section class="section">
    <div class="section-header">
        <h2 class="section-title">Prossimi Eventi</h2>
        <p class="section-subtitle">Gli eventi in programma nelle prossime settimane</p>
    </div>

    <div class="tours-grid">
        <!-- Event Card 1 -->
        <div class="tour-card">
            <div class="tour-content">
                <span class="event-date">26 Aprile 2025 • 10:00</span>
                <h3 class="tour-title">Musei Vaticani e Cappella Sistina</h3>
                <div class="event-details">
                    <div class="event-detail">
                        <i class="fa-solid fa-language detail-icon"></i>
                        <span>Lingua: Italiano</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-user detail-icon"></i>
                        <span>Guida: Marco Bianchi</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-users detail-icon"></i>
                        <span>Posti disponibili: 12/20</span>
                    </div>
                </div>
                <div class="tour-footer">
                    <span class="tour-price">€35 / persona</span>
                    <a href="prenota.php?evento=101" class="btn btn-primary">Prenota</a>
                </div>
            </div>
        </div>

        <!-- Event Card 2 -->
        <div class="tour-card">
            <div class="tour-content">
                <span class="event-date">27 Aprile 2025 • 09:30</span>
                <h3 class="tour-title">Sito Archeologico di Pompei</h3>
                <div class="event-details">
                    <div class="event-detail">
                        <i class="fa-solid fa-language detail-icon"></i>
                        <span>Lingua: Inglese</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-user detail-icon"></i>
                        <span>Guida: Sofia Rossi</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-users detail-icon"></i>
                        <span>Posti disponibili: 8/15</span>
                    </div>
                </div>
                <div class="tour-footer">
                    <span class="tour-price">€45 / persona</span>
                    <a href="prenota.php?evento=102" class="btn btn-primary">Prenota</a>
                </div>
            </div>
        </div>

        <!-- Event Card 3 -->
        <div class="tour-card">
            <div class="tour-content">
                <span class="event-date">30 Aprile 2025 • 14:00</span>
                <h3 class="tour-title">Galleria degli Uffizi</h3>
                <div class="event-details">
                    <div class="event-detail">
                        <i class="fa-solid fa-language detail-icon"></i>
                        <span>Lingua: Italiano</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-user detail-icon"></i>
                        <span>Guida: Laura Verdi</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-users detail-icon"></i>
                        <span>Posti disponibili: 5/12</span>
                    </div>
                </div>
                <div class="tour-footer">
                    <span class="tour-price">€40 / persona</span>
                    <a href="prenota.php?evento=103" class="btn btn-primary">Prenota</a>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-3">
        <a href="eventi.php" class="btn btn-outline">Vedi Tutti gli Eventi</a>
    </div>
</section>

<!-- Guide Section -->
<section class="section">
    <div class="section-header">
        <h2 class="section-title">Le Nostre Guide</h2>
        <p class="section-subtitle">Professionisti esperti e multilingue per accompagnarti alla scoperta del patrimonio culturale</p>
    </div>

    <div class="tours-grid">
        <!-- Guide Card 1 -->
        <div class="tour-card">
            <img src="../images/guide1.jpg" alt="Marco Bianchi" class="tour-image">
            <div class="tour-content">
                <h3 class="tour-title">Marco Bianchi</h3>
                <div class="event-details">
                    <div class="event-detail">
                        <i class="fa-solid fa-graduation-cap detail-icon"></i>
                        <span>Laurea in Storia dell'Arte</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-language detail-icon"></i>
                        <span>Italiano (madrelingua), Inglese (avanzato)</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-location-dot detail-icon"></i>
                        <span>Roma</span>
                    </div>
                </div>
                <p class="tour-description">Specializzato in arte rinascimentale e barocca, Marco vi accompagnerà alla scoperta dei più importanti siti culturali di Roma.</p>
            </div>
        </div>

        <!-- Guide Card 2 -->
        <div class="tour-card">
            <img src="../images/guide2.jpg" alt="Sofia Rossi" class="tour-image">
            <div class="tour-content">
                <h3 class="tour-title">Sofia Rossi</h3>
                <div class="event-details">
                    <div class="event-detail">
                        <i class="fa-solid fa-graduation-cap detail-icon"></i>
                        <span>Dottorato in Archeologia</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-language detail-icon"></i>
                        <span>Italiano (madrelingua), Inglese (madrelingua), Spagnolo (avanzato)</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-location-dot detail-icon"></i>
                        <span>Napoli</span>
                    </div>
                </div>
                <p class="tour-description">Con oltre 10 anni di esperienza negli scavi di Pompei, Sofia è la guida ideale per scoprire i segreti dell'antica città romana.</p>
            </div>
        </div>

        <!-- Guide Card 3 -->
        <div class="tour-card">
            <img src="../images/guide3.jpg" alt="Laura Verdi" class="tour-image">
            <div class="tour-content">
                <h3 class="tour-title">Laura Verdi</h3>
                <div class="event-details">
                    <div class="event-detail">
                        <i class="fa-solid fa-graduation-cap detail-icon"></i>
                        <span>Laurea in Storia dell'Arte e Conservazione</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-language detail-icon"></i>
                        <span>Italiano (madrelingua), Francese (avanzato), Tedesco (normale)</span>
                    </div>
                    <div class="event-detail">
                        <i class="fa-solid fa-location-dot detail-icon"></i>
                        <span>Firenze</span>
                    </div>
                </div>
                <p class="tour-description">Esperta d'arte rinascimentale fiorentina, Laura vi svelerà i segreti dei capolavori custoditi nella Galleria degli Uffizi.</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials">
    <div class="section-header">
        <h2 class="section-title">Cosa Dicono i Nostri Clienti</h2>
        <p class="section-subtitle">Le esperienze di chi ha scelto le nostre visite guidate</p>
    </div>

    <div class="testimonials-grid">
        <div class="testimonial-card">
            <p class="testimonial-content">La visita ai Musei Vaticani con Marco è stata un'esperienza indimenticabile. La sua conoscenza e passione hanno reso ogni opera d'arte ancora più speciale.</p>
            <div class="testimonial-author">
                <img src="../images/user1.jpg" alt="Giulia Marino" class="author-image">
                <div class="author-info">
                    <h4>Giulia Marino</h4>
                    <p>Roma, Italia</p>
                </div>
            </div>
        </div>

        <div class="testimonial-card">
            <p class="testimonial-content">Sofia ci ha fatto viaggiare nel tempo a Pompei, raccontando storie affascinanti sulla vita quotidiana degli antichi romani. Consigliatissima!</p>
            <div class="testimonial-author">
                <img src="../images/user2.jpg" alt="John Smith" class="author-image">
                <div class="author-info">
                    <h4>John Smith</h4>
                    <p>Londra, Regno Unito</p>
                </div>
            </div>
        </div>

        <div class="testimonial-card">
            <p class="testimonial-content">La Galleria degli Uffizi è magnifica, ma l'esperienza con Laura come guida l'ha resa ancora più straordinaria. La sua conoscenza dell'arte rinascimentale è impressionante.</p>
            <div class="testimonial-author">
                <img src="../images/user3.jpg" alt="Marie Dupont" class="author-image">
                <div class="author-info">
                    <h4>Marie Dupont</h4>
                    <p>Parigi, Francia</p>
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
        <form class="newsletter-form">
            <input type="email" placeholder="La tua email" class="newsletter-input" required>
            <button type="submit" class="newsletter-btn">Iscriviti</button>
        </form>
    </div>
</section>

<?php
require '../strutture_pagina/footer.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle mobile menu
        const navbarToggle = document.querySelector('.navbar-toggle');
        const navbarLinks = document.querySelector('.navbar-links');

        if (navbarToggle && navbarLinks) {
            navbarToggle.addEventListener('click', function() {
                navbarLinks.classList.toggle('active');
            });
        }
    });
</script>

</body>
</html>