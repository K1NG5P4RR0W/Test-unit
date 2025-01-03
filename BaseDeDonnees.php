<?php
namespace Bdd;
class BaseDeDonnes {
    public function connexion(){

    
    try{
        $connexion=new PDO("mysql:host=localhost;dbname=tp_tdd", 'root', '');
        return $connexion;
        
    }catch(Exception $e){
        echo 'Echec de connexion à la base de données :', $e->getMessage(), "\n";
        
    }
}
}

$bdd=new BaseDeDonnes();
$bdd->connexion();

?>