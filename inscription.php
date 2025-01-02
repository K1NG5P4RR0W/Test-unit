<?php
require_once('BaseDeDonnees.php');
class Inscription
{
    private $errors = [];
    public mixed $pdo;
    public function __construct()
    {
        $bdd = new BaseDeDonnes();
        $this->pdo = $bdd->connexion();
        $this->createTable();
    }


    public function getErrors()
    {
        return $this->errors;
    }

    private function addError($error)
    {
        $this->errors[] = $error;
    }

    public function createTable()
    {
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

    public function verifierMotDePasseFort($mdp)
    {
        $errors = [];

        if (strlen($mdp) < 8) {
            $this->addError("Le mot de passe doit contenir au moins 8 caractères.");
        }

        if (!preg_match('/[A-Z]/', $mdp)) {
            $this->addError("Le mot de passe doit contenir au moins une lettre majuscule.");
        }

        if (!preg_match('/[a-z]/', $mdp)) {
            $this->addError("Le mot de passe doit contenir au moins une lettre minuscule.");
        }

        if (!preg_match('/[0-9]/', $mdp)) {
            $this->addError("Le mot de passe doit contenir au moins un chiffre.");
        }

        if (!preg_match('/[\W_]/', $mdp)) {
            $this->addError("Le mot de passe doit contenir au moins un caractère spécial.");
        }

        return count($this->errors) === 0;
    }


    public function inscriptionUtilisateur(string $nom, string $prenom, string $pseudo, string $numero, string $mdp)
    {
        try {

            $verifiePseudo = $this->pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE pseudo = :pseudo");
            $verifiePseudo->bindParam(":pseudo", $pseudo);
            $verifiePseudo->execute();
            $count = $verifiePseudo->fetchColumn();

            if ($count > 0) {
                $this->addError("Le pseudo existe déjà. Veuillez en choisir un autre.");
            }


            $error = $this->verifierMotDePasseFort($mdp);
            if ($error > 0) {
                return $error;
            }

            if (count($this->errors) === 0) {

                $mdp = password_hash($mdp, PASSWORD_BCRYPT);


                $requete = $this->pdo->prepare("INSERT INTO utilisateurs (nom, prenom, pseudo, numero, mdp)
            VALUES (:nom, :prenom, :pseudo, :numero, :mdp)");
                $requete->bindParam(":nom", $nom);
                $requete->bindParam(":prenom", $prenom);
                $requete->bindParam(":pseudo", $pseudo);
                $requete->bindParam(":numero", $numero);
                $requete->bindParam(":mdp", $mdp);
                $requete->execute();

                return "Utilisateur créé avec succès";
            }
            return 'erreur lors de l\'inscription';
        } catch (Exception $e) {

            return "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
