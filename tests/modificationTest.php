<?php

require('src/BaseDeDonnees.php');
require('src/miseAJour.php');

use PHPUnit\Framework\TestCase;
use App\MiseAJOUR;

class ModificationTest extends TestCase
{
    private $pdoMock;
    private $statementMock;

    protected function setUp(): void
    {
        
        $this->statementMock = $this->createMock(\PDOStatement::class);

        
        $this->pdoMock = $this->createMock(\PDO::class);
        $this->pdoMock
            ->method('prepare')
            ->willReturn($this->statementMock);
    }

    public function testMiseAJourAvecNomEtPrenom()
    {
        
        $this->statementMock
            ->method('execute')
            ->willReturn(true);

        
        $miseAJour = new MiseAJOUR();
        $miseAJour->pdo = $this->pdoMock; 

        $resultat = $miseAJour->miseAJour('1', 'Dupont', 'Jean', null, null);

        
        $this->assertEquals("Mise à jour réussie.", $resultat);
    }

    public function testMiseAJourAvecNumeroEtMdp()
    {
        
        $this->statementMock
            ->method('execute')
            ->willReturn(true);

        
        $miseAJour = new MiseAJOUR();
        $miseAJour->pdo = $this->pdoMock; 
 
        $resultat = $miseAJour->miseAJour('1', null, null, '0123456789', 'NewPassword123');
        $this->assertEquals("Mise à jour réussie.", $resultat);
    }

    public function testMiseAJourEchec()
    {
        
        $this->statementMock
            ->method('execute')
            ->willThrowException(new \Exception("Erreur de base de données"));

        
        $miseAJour = new MiseAJOUR();
        $miseAJour->pdo = $this->pdoMock; 

        
        $resultat = $miseAJour->miseAJour('1', 'Dupont', 'Jean', '0123456789', 'NewPassword123');
        $this->assertStringContainsString("Erreur lors de la mise à jour :", $resultat);
    }
}
?>