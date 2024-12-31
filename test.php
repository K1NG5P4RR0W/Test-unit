<?php
$mdp='unmotdepasse_solide';
$mdp_hash= password_hash($mdp, PASSWORD_BCRYPT);
echo $mdp_hash;
?>