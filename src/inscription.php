<?php

namespace App;

use App\BaseDeDonnes;

class Inscription
{
    private array $errors = [];
    private mixed $pdo;

    public function __construct()
    {
        $bdd = new BaseDeDonnes();
        $this->pdo = $bdd->connexion();
        $this->createTable();
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    private function createTable(): void
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

    private function verifierMotDePasseFort(string $mdp): bool
    {
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

    public function inscriptionUtilisateur(string $nom, string $prenom, string $pseudo, string $numero, string $mdp): string
    {
        try {
            // Vérifiez si le pseudo existe déjà
            $verifiePseudo = $this->pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE pseudo = :pseudo");
            $verifiePseudo->bindParam(":pseudo", $pseudo);
            $verifiePseudo->execute();
            $count = $verifiePseudo->fetchColumn();

            if ($count > 0) {
                $this->addError("Le pseudo existe déjà. Veuillez en choisir un autre.");
            }

            // Vérifiez la force du mot de passe
            $this->verifierMotDePasseFort($mdp);

            if (count($this->errors) > 0) {
                return "Erreur lors de l'inscription : " . implode(", ", $this->errors);
            }

            // Hachez le mot de passe
            $mdp = password_hash($mdp, PASSWORD_BCRYPT);

            // Insérez l'utilisateur dans la base de données
            $requete = $this->pdo->prepare("INSERT INTO utilisateurs (nom, prenom, pseudo, numero, mdp)
                VALUES (:nom, :prenom, :pseudo, :numero, :mdp)");
            $requete->bindParam(":nom", $nom);
            $requete->bindParam(":prenom", $prenom);
            $requete->bindParam(":pseudo", $pseudo);
            $requete->bindParam(":numero", $numero);
            $requete->bindParam(":mdp", $mdp);
            $requete->execute();

            return "Utilisateur créé avec succès";
        } catch (\Exception $e) {
            return "Erreur lors de l'inscription : " . $e->getMessage();
        }
    }
}
