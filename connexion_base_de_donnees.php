<?php
    try {
        $baseDeDonnees = new PDO("mysql:hostname=localhost;dbname=vente_piece_voiture", "root", "");
        $baseDeDonnees->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
?>