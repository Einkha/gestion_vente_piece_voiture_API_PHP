<?php

    include './connexion_base_de_donnees.php';

    if($_GET)
    {
        $action  = $_GET['action'];
        $resultat = ['erreur' => false];

        if ($action == 'connexion') 
        {
            $pseudo = $_POST['pseudo'];
            $motDePasse = $_POST['mot_de_passe'];

            if(empty($pseudo) || empty($pseudo))
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Veillez completer tous les champs";
            }
            else
            {
                session_start();

                $requete_connexion = "SELECT * FROM authentification WHERE pseudo=:pseudo";
        
                try 
                {
                    $requete = $baseDeDonnees->prepare($requete_connexion);
                    $requete->bindValue(':pseudo', $pseudo);
                    $requete->execute();
            
                    $reponse = $requete->fetch(PDO::FETCH_ASSOC);
            
                    if ($reponse) 
                    {
                        $motDePasseBdd = $reponse['mot_de_passe'];
            
                        if (password_verify($motDePasse, $motDePasseBdd)) 
                        {
                            $_SESSION['connecte'] = true;
                            $_SESSION['utilisateur_id'] = $reponse['id_utilisateur'];
                            $_SESSION['utilisateur_pseudo'] = $reponse['pseudo'];
            
                            // Réinitialiser les tentatives de connexion
                            $_SESSION['tentatives'] = 0;
                            $_SESSION['temps_derniere_tentative'] = time();
            
                            $resultat['message'] = 'Connexion réussie';
                        } 
                        else 
                        {
                            if (isset($_SESSION['tentatives']))
                            {
                               $_SESSION['tentatives']++;
                           } 
                           else 
                           {
                               $_SESSION['tentatives'] = 1;
                           }
       
                           if ($_SESSION['tentatives'] >= 5) 
                           {
                               $tempsEcoule = time() - $_SESSION['temps_derniere_tentative'];
                               if ($tempsEcoule > 900) 
                               {
                                   $_SESSION['tentatives'] = 0;
                                   $_SESSION['temps_derniere_tentative'] = time();
                               } 
                               else 
                               {
                                   $resultat['erruer'] = true;
                                   $resultat['message'] = 'Trop de tentatives de connexion. Veuillez attendre 15 minutes.';
                                   // Bloquer la tentative de connexion
                                   exit;
                               }
                           }
                           else 
                           {
                               $resultat['erruer'] = true;
                               $resultat['message'] = 'Mot de passe incorrect';
                           }   
                        }
                    } 
                    else 
                    {
                        $resultat['erruer'] = true;
                        $resultat['message'] = "Pseudo non trouvé";
                    }
                } catch (PDOException $e) {
                    $resultat['erruer'] = true;
                    $resultat['message'] = 'Erreur lors de la connexion : ' . $e->getMessage();
                }    
            }
        
            echo json_encode($resultat);

        }

        if($action == "deconnexion")
        {
            session_start();
            $_SESSION = array();
            session_destroy();

            $resultat['message'] = 'Déconnexion réussie';

            echo json_encode($resultat);
        }
        
    }

?>