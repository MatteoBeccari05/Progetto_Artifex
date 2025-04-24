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
