<?php

require('src/BaseDeDonnees.php');
require('src/connexion.php');

use PHPUnit\Framework\TestCase;
use App\Session;

class deconnexionTest extends TestCase
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

    

    public function testDeconnexion()
    {
        
        $_SESSION['id_user'] = 1;
        $_SESSION['Pseudo'] = 'jdupont';

        $session = new Session();
        $session->deconnexion();

       
        $this->assertEmpty($_SESSION);
    }
}
?>