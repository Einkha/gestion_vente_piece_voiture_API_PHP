<?php

    include './connexion_base_de_donnees.php';

    if($_GET)
    {
        $action  = $_GET['action'];
        $resultat = ['erreur' => false];

        if ($action == "rechercheDate")
        {
            $dateDebut = $_POST['date_debut'];
            $dateFin = $_POST['date_fin'];

            $requete_recherche = "SELECT vente.id_vente, vente.quantite, client.nom_client, piece_voiture.nom_piece FROM vente
                                  JOIN client ON vente.id_client = client.id_client
                                  JOIN piece_voiture ON vente.id_piece_voiture = piece_voiture.id_piece_voiture
                                  WHERE vente.date_achat BETWEEN :dateDebut AND :dateFin";
            try 
            {
                $requete = $baseDeDonnees->prepare($requete_recherche);
                $requete->bindValue(':dateDebut', $dateDebut);
                $requete->bindValue(':dateFin', $dateFin);
                $requete->execute();
                
                $resultats = $requete->fetchAll(PDO::FETCH_ASSOC);
                $resultat['donnees'] = $resultats;
            }
            catch (PDOException $e) {
                $resultat['erreur'] = true;
                $resultat['message'] = $e->getMessage();
            }
            echo json_encode($resultat);
        }
    }

?>