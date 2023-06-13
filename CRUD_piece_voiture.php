<?php

    include './connexion_base_de_donnees.php';
// header("Access-Control-Allow-Origin: *");
// header('Access-Control-Allow-Methods:GET, POST, OPTIONS, PUT, DELETE');
// header('Access-Control-Allow-Headers: X-Requested-With,Origin,Content-Type,Cookie,Accept');
// header('Content-Type: application/json');


if ($_GET)
{
    $action  = $_GET['action'];
    $resultat = ['erreur' => false];

    if ($action == "affichePieceVoiture")
    {
        $requete_affichage = "SELECT * FROM vente_piece_voiture.piece_voiture";

        try 
        {
            $reponce = $baseDeDonnees->query($requete_affichage);

            $i = 0;
            $pieces = [];
            $Tous_pieces = [];

            while($piece = $reponce->fetch())
            {
                $pieces["id_piece_voiture"] = $piece['id_piece_voiture'];
                $pieces["nom_piece"] = $piece['nom_piece'];
                $pieces["prix_unitaire"] = $piece['prix_unitaire'];
                $pieces["nombre_stock"] = $piece['nombre_stock'];

                $Tous_pieces[$i] = $pieces;
                $i++;
            }
            $resultat['pieces'] = $Tous_pieces;          
        }
        catch (PDOException $e) {
            $resultat['erreur'] = true;
            $resultat['message'] = "Erreur du requete http";
        }
        echo json_encode($resultat);
    }

    if ($action == "insertionPieceVoiture")
    {
        $idPieceVoiture = $_POST['id_piece_voiture'];
        $nomPiece = $_POST['nom_piece'];
        $prixUnitaire = $_POST['prix_unitaire'];
        $nombreStock = $_POST['nombre_stock'];

        $requete_insertion = "INSERT INTO vente_piece_voiture.piece_voiture(id_piece_voiture, nom_piece, prix_unitaire, nombre_stock) VALUES (:idPieceVoiture, :nomPiece, :prixUnitaire, :nombreStock)";
        $requete_estExiste = "SELECT * FROM vente_piece_voiture.piece_voiture WHERE id_piece_voiture = :idPieceVoiture";

        if ( empty($idPieceVoiture) || empty($nomPiece) || empty($prixUnitaire) || empty($nombreStock) || !preg_match('/piece[0-9]+/', $idPieceVoiture)  || !preg_match('/[a-zA-Z0-9]+/', $nomPiece) || !is_numeric($nombreStock) || !is_numeric($prixUnitaire))
        {
            $resultat['erreur'] = true;
            $resultat['message'] = "Veiller inserer des informations valides !";
        }
        else
        {
            try {
                $requete = $baseDeDonnees->prepare($requete_estExiste);
                $requete->bindValue(':idPieceVoiture', $idPieceVoiture);
                $requete->execute();

                if($requete->rowCount() > 0)
                {
                    $resultat['message'] = "L'identifiant existe deja dans la base de donnees !";
                }
                else
                {
                    try {
                        $requete = $baseDeDonnees->prepare($requete_insertion);
                        $requete->bindValue(':idPieceVoiture', $idPieceVoiture);
                        $requete->bindValue(':nomPiece', $nomPiece);
                        $requete->bindValue(':prixUnitaire', $prixUnitaire);
                        $requete->bindValue(':nombreStock', $nombreStock);
                        $reponse = $requete->execute();
            
                        $resultat['message'] = "Insertion d'une piece avec succes !";

                        } 
                        catch (PDOException $e) {
                            $resultat['erreur'] = true;
                            $resultat['message'] = "Erreur du requete http";
                        } 
                }
            } catch (PDOException $e) {
                $resultat['erreur'] = true;
                $resultat['message'] = "Erreur du requete http";
            }   
        }
        echo json_encode($resultat);
    }

    if ($action == "modificationPieceVoiture")
        {
            $idPieceVoiture = $_POST['id_piece_voiture'];
            $nomPiece = $_POST['nom_piece'];
            $prixUnitaire = $_POST['prix_unitaire'];
            $nombreStock = $_POST['nombre_stock'];

            $requete_modification = "UPDATE vente_piece_voiture.piece_voiture SET nom_piece=:nomPiece, prix_unitaire=:prixUnitaire, nombre_stock=:nombreStock WHERE id_piece_voiture=:idPieceVoiture";
            $requete_estExiste = "SELECT * FROM vente_piece_voiture.piece_voiture WHERE id_piece_voiture = :idPieceVoiture";

            if ( empty($idPieceVoiture) || empty($nomPiece) || empty($prixUnitaire) || empty($nombreStock) || !preg_match('/piece[0-9]+/', $idPieceVoiture)  || !preg_match('/[a-zA-Z0-9]+/', $nomPiece) || !is_numeric($nombreStock) || !is_numeric($prixUnitaire))
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Veiller inserer des informations valides !";
            }
            else
            {
                try {
                    $requete = $baseDeDonnees->prepare($requete_estExiste);
                    $requete->bindValue(':idPieceVoiture', $idPieceVoiture);
                    $requete->execute();
    
                    if($requete->rowCount() <= 0)
                    {
                        $resultat['message'] = "L'identifiant n'existe pas dans la base de donnees !";
                    }
                    else
                    {
                        try {
                            $requete = $baseDeDonnees->prepare($requete_modification);
                            $requete->bindValue(':idPieceVoiture', $idPieceVoiture);
                            $requete->bindValue(':nomPiece', $nomPiece);
                            $requete->bindValue(':prixUnitaire', $prixUnitaire);
                            $requete->bindValue(':nombreStock', $nombreStock);
                            $reponse = $requete->execute();

                            $resultat['message'] = "Modification d'une piece avec succes !";

                        } catch (PDOException $e) {
                            $resultat['erreur'] = true;
                            $resultat['message'] = "Erreur du requete http";
                        }
                    }
                } catch (PDOException $e) {
                    $resultat['erreur'] = true;
                    $resultat['message'] = "Erreur du requete http";
                }
            }

            echo json_encode($resultat);
        }

        if ($action == "suppressionPieceVoiture")
        {
            $idPieceVoiture = $_POST['id_piece_voiture'];

            $requete_suppression = "DELETE FROM vente_piece_voiture.piece_voiture WHERE id_piece_voiture = :idPieceVoiture";
            $requete_estExiste = "SELECT * FROM vente_piece_voiture.piece_voiture WHERE id_piece_voiture = :idPieceVoiture";

            if ( empty($idPieceVoiture) || !preg_match('/piece[0-9]+/', $idPieceVoiture))
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Veiller inserer l'identifiant valide !";
            }
            else{
                try 
                {
                    $requete = $baseDeDonnees->prepare($requete_estExiste);
                    $requete->bindValue(':idPieceVoiture', $idPieceVoiture);
                    $requete->execute();
    
                    if($requete->rowCount() <= 0)
                    {
                        $resultat['message'] = "L'identifiant n'existe pas dans la base de donnees !";
                    }
                    else 
                    {
                        try 
                        {
                            $requete = $baseDeDonnees->prepare($requete_suppression);
                            $requete->bindValue(':idPieceVoiture', $idPieceVoiture);
                            $reponse = $requete->execute();
            
                            $resultat['message'] = "Supression d'une piece avec succes !";
                
                        }
                        catch (PDOException $e)
                        {
                            $resultat['erreur'] = true;
                            $resultat['message'] = "Erreur du requete http";
                        }
                    }
                } catch(PDOException $e){
                    $resultat['erreur'] = true;
                    $resultat['message'] = "Erreur du requete http";
                }    
            }
            echo json_encode($resultat);
        }        
}

?>