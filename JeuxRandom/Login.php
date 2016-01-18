<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" href="main.css">
</head>
<body>

<?php
include('connexion.php');
session_start();
//On définie logged en false
$_SESSION['logged'] = false;

//On vérifie si l'utilisateur à cliqué et si les variables existent.
if(isset($_POST['valid']) && isset($_POST['MDP'])){
	//Requête sur la BDD, selection de toute la table.
	$query = ('SELECT * FROM utilisateurs WHERE `login` = :password');
	$preparation = $database->prepare($query);
	$encryptPass = sha1($_POST['MDP']);
	$preparation->bindParam('password', $encryptPass, PDO::PARAM_STR);
	$preparation->execute();

	//Boucle pour récupérer les donnees de la table.
	while($donnees = $preparation->fetch()){
		$pass = $donnees['login'];
		$id = $donnees['id'];
	}

}
//Vérification de l'existence de Login et si le password de la bdd est égal au password donné.
if(isset($pass) && $pass === sha1($_POST['MDP'])){
	//On définie logged en true pour la vérif sur la page index.php.
	$_SESSION['logged'] = true;
	$_SESSION['id'] = $id;
	//On redirige vers index.php si tout est bon
	header("Location: index.php");
	exit;
}else{
	echo("<p>Veuillez rentrer votre mot de passe :</p><br>");
	echo("<form action='Login.php' method='POST'><input type='password' name='MDP'><br><br>");
	echo("<input type='submit' value='Valider' name='valid'></form>");
}

?>

<script type="text/javascript">
	window.onload = function(){
		document.getElementsByName('MDP')[0].focus();
	}
</script>

</body>
</html>