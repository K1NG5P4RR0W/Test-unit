<?php
require('src/BaseDeDonnees.php');
require ('src/connexion.php');

use PHPUnit\Framework\TestCase;
use App\Session;

class SessionTest extends TestCase
{
    private $session;

    protected function setUp(): void
    {
        // Mock la connexion à la base de données
        $pdo = $this->createMock(\PDO::class);
        $pdoStatement = $this->createMock(\PDOStatement::class);

        $pdoStatement->method('fetch')->with(\PDO::FETCH_ASSOC)
            ->willReturn([
                'id' => 1,
                'pseudo' => 'validUser',
                'mdp' => password_hash('validPassword', PASSWORD_DEFAULT)
            ]);

        $pdo->method('prepare')
            ->with("SELECT * FROM utilisateurs WHERE pseudo = :identifiant")
            ->willReturn($pdoStatement);

        $baseDeDonnes = $this->createMock(App\BaseDeDonnes::class);
        $baseDeDonnes->method('connexion')->willReturn($pdo);

        $this->session = new Session($baseDeDonnes);
    }

    public function testConnexionStandard()
    {
        // Démarrer la session pour le test
        session_start();

        // Intercepter la sortie pour la redirection
        ob_start();

        // Appeler la méthode de connexion avec des identifiants valides
        $this->session->sessionUtilisateur('validUser', 'validPassword');

        // Récupérer la sortie pour vérifier la redirection
        $output = ob_get_clean();

        // Vérifier que la redirection vers le tableau de bord a eu lieu
        $this->assertStringContainsString('Location: tableau_de_bord.php', $output);

        // Vérifier que la session a été mise à jour
        $this->assertTrue(array_key_exists('id_user', $_SESSION));
        $this->assertTrue(array_key_exists('Pseudo', $_SESSION));

        // Vérifier que les informations de session sont correctes
        $this->assertEquals(1, $_SESSION['id_user']);
        $this->assertEquals('validUser', $_SESSION['Pseudo']);

        // Nettoyer la session après le test
        session_unset();
        session_destroy();
    }
}
?>