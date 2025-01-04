<?php
require_once('src/BaseDeDonnees.php');
use PHPUnit\Framework\TestCase;
use App\BaseDeDonnes;
use \App\Inscription;

class InscriptionTest extends TestCase
{
    private mixed $pdo;

    protected function setUp(): void
    {
        
        $bdd = new BaseDeDonnes();
        $this->pdo = $bdd->connexion();

        
    }
    
    public function testCreationCompteAvecSucces(): void
    {
        $inscription = new Inscription();

        
        $resultat = $inscription->inscriptionUtilisateur(
            "Jean",
            "Gong",
            "jdupont5425",
            "0612345678",
            "P@ssword123"
        );

       
        $this->assertEquals(
            "Utilisateur créé avec succès",
            $resultat,
            "Le message de confirmation doit indiquer un succès"
        );

        
        $pseudo = "jdupont";
        $requete = $this->pdo->prepare("SELECT * FROM utilisateurs WHERE pseudo = :pseudo");
        $requete->bindParam(":pseudo", $pseudo );
        $requete->execute();
        $utilisateur = $requete->fetch();

        $this->assertNotEmpty($utilisateur, "L'utilisateur doit exister en base après l'inscription");
        $this->assertEquals("Jean", $utilisateur['nom']);
        $this->assertEquals("Dupont", $utilisateur['prenom']);
        $this->assertEquals("0612345678", $utilisateur['numero']);
    }
}
