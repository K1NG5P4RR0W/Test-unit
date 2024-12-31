<?php
require_once('BaseDeDonnees.php');
class Inscription{
    public mixed $pdo;
    public function __construct(){
        $bdd= new BaseDeDonnes();
        $this->pdo=$bdd->connexion();
        $this->createTable();
        
    }


    public function createTable(){
        $requete = "
        CREATE TABLE IF NOT EXISTS utilisateurs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(150) NOT NULL,
            prenom VARCHAR(150) NOT NULL,
            pseudo VARCHAR(100) NOT NULL,
            numero VARCHAR(30) NOT NULL,
            mdp VARCHAR(300) NOT NULL,
            user_roles VARCHAR(25) DEFAULT 'utilisateur'
        )
    ";
        $this->pdo->exec($requete);
    }

    public function inscriptionUtilisateur(string $nom, string $prenom, string $pseudo, string $numero, string $mdp){
        try {
            
            $verifiePseudo = $this->pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE pseudo = :pseudo");
            $verifiePseudo->bindParam(":pseudo", $pseudo);
            $verifiePseudo->execute();
            $count = $verifiePseudo->fetchColumn();
    
            if ($count > 0) {
                throw new Exception("Le pseudo existe déjà. Veuillez en choisir un autre.");
            }else {
                $mdp=password_hash($mdp, PASSWORD_BCRYPT);

                $requete = $this->pdo->prepare(" INSERT INTO utilisateurs (nom, prenom, pseudo, numero, mdp)
                VALUES(:nom, :prenom, :pseudo, :numero, :mdp)");
    
                $requete->bindParam(":nom", $nom);
                $requete->bindParam(":prenom", $prenom);
                $requete->bindParam(":pseudo", $pseudo);
                $requete->bindParam(":numero", $numero);
                $requete->bindParam(":mdp", $mdp);
                $requete->execute();
            }
            return "utilisateur créer avec succès";
           
        }catch(Exception  $e){
            return "Erreur lors de l'inscription".$e->getMessage();
        }

    }
}
?>