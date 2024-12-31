<?php 
spl_autoload_register();
class Session{
    public mixed $pdo;
    public function __construct(){
        $bdd = new BaseDeDonnes();
        $this->pdo=$bdd->connexion();        
    }
    
    public function sessionUtilisateur(string $identifiant, string $mot_de_passe){
        $requete = $this->pdo->prepare("SELECT * FROM where pseudo =:identifiant");
        $requete -> bindParam(':identifiant', $identifiant,PDO::PARAM_STR);
        $requete->execute();
    
        $user=$requete->fetch(PDO::FETCH_ASSOC);
        if (password_verify($mot_de_passe, $user['mdp'])){
            $_SESSION['id_user']=$user['id'];
            $_SESSION['Pseudo']=$user['pseudo'];
            header('tableau_de_bord.php');
            
        }
        else{
            echo "pseudo ou mot de passe incorrect !";
        }
    }

}
?>