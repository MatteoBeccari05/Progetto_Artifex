create database artifex;
use artifex;

create table amministratori (
    id int auto_increment primary key,
    username varchar(50) not null unique,
    password varchar(255) not null,
    email varchar(100) not null unique
);

create table visitatori (
    id int auto_increment primary key,
    nome varchar(50) not null,
    cognome varchar(50) not null,
    nazionalita varchar(50) not null,
    lingua_base varchar(30) not null,
    email varchar(100) not null unique,
    telefono varchar(20) not null,
    password varchar(255) not null
);

create table guide (
    id int auto_increment primary key,
    nome varchar(50) not null,
    cognome varchar(50) not null,
    data_nascita date not null,
    luogo_nascita varchar(100) not null,
    titolo_studio varchar(100) not null
);

create table competenze_linguistiche (
    id int auto_increment primary key,
    id_guida int not null,
    lingua varchar(30) not null,
    livello enum('normale', 'avanzato', 'madrelingua') not null,
    foreign key (id_guida) references guide(id)
);

create table visite (
    id int auto_increment primary key,
    titolo varchar(100) not null,
    descrizione text not null,
    durata_media int not null,
    luogo varchar(100) not null
);

create table eventi (
    id int auto_increment primary key,
    id_visita int not null,
    id_guida int not null,
    data_evento date not null,
    ora_inizio time not null,
    lingua varchar(30) not null,
    prezzo decimal(10,2) not null,
    min_partecipanti int not null,
    max_partecipanti int not null,
    foreign key (id_visita) references visite(id),
    foreign key (id_guida) references guide(id)
);

create table carrelli (
    id int auto_increment primary key,
    id_visitatore int not null,
    stato enum('attivo', 'pagato', 'annullato') default 'attivo',
    data_creazione timestamp default current_timestamp,
    foreign key (id_visitatore) references visitatori(id)
);

create table elementi_carrello (
    id int auto_increment primary key,
    id_carrello int not null,
    id_evento int not null,
    quantita int not null default 1,
    foreign key (id_carrello) references carrelli(id),
    foreign key (id_evento) references eventi(id)
);

create table ordini (
    id int auto_increment primary key,
    id_visitatore int not null,
    id_carrello int not null,
    data_ordine timestamp default current_timestamp,
    importo_totale decimal(10,2) not null,
    foreign key (id_visitatore) references visitatori(id),
    foreign key (id_carrello) references carrelli(id)
);

create table biglietti (
    id int auto_increment primary key,
    id_ordine int not null,
    id_evento int not null,
    id_visitatore int not null,
    codice_qr varchar(255) not null,
    data_emissione timestamp default current_timestamp,
    stato enum('valido', 'utilizzato', 'annullato') default 'valido',
    foreign key (id_ordine) references ordini(id),
    foreign key (id_evento) references eventi(id),
    foreign key (id_visitatore) references visitatori(id)
);


INSERT INTO amministratori (username, password, email)
VALUES 
('admin1', 'hashed_password1', 'admin1@artifex.com'),
('admin2', 'hashed_password2', 'admin2@artifex.com');




INSERT INTO guide (nome, cognome, data_nascita, luogo_nascita, titolo_studio)
VALUES 
('Giulia', 'Bianchi', '1985-06-15', 'Milano', 'Laurea in Storia dell’Arte'),
('Carlos', 'Ramirez', '1990-03-22', 'Madrid', 'Laurea in Turismo');



INSERT INTO competenze_linguistiche (id_guida, lingua, livello)
VALUES 
(1, 'Italiano', 'madrelingua'),
(1, 'Inglese', 'avanzato'),
(2, 'Spagnolo', 'madrelingua'),
(2, 'Francese', 'avanzato');



INSERT INTO visite (titolo, descrizione, durata_media, luogo)
VALUES 
('Visita al Museo Archeologico', 'Esplorazione dei reperti della Roma antica.', 90, 'Museo Archeologico Nazionale'),
('Tour Rinascimentale', 'Un viaggio tra le opere d’arte del Rinascimento.', 120, 'Galleria degli Uffizi'),
('Passeggiata Barocca', 'Scopri le meraviglie dell’architettura barocca nel centro storico.', 90, 'Centro storico di Roma'),
('Alla scoperta di Leonardo', 'Tour guidato tra le invenzioni e opere di Leonardo da Vinci.', 75, 'Museo Leonardo da Vinci, Firenze'),
('Storia Medievale', 'Visita guidata nel castello medievale e nei suoi sotterranei.', 60, 'Castello di Brescia'),
('Arte Contemporanea Italiana', 'Esplorazione delle opere di artisti italiani contemporanei.', 90, 'MAXXI - Museo nazionale delle arti del XXI secolo, Roma'),
('Tour delle Ville Venete', 'Tour in pullman tra le antiche ville nobiliari del Veneto.', 180, 'Regione Veneto');




INSERT INTO eventi (id_visita, id_guida, data_evento, ora_inizio, lingua, prezzo, min_partecipanti, max_partecipanti)
VALUES 
(1, 1, '2025-05-10', '10:00:00', 'Italiano', 15.00, 5, 20),
(2, 2, '2025-05-11', '14:00:00', 'Spagnolo', 18.00, 4, 25),
(3, 1, '2025-05-12', '09:30:00', 'Italiano', 12.50, 3, 15),
(4, 2, '2025-05-13', '11:00:00', 'Spagnolo', 14.00, 5, 20),
(5, 1, '2025-05-14', '15:00:00', 'Inglese', 10.00, 2, 12),
(6, 2, '2025-05-15', '13:00:00', 'Francese', 16.00, 4, 18),
(7, 2, '2025-05-16', '08:00:00', 'Italiano', 22.00, 6, 30),
(3, 2, '2025-05-17', '10:00:00', 'Spagnolo', 12.50, 3, 15),
(4, 1, '2025-05-18', '12:00:00', 'Italiano', 14.00, 5, 20);


