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

    if ($action == "afficheClient")
    {
        $requete_affichage = "SELECT * FROM vente_piece_voiture.client";

        try 
        {
            $reponce = $baseDeDonnees->query($requete_affichage);

            $i = 0;
            $clients = [];
            $Tous_Clients = [];

            while($client = $reponce->fetch())
            {
                $clients["id_client"] = $client['id_client'];
                $clients["nom_client"] = $client['nom_client'];
                $clients["prenom_client"] = $client['prenom_client'];

                $Tous_Clients[$i] = $clients;
                $i++;
            };
        
            $resultat['clients'] = $Tous_Clients;
        }
        catch (PDOException $e) {
            $resultat['erreur'] = true;
            $resultat['message'] = $e;
        }
        echo json_encode($resultat);
    }

    if ($action == "insertionClient")
    {
        $idClient = $_POST['id_client'];
        $nomClient = $_POST['nom_client'];
        $prenomClient = $_POST['prenom_client'];

        if ( empty($idClient) || empty($nomClient)  || empty($prenomClient) || !preg_match('/client[0-9]+/', $idClient)  || !preg_match('/[a-zA-Z0-9]+/', $nomClient) || !preg_match('/[a-zA-Z0-9]+/', $prenomClient))
        {
            $resultat['erreur'] = true;
            $resultat['message'] = "Veiller inserer des informations valides et completer tous les champs !";
        }
        else
        {
            $requete_estExiste = "SELECT * FROM vente_piece_voiture.client WHERE id_client = :idClient";

            try {
                $requete = $baseDeDonnees->prepare($requete_estExiste);
                $requete->bindValue(':idClient', $idClient);
                $requete->execute();

                if($requete->rowCount() > 0)
                {
                    $resultat['message'] = "L'identifiant existe deja dans la base de donnees !";
                }
                else
                {
                    $requete_insertion = "INSERT INTO vente_piece_voiture.client(id_client, nom_client, prenom_client) VALUES (:idClient, :nomClient, :prenomClient)";

                    try {
                        $requete = $baseDeDonnees->prepare($requete_insertion);
                        $requete->bindValue(':idClient', $idClient);
                        $requete->bindValue(':nomClient', $nomClient);
                        $requete->bindValue(':prenomClient', $prenomClient);
                        $reponse = $requete->execute();
            
                        $resultat['message'] = "Insertion d'un client avec succes !";
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

    if ($action == "modificationClient")
        {
            $idClient = $_POST['id_client'];
            $nomClient = $_POST['nom_client'];
            $prenomClient = $_POST['prenom_client'];

            $requete_modification = "UPDATE vente_piece_voiture.client SET nom_client=:nomClient, prenom_client=:prenomClient WHERE id_client=:idClient";
            $requete_estExiste = "SELECT * FROM vente_piece_voiture.client WHERE id_client = :idClient";

            if ( empty($idClient) || empty($nomClient)  || empty($prenomClient) || !preg_match('/client[0-9]+/', $idClient)  || !preg_match('/[a-zA-Z0-9]+/', $nomClient) || !preg_match('/[a-zA-Z0-9]+/', $prenomClient))
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Veiller inserer des informations valides !";
            }
            else
            {
                try {
                    $requete = $baseDeDonnees->prepare($requete_estExiste);
                    $requete->bindValue(':idClient', $idClient);
                    $requete->execute();
    
                    if($requete->rowCount() <= 0)
                    {
                        $resultat['message'] = "L'identifiant n'existe pas dans la base de donnees !";
                    }
                    else
                    {
                        try {
                            $requete = $baseDeDonnees->prepare($requete_modification);
                            $requete->bindValue(':idClient', $idClient);
                            $requete->bindValue(':nomClient', $nomClient);
                            $requete->bindValue(':prenomClient', $prenomClient);
                            $reponse = $requete->execute();

                            $resultat['message'] = "Modification des infornations avec succes !";
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

        if ($action == "suppressionClient")
        {
            $idClient = $_POST['id_client'];

            $requete_suppression = "DELETE FROM vente_piece_voiture.client WHERE id_client = :idClient";
            $requete_estExiste = "SELECT * FROM vente_piece_voiture.client WHERE id_client = :idClient";

            if ( empty($idClient) || !preg_match('/client[0-9]+/', $idClient))
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Veiller inserer l'identifiant valide !";
            }
            else{
                try 
                {
                    $requete = $baseDeDonnees->prepare($requete_estExiste);
                    $requete->bindValue(':idClient', $idClient);
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
                            $requete->bindValue(':idClient', $idClient);
                            $reponse = $requete->execute();
            
                            $resultat['message'] = "Supression d'un client avec succes !";
                
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