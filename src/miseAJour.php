<?php
namespace App;
require_once('BaseDeDonnees.php');   
class MiseAJOUR
{
    public mixed $pdo;
    public function __construct()
    {
        $bdd = new BaseDeDonnes();
        $this->pdo= $bdd->connexion();
    }
    public function miseAJour(string $id, string $nom = null, string $prenom = null, string $num = null, string $mdp = null)
    {
        try {
            // Construire la requête SQL dynamiquement
            $sql = "UPDATE utilisateurs SET ";
            $params = [];
    
            if (!empty($nom)) { // Vérifie si le champ n'est pas vide
                $sql .= "nom = :nom, ";
                $params[':nom'] = $nom;
            }
            if (!empty($prenom)) {
                $sql .= "prenom = :prenom, ";
                $params[':prenom'] = $prenom;
            }
            if (!empty($num)) {
                $sql .= "numero = :numero, ";
                $params[':numero'] = $num;
            }
            if (!empty($mdp)) {
                $mdp = password_hash($mdp, PASSWORD_BCRYPT);
                $sql .= "mdp = :mdp, ";
                $params[':mdp'] = $mdp;
            }
    
            // Retirer la virgule finale et ajouter la clause WHERE
            $sql = rtrim($sql, ", ") . " WHERE id = :id";
            $params[':id'] = $id;
    
            // Préparer et exécuter la requête
            $requete = $this->pdo->prepare($sql);
            $requete->execute($params);
    
            return "Mise à jour réussie.";
        } catch (\Exception $e) {
            return "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
    
}

?>