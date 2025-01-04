<?php

namespace App;
require_once('BaseDeDonnees.php');
class Session
{
    public mixed $pdo;

    public function __construct()
    {
        $bdd = new BaseDeDonnes();
        $this->pdo = $bdd->connexion();
    }

    public function sessionUtilisateur(string $identifiant, string $mot_de_passe)
    {
        $requete = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE pseudo = :identifiant");
        $requete->bindParam(':identifiant', $identifiant, \PDO::PARAM_STR);
        $requete->execute();

        $user = $requete->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($mot_de_passe, $user['mdp'])) {
            $_SESSION['id_user'] = $user['id'];
            $_SESSION['Pseudo'] = $user['pseudo'];
            header('Location: tableau_de_bord.php');
            exit();
        } else {
            throw new \Exception("pseudo ou mot de passe incorrect !");
        }
    }
}

?>