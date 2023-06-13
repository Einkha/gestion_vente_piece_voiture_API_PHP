<?php

    include './connexion_base_de_donnees.php';

    if ($_GET)
    {
        $action  = $_GET['action'];
        $resultat = ['erreur' => false];

        if($action =='inscription')
        {
            $pseudo = $_POST['pseudo'];
            $motDePasse = $_POST['mot_de_passe'];
            $confirmerMotDePasse = $_POST['confirmer_mot_de_passe'];
            $motClef = $_POST['mot_clef'];
                        
            if (empty($pseudo) || empty($motDePasse) || empty($confirmerMotDePasse) ||
                !preg_match('/^[a-zA-Z0-9]+$/', $pseudo) || !preg_match('/^[a-zA-Z0-9]+$/', $motClef)) 
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Veuillez insérer des informations valides et compléter tous les champs !";
            } 
            else if ($motDePasse != $confirmerMotDePasse) 
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Mot de passe incorrect, veuillez réessayer !";
            } 
            else if (strlen($motDePasse) <= 7) 
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "La longueur du mot de passe doit être au moins 8 caractères !";
            } 
            else 
            {
                $requete_estExiste = "SELECT * FROM authentification WHERE pseudo=:pseudo";

                try {
                    $requete = $baseDeDonnees->prepare($requete_estExiste);
                    $requete->bindValue(':pseudo', $pseudo);
                    $requete->execute();

                    if($requete->rowCount() > 0)
                    {
                        $resultat['erreur'] = true;
                        $resultat['message'] = "Le pseudo existe deja dans la base de donnees !";
                    }
                    else
                    {
                        $motDePasseHasher = password_hash($motDePasse, PASSWORD_DEFAULT);
                        $motClefHasher = password_hash($motClef, PASSWORD_DEFAULT);
                        $requete_insertion = "INSERT INTO authentification(pseudo, mot_de_passe, mot_clef) VALUES(:pseudo, :motDePasse, :motClefHasher)";

                        try {
                            $requete = $baseDeDonnees->prepare($requete_insertion);
                            $requete->bindValue(':pseudo', $pseudo);
                            $requete->bindValue(':motDePasse', $motDePasseHasher);
                            $requete->bindValue(':motClefHasher', $motClefHasher);
                            $requete->execute();

                            $resultat['message'] = "Création du compte avec succès !";
                        } catch (PDOException $e) {
                            $resultat['erreur'] = true;
                            $resultat['message'] = "Erreur du requete http !";
                        }
                    }
                } catch (PDOException $e) {
                    $resultat['erreur'] = true;
                    $resultat['message'] = "Erreur du requete http !";
                }
            }
            echo json_encode($resultat);
        }
       
        if($action == "verifierMotClef")
        {
            $pseudo = $_POST['pseudo'];
            $motClef = $_POST['mot_clef'];

            if(empty($pseudo) || empty($motClef))
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Veillez completer les champs !";
            }
            else
            {
                $requete_verifier_motClef = "SELECT * FROM authentification WHERE pseudo=:pseudo";

                try {
                    $requete = $baseDeDonnees->prepare($requete_verifier_motClef);
                    $requete->bindValue(':pseudo', $pseudo);
                    $requete->execute();

                    $reponse = $requete->fetch(PDO::FETCH_ASSOC);

                    if($reponse)
                    {
                        $motClefBdd = $reponse['mot_clef'];

                        if(password_verify($motClef, $motClefBdd))
                        {
                            $resultat['message'] = "Votre mot clef est valide !";
                        }
                        else
                        {
                            $resultat['erreur'] = true;
                            $resultat['message'] = "Votre mot clef est invalide !";
                        }
                    }
                    else
                    {
                        $resultat['erreur'] = true;
                        $resultat['message'] = "Le pseudo n'existe pas dans la base de donnees !";
                    }
                } catch (PDOException $e) {
                    $resultat['erreur'] = true;
                    $resultat['message'] = "Erreur du requete http !";
                }
            }
            echo json_encode($resultat);
        }

        if($action == 'changeMotDePasse')
        {
            $pseudo = $_POST['pseudo'];
            $nouveauMotDePasse = $_POST['noueau_mot_de_passe'];
            $confirmerNouveauMotDePasse = $_POST['confirmer_noueau_mot_de_passe'];

            if(empty($pseudo) || empty($nouveauMotDePasse) || empty($nouveauMotDePasse))
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Veillez completer tous champs !";
            }
            else if($nouveauMotDePasse != $nouveauMotDePasse)
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "Mot de passe incorrect, veuillez réessayer !";
            }
            else if(strlen($nouveauMotDePasse) <= 7)
            {
                $resultat['erreur'] = true;
                $resultat['message'] = "La longueur du mot de passe doit être au moins 8 caractères !";
            }
            else
            {
                $nouveauMotDePasseHasher = password_hash($nouveauMotDePasse, PASSWORD_DEFAULT);
                $requete_update_mdp = "UPDATE authentification SET mot_de_passe=:nouveauMotDePasseHasher WHERE pseudo=:pseudo";

                try {
                    $requete = $baseDeDonnees->prepare($requete_update_mdp);
                    $requete->bindValue(':nouveauMotDePasseHasher', $nouveauMotDePasseHasher);
                    $requete->bindValue(':pseudo', $pseudo);
                    $requete->execute();

                    $resultat['message'] = "Mise a jour de passe avec succes !";

                } catch (PDOException $e) {
                    $resultat['erreur'];
                    $resultat['message'] = "Erruer du requete http !";
                }
            }
            echo json_encode($resultat);
        }
    }

?>