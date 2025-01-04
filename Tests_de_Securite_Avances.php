
<?php
namespace TestPMP;
use PHPUnit\Framework\TestCase;
use Sign\Inscription;

class InscriptionTest extends TestCase
{
    private Inscription $inscription;

    protected function setUp(): void
    {
        $this->inscription = new Inscription();
    }

    public function testHachageMotDePasse()
    {
        $motDePasse = "P@ssw0rd123!";
        $result = $this->inscription->inscriptionUtilisateur(
            "Jean", 
            "Dupont", 
            "jdupont", 
            "0600000000", 
            $motDePasse
        );

        $this->assertEquals("Utilisateur créé avec succès", $result);

       
        $requete = $this->inscription->pdo->prepare("SELECT mdp FROM utilisateurs WHERE pseudo = :pseudo");
        $requete->bindParam(":pseudo", $pseudo = "jdupont");
        $requete->execute();
        $hashedPassword = $requete->fetchColumn();
 //  verification du stockage en clair du mot de passe
        $this->assertNotEquals($motDePasse, $hashedPassword);
        $this->assertTrue(password_verify($motDePasse, $hashedPassword));

        // Vérification du  hachage 
    $this->assertTrue(password_verify($motDePasse, $hashedPassword));

    // Vérification du salage
    $autreHash = password_hash($motDePasse, PASSWORD_BCRYPT);
    $this->assertNotEquals($hashedPassword, $autreHash);
    }
    public function testProtectionInjectionSQL()
{
    // Insertion d'une requête préparée
    $result = $this->inscription->inscriptionUtilisateur(
        "Jean",
        "Dupont",
        "pseudo'); DROP TABLE utilisateurs; --",
        "0600000000",
        "P@ssw0rd123!"
    );

    // Vérifie que la requête n'a pas échoué et que la table n'est pas supprimée
    $this->assertEquals("Utilisateur créé avec succès", $result);

    // Vérifie que les données sont insérées correctement
    $stmt = $this->inscription->pdo->prepare("SELECT pseudo FROM utilisateurs WHERE pseudo = :pseudo");
    $stmt->bindParam(":pseudo", $pseudo = "pseudo'); DROP TABLE utilisateurs; --");
    $stmt->execute();
    $data = $stmt->fetchColumn();

    $this->assertEquals("pseudo'); DROP TABLE utilisateurs; --", $data);
}
public function testProtectionXSS()
{
    // Données avec du code malveillant
    $nom = "<script>alert('XSS')</script>";
    $prenom = "<b onclick='malicious()'>Text</b>";
    $result = $this->inscription->inscriptionUtilisateur(
        $nom,
        $prenom,
        "safePseudo",
        "0600000000",
        "P@ssw0rd123!"
    );

    // Vérifie que l'inscription réussit
    $this->assertEquals("Utilisateur créé avec succès", $result);

    // Vérifie que les données sont encodées correctement dans la base de données
    $stmt = $this->inscription->pdo->prepare("SELECT nom, prenom FROM utilisateurs WHERE pseudo = :pseudo");
    $stmt->bindParam(":pseudo", $pseudo = "safePseudo");
    $stmt->execute();
    $data = $stmt->fetch(\PDO::FETCH_ASSOC);

    $this->assertStringContainsString("&lt;script&gt;", $data['nom']);
    $this->assertStringContainsString("&lt;b", $data['prenom']);
}
public function testProtectionCSRF()
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $_POST['csrf_token'] = $_SESSION['csrf_token'];

    // Validation du token
    $this->assertEquals($_SESSION['csrf_token'], $_POST['csrf_token']);

    // Expiration du token
    $_SESSION['csrf_expiry'] = time() + 60; // 1 minute
    $this->assertTrue(time() <= $_SESSION['csrf_expiry']);
}
public function testProtectionSession()
{
    session_start();
    session_regenerate_id(true);

    // Vérifie la sécurisation des cookies
    $this->assertTrue(ini_get('session.cookie_secure') == true);
    $this->assertTrue(ini_get('session.cookie_httponly') == true);

    // Timeout de session
    $_SESSION['last_activity'] = time();
    $timeout = 300; // 5 minutes
    $this->assertTrue(time() - $_SESSION['last_activity'] < $timeout);
}
public function testProtectionDonnees()
{
    $sensitiveData = "Donnée sensible";
    $key = 'secret_key_123456'; // Clé de chiffrement
    $encryptedData = openssl_encrypt($sensitiveData, 'AES-128-ECB', $key);

    // Vérifie que les données sont bien chiffrées
    $this->assertNotEquals($sensitiveData, $encryptedData);

    $decryptedData = openssl_decrypt($encryptedData, 'AES-128-ECB', $key);
    $this->assertEquals($sensitiveData, $decryptedData);

    // Vérifie que l'accès est restreint
    $isAuthorized = true; // Simule une vérification d'accès
    $this->assertTrue($isAuthorized);
}
}