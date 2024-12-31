<?php


require_once('inscription.php');
require_once('connexion.php');
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD']==='POST'){
    if (isset($_POST['typeFormu']) && $_POST['typeFormu'] === 'inscription') {
        if (isset($_POST['nom']) and isset($_POST['prenom']) && isset($_POST['numero']) && isset($_POST['pseudo']) && isset($_POST['mdpFirst']) && isset($_POST['mdp'])) {
            $nom = htmlspecialchars($_POST['nom']);
            $prenom = htmlspecialchars($_POST['prenom']);
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $numero = htmlspecialchars($_POST['numero']);
            $mdpFirst = $_POST['mdpFirst'];
            $mdp = $_POST['mdp'];
            if ($mdpFirst === $mdp) {
                $inscrire = new Inscription();
                $inscrire->inscriptionUtilisateur($nom, $prenom, $pseudo, $numero, $mdp);
            } else {
                echo 'les mots de passes ne correspondent pas';
            }
        }
    } 
    elseif (isset($_POST['typeFormu']) && $_POST['typeFormu'] === 'connexion') {
        if (isset($_POST['pseudo']) && isset($_POST['mdp'])) {
            $pseudo = $_POST['pseudo'];
            $mdp = $_POST['mdp'];

            $connecter = new Session();
            $connecter->sessionUtilisateur($pseudo, $mdp);
        }
    }
    else {
        echo "Une erreur c'est produite";
    } 
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="pico-main/css/pico.min.css">
    <title>Page d'inscription</title>
    <style>
        h1 {
            text-align: center;
            font-weight: 800;

        }

      
      
    </style>
</head>

<body>
    <main class="container">
        <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'login';
        ?>
        <?php if ($page === 'register') { ?>
            <fieldset>
                <form action="" method="post">
                    <h1>Page d'inscription</h1>
                    <input type="hidden" name="typeFormu" value="inscription">
                    <div class="item">
                        <label for="nom">Entrez votre nom</label>
                        <input type="text" name="nom" id="" required>
                    </div>

                    <div class="item">
                        <label for="prenon">Entrez votre prénom</label>
                        <input type="text" name="prenom" id="" required>
                    </div>
                    <div>
                        <label for="pseudo">Définissez votre pseudo</label>
                        <input type="text" name="pseudo" id="pseudo">
                    </div>
                    <div class="item">
                        <label for="numero">Quel est votre numero de Téléphone ?</label>
                        <input type="tel" name="numero" placeholder="Tel" aria-label="Tel" autocomplete="tel">
                    </div>

                    <div class="item">
                        <label for="mdp">Définissez un mot de passe </label>
                        <input type="password" name="mdpFirst" id="" required>
                    </div>
                    <div class="item">
                        <label for="">Confirmez votre mot de passe</label>
                        <input type="password" name="mdp" required>
                    </div>
                    <input type="submit" value="S'inscrire" role="button" class="contrast">
            </fieldset>

            
            </form>
            <a href="?page=login">Déjà inscrit ? Connectez-vous ici</a>
        <?php } elseif ($page === 'login') { ?>
            <form action="" method="post">
                <fieldset>
                    <input type="hidden" name="typeFormu" value="connexion">
                    <div>
                        <label for="pseudo">Entrez votre pseudo</label>
                        <input type="text" name="idendifiant">
                    </div>

                    <div>
                        <label for="mot_de_passe">Entrez votre mot de passe</label>
                        <input type="password" name="mdp" id="">
                    </div>
                </fieldset>

                <input type="submit" value="Se connecter" role="button" class="contrast">
            </form>
            <div scope="row">
                <a href="?page=register">Pas encore inscrit ?</a>
            <?php } else { ?>
                <!-- Page par défaut ou erreur -->
                <h2>Erreur</h2>
                <p>La page demandée est introuvable.</p>
                <a href="?page=login">Retour à la connexion</a>
            <?php } ?>
            </div>

    </main>
</body>

</html>