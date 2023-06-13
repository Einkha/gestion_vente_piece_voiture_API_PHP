<?php

    $table_client = "
        CREATE TABLE IF NOT EXISTS client(
            id_client varchar(10) PRIMARY KEY,
            nom_client varchar(50),
            prenom_client varchar(50)
        )
    ";

    $table_piece_voiture = "
        CREATE TABLE IF NOT EXISTS piece_voiture(
            id_piece_voiture varchar(10) PRIMARY KEY,
            nom_piece varchar(50),
            prix_unitaire float,
            nombre_stock int
        )
    ";

    $table_vente = "
        CREATE TABLE IF NOT EXISTS vente(
            id_vente varchar(10) PRIMARY KEY,
            id_client varchar(10),
            id_piece_voiture varchar(10),
            quantite int,
            date_achat DATE
        )
    ";

    $table_authentification = "
        CREATE TABLE IF NOT EXISTS authentification(
            id_utilisateur int PRIMARY KEY,
            pseudo varchar(20),
            mot_de_passe varchar(100),
            mot_clef varchar(100)
        )
    ";

?>