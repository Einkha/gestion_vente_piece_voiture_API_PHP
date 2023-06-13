<?php

    include './connexion_base_de_donnees.php';
    
    if($_GET)
    {
        $action  = $_GET['action'];
        $resultat = ['erreur' => false];

        if ($action == "rechercheClient")
        {
            $elementRechercher = $_POST['element_rechercher'];

            if(empty($elementRechercher) || !preg_match('/[a-zA-Z0-9]+/', $elementRechercher))
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Veuillez saisir uniquement des lettres ou des chiffres et compléter le champ !";
            }
            else
            {
                $requete_recherche = "SELECT * FROM client WHERE nom_client LIKE :elementRechercher OR id_client LIKE :elementRechercher";
            
                try {
                    $requete = $baseDeDonnees->prepare($requete_recherche);
                    $elementRechercher = "%".$elementRechercher."%";
                    $requete->bindValue(':elementRechercher', $elementRechercher);
                    $requete->execute();
            
                    $reponse = $requete->fetchAll(PDO::FETCH_ASSOC);
            
                    $resultat['message'] = $reponse;
            
                } catch (PDOException $e) {
                    $resultat['erreur'] = true;
                    $resultat['message'] = "Erreur du requete http !";
                }
            }
        }
        echo json_encode($resultat);        
    }

?>