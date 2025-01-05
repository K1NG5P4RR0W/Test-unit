<?php
require('src/BaseDeDonnees.php');
require ('src/connexion.php');


use PHPUnit\Framework\TestCase;
use App\Session;
use App\Inscription;

class SessionTest extends TestCase
{
    
    private $pdoMock;
    private $statementMock;

    protected function setUp(): void
    {
        // Mock de PDOStatement
        $this->statementMock = $this->createMock(\PDOStatement::class);

        // Mock de PDO
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoMock
            ->method('prepare')
            ->willReturn($this->statementMock);
    }

    public function testInscriptionUtilisateurReussie()
    {
        // Simule un pseudo non existant
        $this->statementMock
            ->method('fetchColumn')
            ->willReturn(0); // Le pseudo n'existe pas

        // Simule une exécution réussie de l'insertion
        $this->statementMock
            ->method('execute')
            ->willReturn(true);

        // Instancie la classe Inscription avec le PDO mocké
        
        $inscription = new Inscription();
        $inscription->pdo = $this->pdoMock; // Injection manuelle du mock PDO

        $resultat = $inscription->inscriptionUtilisateur(
            'Dupont', 'Jean', 'jeandupont', '0123456789', 'StrongP@ssw0rd'
        );

        $this->assertEquals('Utilisateur créé avec succès', $resultat);
    }

    public function testInscriptionUtilisateurEchecPseudoExiste()
    {
        // Simule un pseudo déjà existant
        $this->statementMock
            ->method('fetchColumn')
            ->willReturn(1); // Le pseudo existe déjà

        // Instancie la classe Inscription avec le PDO mocké
        $inscription = new Inscription();
        $inscription->pdo = $this->pdoMock; // Injection manuelle du mock PDO

        $resultat = $inscription->inscriptionUtilisateur(
            'Dupont', 'Jean', 'jeandupont', '0123456789', 'StrongP@ssw0rd'
        );

        $this->assertStringContainsString(
            'Le pseudo existe déjà',
            $resultat
        );
    }

    public function testSessionUtilisateurReussite()
    {
        // Simule les données retournées par la base de données
        $userData = [
            'id' => 1,
            'pseudo' => 'testuser',
            'mdp' => password_hash('password123', PASSWORD_DEFAULT),
        ];

        $this->statementMock
            ->method('fetch')
            ->willReturn($userData);

        $session = new Session();
        $session->pdo = $this->pdoMock; // Injection manuelle du mock PDO

        $_SESSION = [];

        $this->expectOutputString('');
        $session->sessionUtilisateur('testuser', 'password123');

        $this->assertEquals(1, $_SESSION['id_user']);
        $this->assertEquals('testuser', $_SESSION['Pseudo']);
    }

    public function testSessionUtilisateurEchec()
    {
        // Simule un utilisateur non trouvé
        $this->statementMock
            ->method('fetch')
            ->willReturn(false);

        $session = new Session();
        $session->pdo = $this->pdoMock; // Injection manuelle du mock PDO

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('pseudo ou mot de passe incorrect !');

        $session->sessionUtilisateur('fakeuser', 'fakepassword');
    }
}

?>
