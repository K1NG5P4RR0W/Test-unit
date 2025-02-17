<?php


require_once('src/inscription.php');
require_once('src/connexion.php');
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['typeFormu']) && $_POST['typeFormu'] === 'inscription') {
        if (isset($_POST['nom']) and isset($_POST['prenom']) && isset($_POST['numero']) && isset($_POST['pseudo']) && isset($_POST['mdpFirst']) && isset($_POST['mdp'])) {
            $nom = htmlspecialchars($_POST['nom']);
            $prenom = htmlspecialchars($_POST['prenom']);
            $pseudo = htmlspecialchars($_POST['pseudo']);
            $numero = htmlspecialchars($_POST['numero']);
            $mdpFirst = $_POST['mdpFirst'];
            $mdp = $_POST['mdp'];


            if (empty($nom)) {
                $fieldErrors['nom'] = "Le nom est requis.";
            }


            if (empty($pseudo)) {
                $fieldErrors['pseudo'] = "Le pseudo est requis";
            }

            if (empty($mdpFirst) && empty($mdp)) {
                $fieldErrors['mdpFirst'] = "Le mot de passe est requis.";
            } else {
                // Vérification de la force du mot de passe
                $inscrire = new App\Inscription();
                $passwordErrors = $inscrire->verifierMotDePasseFort($mdp);

                if (!empty($passwordErrors) && is_array($passwordErrors)) {
                    $fieldErrors['mdpFirst'] = implode('<br>', $passwordErrors);
                }
            }

            if ($mdpFirst !== $mdp) {
                $fieldErrors['mdp'] = "Les mots de passe ne correspondent pas.";
            }

            $inscrire = new App\Inscription();
            $inscrire->inscriptionUtilisateur($nom, $prenom, $pseudo, $numero, $mdp);
        }
    } elseif (isset($_POST['typeFormu']) && $_POST['typeFormu'] === 'connexion') {
        if (isset($_POST['pseudo']) && isset($_POST['mdp'])) {
            $pseudo = $_POST['pseudo'];
            $mdp = $_POST['mdp'];

            if (empty($pseudo)) {
                $fieldErrors['pseudo'] = "Le pseudo est requis.";
            }

            if (empty($mdp)) {
                $fieldErrors['mdp'] = "Le mot de passe est requis.";
            }

            if (empty($fieldErrors)) {
                try {
                    $connecter = new Session();
                    if ($connecter->sessionUtilisateur($pseudo, $mdp)) {
                        echo "<p style='color:green;'>Connexion réussie !</p>";
                    } else {
                        $fieldErrors['general'] = "Pseudo ou mot de passe incorrect.";
                    }
                } catch (Exception $e) {
                    $fieldErrors['general'] = "Erreur lors de la connexion : " . $e->getMessage();
                }
            }
        }
    } else {
        echo "Une erreur c'est produite";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <title>Page d'inscription</title>
    <style>
        h1 {
            text-align: center;
            font-weight: 800;

        }

        .error {
            color: red;
            padding: 20px 20px;
            font-size: 15px;
            background-color: rgba(213, 213, 212, 0.86);
            border-radius: 10px;
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
                        <?php if (isset($fieldErrors['nom'])) { ?>
                            <p class="error"><?= $fieldErrors['nom'] ?></p>
                        <?php } ?>
                    </div>

                    <div class="item">
                        <label for="prenon">Entrez votre prénom</label>
                        <input type="text" name="prenom" id="" required>

                    </div>

                    <div>
                        <label for="pseudo">Définissez votre pseudo</label>
                        <input type="text" name="pseudo" id="pseudo" required>
                        <?php if (isset($fieldErrors['pseudo'])) { ?>
                            <p class="error"><?= $fieldErrors['pseudo'] ?></p>
                            <?php if (in_array("Le pseudo existe déjà. Veuillez en choisir un autre.", $errorMessages)) {
                                echo "<div class=\"error\" style='color: red;'>Le pseudo existe déjà. Veuillez en choisir un autre.</div>";
                            } ?>
                        <?php } ?>
                    </div>

                    <div class="item">
                        <label for="numero">Quel est votre numero de Téléphone ?</label>
                        <input type="tel" name="numero" placeholder="Tel" aria-label="Tel" autocomplete="tel">
                    </div>

                    <div class="item">
                        <label for="mdp">Définissez un mot de passe </label>
                        <input type="password" name="mdpFirst" id="" required>
                        <?php if (isset($fieldErrors['mdpFirst'])) { ?>
                            <p class="error"><?= $fieldErrors['mdpFirst'] ?></p>
                        <?php } ?>
                    </div>

                    <div class="item">
                        <label for="">Confirmez votre mot de passe</label>
                        <input type="password" name="mdp" required>
                        <?php if (isset($fieldErrors['mdp'])) { ?>
                            <p class="error"><?= $fieldErrors['mdp'] ?></p>
                        <?php } ?>
                    </div>

                    <input type="submit" value="S'inscrire" role="button" class="contrast">
            </fieldset>


            </form>
            <a href="?page=login">Déjà inscrit ? Connectez-vous ici</a>
        <?php } elseif ($page === 'login') { ?>
            <div></div>
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