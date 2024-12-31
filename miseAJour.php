<?php
class MiseAJOUR
{
    public mixed $pdo;
    public function __construct()
    {
        $bdd = new BaseDeDonnes();
        $this->pdo= $bdd->connexion();
    }
    public function miseAJour(int $id, string $nom=NULL, string $prenom=NULL, int $num=NULL, string $mdp=NULL)
    {
        try {
            // Assurer une mise à jour que uniquement les valeurs renseigner dans le formulaire soient mises à jour
            $sql = "UPDATE utilisateurs SET ";
            $params = []; 

            if ($nom !== null) {
                $sql .= "nom = :nom, ";
                $params[':nom'] = $nom;
            }
            if ($prenom !== null) {
                $sql .= "prenom = :prenom, ";
                $params[':prenom'] = $prenom;
            }
            if ($num !== null) {
                $sql .= "numero = :numero, ";
                $params[':numero'] = $num;
            }
            if ($mdp !== null) {
                $mdp = password_hash($mdp, PASSWORD_BCRYPT);
                $sql .= "mdp = :mdp, ";
                $params[':mdp'] = $mdp;
            }
    
            $sql = rtrim($sql, ", ") . " WHERE id = :id";
            $params[':id'] = $id;
    
            
            $requete = $this->pdo->prepare($sql);
            $requete->execute($params);
    
            return "Mise à jour réussie.";
        } catch (Exception $e) {
            return "Erreur lors de la mise à jour : " . $e->getMessage();
        }
    }
}

?>