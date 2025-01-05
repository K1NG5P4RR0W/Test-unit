<?php

use PHPUnit\Framework\TestCase;
use App\Inscription;

class MessagesTest extends TestCase
{
    private $pdoMock;

    protected function setUp(): void
    {
        
        $this->pdoMock = $this->createMock(\PDO::class);
    }

    public function testPseudoExistantGenereErreur()
    {
        
        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetchColumn')->willReturn(1); 

        $this->pdoMock->method('prepare')->willReturn($stmtMock);

        
        $inscription = new Inscription();
        $inscription->pdo = $this->pdoMock;

        
        $result = $inscription->inscriptionUtilisateur(
            'John', 'Doe', 'existentPseudo', '1234567890', 'Password!1'
        );

        
        $this->assertStringContainsString(
            'Le pseudo existe déjà. Veuillez en choisir un autre.',
            $result
        );
    }

    public function testMotDePasseNonConformeGenereErreurs()
    {
       
        $inscription = new Inscription();
        $inscription->pdo = $this->pdoMock;

        
        $result = $inscription->inscriptionUtilisateur(
            'John', 'Doe', 'newPseudo', '1234567890', 'weak'
        );

        
        $this->assertStringContainsString(
            'Le mot de passe doit contenir au moins 8 caractères.',
            $result
        );
        $this->assertStringContainsString(
            'Le mot de passe doit contenir au moins une lettre majuscule.',
            $result
        );
        $this->assertStringContainsString(
            'Le mot de passe doit contenir au moins une lettre minuscule.',
            $result
        );
        $this->assertStringContainsString(
            'Le mot de passe doit contenir au moins un chiffre.',
            $result
        );
        $this->assertStringContainsString(
            'Le mot de passe doit contenir au moins un caractère spécial.',
            $result
        );
    }

    public function testAucuneErreurAvecDonneesValides()
    {
        
        $stmtMock = $this->createMock(\PDOStatement::class);
        $stmtMock->method('execute')->willReturn(true);
        $stmtMock->method('fetchColumn')->willReturn(0); 

        $this->pdoMock->method('prepare')->willReturn($stmtMock);

        
        $inscription = new Inscription();
        $inscription->pdo = $this->pdoMock;

        
        $result = $inscription->inscriptionUtilisateur(
            'John', 'Doe', 'newPseudo', '1234567890', 'Password!1'
        );

        
        $this->assertEquals('Utilisateur créé avec succès', $result);
        $this->assertEmpty($inscription->getErrors());
    }
}
