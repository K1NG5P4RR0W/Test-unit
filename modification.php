<?php


require_once('miseAJour.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['nom']) and isset($_POST['prenom']) && isset($_POST['numero']) && isset($_POST['mdp'])) {
        $id = isset($_POST['id']) ? intval($_POST['id']) : null;
        if ($id === null || $id <= 0) {
            die('ID invalide ou manquant.');
        }
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $numero = htmlspecialchars($_POST['numero']);
        $mdp = $_POST['mdp'];

        $update = new MiseAJOUR();
        $update->miseAJour($id, $nom, $prenom, $numero, $mdp);
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier informations utilisateurs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <style>
        .info-utilisateur {
            text-align: center;
            margin: 120px;
            padding: 40px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 15px 15px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="info-utilisateur">
            <?php
            require_once('BaseDeDonnees.php');
            try {
                // Vérification de l'ID dans l'URL
                if (isset($_GET['id']) && is_numeric($_GET['id'])) {
                    $id = intval($_GET['id']);

                    $bdd = new BaseDeDonnes();
                    $pdo = $bdd->connexion();

                    // Récupération des informations de l'utilisateur
                    $requete = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = :id");
                    $requete->bindParam(':id', $id, PDO::PARAM_INT);
                    $requete->execute();

                    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

                    if ($utilisateur) {
                        echo "<h1>Détails de l'utilisateur</h1>";
                        echo "<p><strong>ID :</strong> " . htmlspecialchars($utilisateur['id']) . "</p>";
                        echo "<p><strong>Nom :</strong> " . htmlspecialchars($utilisateur['nom']) . "</p>";
                        echo "<p><strong>Prénom :</strong> " . htmlspecialchars($utilisateur['prenom']) . "</p>";
                        echo "<p><strong>Pseudo :</strong> " . htmlspecialchars($utilisateur['pseudo']) . "</p>";
                        echo "<p><strong>Numéro :</strong> " . htmlspecialchars($utilisateur['numero']) . "</p>";
                    } else {
                        echo "<p style='color: red;'>Utilisateur non trouvé.</p>";
                    }
                } else {
                    echo "<p style='color: red;'>ID invalide ou manquant.</p>";
                }
            } catch (PDOException $e) {
                echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>";
            }
            ?>
        </div>

        <fieldset>
            <form action="" method="post">
                <h1>Modifier les informations</h1>
                <input type="hidden" name="typeFormu" value="inscription">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
                <div class="item">
                    <label for="nom">Modifiez le nom</label>
                    <input type="text" name="nom" id="">
                </div>

                <div class="item">
                    <label for="prenom">Modifier le prénom</label>
                    <input type="text" name="prenom" id="">
                </div>
                
                <div class="item">
                    <label for="numero">Modifier le numero de téléphone</label>
                    <input type="tel" name="numero" placeholder="Tel" aria-label="Tel" autocomplete="tel">
                </div>

                <div class="item">
                    <label for="mdp">Modifier le mot de passe </label>
                    <input type="password" name="mdp" id="">
                </div>
                <input type="submit" value="Mettre à jour" role="button" class="contrast">
        </fieldset>

    </div>
</body>

</html>