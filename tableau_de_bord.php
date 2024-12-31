<?php
require_once('BaseDeDonnees.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="pico-main/css/pico.min.css">
    <style>
        body {
            padding: 60px 60px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        h1{
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div>
            <h1>Tableau de bord</h1>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Pseudo</th>
                    <th>Numéro</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {

                    $bdd = new BaseDeDonnes();
                    $pdo = $bdd->connexion();


                    $requete = $pdo->query("SELECT nom, prenom, pseudo, numero FROM utilisateurs");


                    while ($row = $requete->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['pseudo']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['numero']) . "</td>";
                        echo "</tr>";
                    }
                } catch (PDOException $e) {
                    echo "</br><p style='color: red; text-align: center;'>Erreur : " . $e->getMessage() . "</p>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>l