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

    if ($action == "afficheVente")
    {
        $requete_affichage = "SELECT * FROM vente, piece_voiture, client WHERE vente.id_client = client.id_client and vente.id_piece_voiture = piece_voiture.id_piece_voiture";

        try 
        {
            $reponce = $baseDeDonnees->query($requete_affichage);

            $i = 0;
            $donnees = [];
            $Tous_donnees = [];

            while($donnee = $reponce->fetch())
            {
                $donnees["nom_client"] = $donnee['nom_client'];
                $donnees["prenom_client"] = $donnee['prenom_client'];
                $donnees["nom_piece"] = $donnee['nom_piece'];
                $donnees["quantite"] = $donnee['quantite'];
                $donnees["date_achat"] = $donnee['date_achat'];

                $Tous_donnees[$i] = $donnees;
                $i++;
            };
        
            $resultat['donnees'] = $Tous_donnees;
            
        }
        catch (PDOException $e) {
            $resultat['erreur'] = true;
            $resultat['message'] = $e;
        }
        echo json_encode($resultat);
    }

    if ($action == "insertionVente")
    {
        $idVente = $_POST['id_vente'];
        $idClient = $_POST['id_client'];
        $idPieceVoiture = $_POST['id_piece_voiture'];
        $Quantite = $_POST['quantite'];
        $dateAchat = $_POST['date_achat'];

        if (empty($idVente) || empty($idClient) || empty($idPieceVoiture) || empty($Quantite) || empty($dateAchat) || !preg_match('/vente[0-9]+/', $idVente) || !preg_match('/client[0-9]+/', $idClient) || !preg_match('/piece[0-9]+/', $idPieceVoiture) || !is_numeric($Quantite) || !strtotime($dateAchat))
        {
            $resultat['message'] = "Veiller inserer les information valide !";
        }
        else 
        {
            $requete_vente_existe = "SELECT * FROM vente_piece_voiture.vente WHERE id_vente=:idVente";

            try 
            {
                $requete = $baseDeDonnees->prepare($requete_vente_existe);
                $requete->bindValue(':idVente', $idVente);
                $requete->execute();

                if($requete->rowCount() > 0)
                {
                    $resultat['message'] = "L'identifiant existe deja dans la base de donnees !";
                }
                else
                {
                    try {
                        $requete_client_existe = "SELECT * FROM vente_piece_voiture.client WHERE id_client=:idClient";
                        $requete = $baseDeDonnees->prepare($requete_client_existe);
                        $requete->bindParam(':idClient', $idClient);
                        $requete->execute();
    
                        $reponse = $requete->fetch(PDO::FETCH_ASSOC);
    
                        if($reponse) 
                        {
                            $requete_piece_existe = "SELECT * FROM vente_piece_voiture.piece_voiture WHERE id_piece_voiture=:idPieceVoiture";
    
                            try {
                                $requete = $baseDeDonnees->prepare($requete_piece_existe);
                                $requete->bindValue(':idPieceVoiture', $idPieceVoiture);
                                $requete->execute();
        
                                $reponse = $requete->fetch(PDO::FETCH_ASSOC);
        
                                if($reponse)
                                {
                                    $stock = $reponse['nombre_stock'];
    
                                    $stock_total = $stock - $Quantite;
    
                                    $requete_vente_insertion = "INSERT INTO vente_piece_voiture.vente(id_vente, id_client, id_piece_voiture, quantite, date_achat) VALUES(:idVente, :idClient, :idPieceVoiture, :Quantite, :dateAchat)";
                                    $requete_vente_update = "UPDATE vente_piece_voiture.piece_voiture SET nombre_stock=:stockTotal WHERE id_piece_voiture=:idPieceVoiture";
    
                                    try {
                                        $requete_1 = $baseDeDonnees->prepare($requete_vente_insertion);
                                        $requete_1->bindValue(':idVente', $idVente);
                                        $requete_1->bindValue(':idClient', $idClient);
                                        $requete_1->bindValue(':idPieceVoiture', $idPieceVoiture);
                                        $requete_1->bindValue(':Quantite', $Quantite);
                                        $requete_1->bindValue(':dateAchat', $dateAchat);
                                        $reponse_1 = $requete_1->execute();
    
                                        $requete_2 = $baseDeDonnees->prepare($requete_vente_update);
                                        $requete_2->bindValue(':stockTotal', $stock_total);
                                        $requete_2->bindValue(':idPieceVoiture', $idPieceVoiture);
                                        $reponse_2 = $requete_2->execute();
    
                                        $resultat['message'] = "Insertion du vente avec succes !";
    
                                    } 
                                    catch (PDOException $e) 
                                    {
                                        $resultat['erreur'] = true;
                                        $resultat['message'] = "Erreur du requete http !";
                                    }
                                } 
                                else
                                {
                                    $resultat['message'] = "L'identifiant du piece n'existe pas dans la base de donnees !";
                                }
        
                            } catch (PDOException $e) {
                                $resultat['erreur'] = true;
                                $resultat['message'] = "Erreur du requete http !";
                            }
                        }
                        else {
                            $resultat['message'] = "L'identifiant du client n'existe pas dans la base de donnees !";
                        }
                    } catch (PDOException $e) {
                        $resultat['erreur'] = true;
                        $resultat['message'] = "Erreur du requete http !";
                    }
                }
            } 
            catch (PDOException $e) 
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Erreur du requete http !";
            }            
        }

        echo json_encode($resultat);
    }

    if ($action == "modificationVente")
        {
            $idVente = $_POST['id_vente'];
            $idClient = $_POST['id_client'];
            $idPieceVoiture = $_POST['id_piece_voiture'];
            $Quantite = $_POST['quantite'];
            $dateAchat = $_POST['date_achat'];

            if (empty($idVente) || empty($idClient) || empty($idPieceVoiture) || empty($Quantite) || empty($dateAchat) || !preg_match('/vente[0-9]+/', $idVente) || !preg_match('/client[0-9]+/', $idClient) || !preg_match('/piece[0-9]+/', $idPieceVoiture) || !is_numeric($Quantite) || !strtotime($dateAchat))
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Veiller inserer des informations valides !";
            }
            else
            {
                $requeteVenteExiste = "SELECT * FROM vente_piece_voiture.vente WHERE id_vente = :idVente";

                try {
                    $requete = $baseDeDonnees->prepare($requeteVenteExiste);
                    $requete->bindValue(':idVente', $idVente);
                    $requete->execute();

                    $reponse = $requete->fetch(PDO::FETCH_ASSOC)
    
                    if(!$reponse)
                    {
                        $resultat['erreur'] = true;
                        $resultat['message'] = "Veiller inserer des informations valides !";
                    }
                    else
                    {
                        $quantiteVendu = $reponse['quantite'];
                        $idPieceVoiture =  $reponse['id_piece_voiture'];

                        $requete_piece_stock = "SELECT * FROM vente_piece_voiture.piece_voiture WHERE id_piece_voiture=:idPieceVoiture";

                        try 
                        {
                            $requete_1 = $baseDeDonnees->prepare($requete_piece_stock);
                            $requete_1->bindValue(':idPieceVoiture', $idPieceVoiture);
                            $requete_1->execute();

                            $reponse_1 = $requete_1->fetch(PDO::FETCH_ASSOC);

                            if (!$reponse_1)
                            {
                                $resultat['erreur'] = true;
                                $resultat['message'] = "L'identifiant du piece n'existe pas dans la base de donnees !";
                            }
                            else
                            {
                                $stock = $reponse_1['nombre_stock'];
                                $nombreStockTotal = $stock + $quantiteVendu;
                                $nouveauStock = $nombreStockTotal - $Quantite;

                                $requete_modification = "UPDATE vente_piece_voiture.vente SET id_piece_voiture=:idPieceVoiture, quantite=:Quantite WHERE id_vente=:idVente";

                                try {
                                    $requete_2 = $baseDeDonnees->prepare($requete_modification);
                                    $requete_2->bindValue('idVente', $idVente);
                                    $requete_2->bindValue('idPieceVoiture', $idPieceVoiture);
                                    $requete_2->bindValue('quantite', $Quantite);
                                    $reponse_2 = $requete_2->execute();

                                    if($reponse_2)
                                    {
                                        $requete_modification = "UPDATE vente_piece_voiture.piece_voiture SET quantite=:Quantite WHERE id_piece_voiture=:idPieceVoiture";
                                        
                                        $requete_3 = $baseDeDonnees->prepare($requete_modification);
                                        $requete_3->bindValue('idPieceVoiture', $idPieceVoiture);
                                        $requete_3->bindValue('quantite', $Quantite);
                                        $requete_3->execute();

                                        $resultat['message'] = 'Modification du vente avec succes !';
                                    }
                                    else
                                    {
                                        $resultat['erreur'] = true;
                                        $resultat['message'] = "Erreur du requete http !";
                                    }
                                } catch (PDOException $e) {
                                    $resultat['erreur'] = true;
                                    $resultat['message'] = $e;
                                }
                            }
                        } catch (PDOException $e) {
                            $resultat['erreur'] = true;
                            $resultat['message'] = $e;
                        }
                    }
                } catch (PDOException $e) {
                    $resultat['erreur'] = true;
                    $resultat['message'] = $e;
                }
            }

            echo json_encode($resultat);
        }

        if ($action == "suppressionVente")
        {
            $idVente = $_POST['id_vente'];

            $requete_Vente_Existe = "SELECT * FROM vente_piece_voiture.vente WHERE id_vente = :idVente";

            if ( empty($idVente) || !preg_match('/vente[0-9]+/', $idVente))
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Veiller inserer l'identifiant valide !";
            }
            else
            {
                try 
                {
                    $requete = $baseDeDonnees->prepare($requete_Vente_Existe);
                    $requete->bindValue(':idVente', $idVente);
                    $requete->execute();
        
                    $reponse = $requete->fetch(PDO::FETCH_ASSOC);
    
                    if(!$reponse)
                    {
                        $resultat['erreur'] = true;
                        $resultat['message'] = "L'identifiant du vente n'existe pas dans la base de donnees !";
                    }
                    else 
                    {
                        $quantite = $reponse['quantite'];
                        $idPieceVoiture = $reponse['id_piece_voiture'];

                        $requete_piece_stock = "SELECT * FROM vente_piece_voiture.piece_voiture WHERE id_piece_voiture=:idPieceVoiture";

                        try 
                        {
                            $requete_1 = $baseDeDonnees->prepare($requete_piece_stock);
                            $requete_1->bindValue(':idPieceVoiture', $idPieceVoiture);
                            $requete_1->execute();

                            $reponse_1 = $requete_1->fetch(PDO::FETCH_ASSOC);

                            if (!$reponse_1)
                            {
                                $resultat['erreur'] = true;
                                $resultat['message'] = "L'identifiant du piece n'existe pas dans la base de donnees !";
                            }
                            else
                            {
                                $stock = $reponse_1['nombre_stock'];
                                $nombreStockTotal = $stock + $quantite;

                                $requete_suppression = "DELETE FROM vente_piece_voiture.vente WHERE id_vente=:idVente";

                                try 
                                {
                                    $requete_2 = $baseDeDonnees->prepare($requete_suppression);
                                    $requete_2->bindValue(':idVente', $idVente);
                                    $reponse_2 = $requete_2->execute();

                                    if ($reponse_2) 
                                    {
                                        $requete_update_stock = "UPDATE vente_piece_voiture.piece_voiture SET nombre_stock=:nombreStockTotal WHERE id_piece_voiture=:idPieceVoiture";

                                        $requete = $baseDeDonnees->prepare($requete_update_stock);
                                        $requete->bindValue(':idPieceVoiture', $idPieceVoiture);
                                        $requete->bindValue(':nombreStockTotal', $nombreStockTotal);
                                        $requete->execute();

                                        $resultat['message'] = "Supression d'une vente avec succes !";
                                    }
                                    else
                                    {
                                        $resultat['erreur'] = true;
                                        $resultat['message'] = "Erreur du requete http !";
                                    }
                                } catch (PDOException $e) {
                                    $resultat['erreur'] = true;
                                    $resultat['message'] = "Erreur du requete http !";
                                }
                            }
                        }
                        catch (PDOException $e)
                        {
                            $resultat['erreur'] = true;
                            $resultat['message'] = "Erreur du requete http !";
                        }
                    }
                } catch(PDOException $e){
                    $resultat['erreur'] = true;
                    $resultat['message'] = "Erreur du requete http !";
                }    
            }
            echo json_encode($resultat);
        }        
}

?>