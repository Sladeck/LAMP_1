<?php
//On essai de se connecter à la BDD
try
{
	// Connexion à la base de données
	$database = new PDO('mysql:host=localhost;dbname=jeuxrandom;charset=utf8', 'root', '');

	// Configuration du pilote : nous voulons des exceptions
	$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Obligatoire pour la suite
}
catch(Exception $e)
{
	echo "Échec : " . $e->getMessage();
}

if($database==true){
	
}else{
	echo('HE NON !');
}