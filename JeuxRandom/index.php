<?php
include('connexion.php');
	session_start();
//Si on as cliqué sur Logout
if(isset($_POST['logout'])){
	if($_SESSION['gagner'] == false){
		$reponse = ('UPDATE `utilisateurs` SET `nb` = :choice, `incr` = :incr WHERE `id` = :id');
		$preparation = $database->prepare($reponse);
		$preparation->bindParam('choice', $_SESSION['choice'], PDO::PARAM_STR);
		$preparation->bindParam('id', $_SESSION['id'], PDO::PARAM_STR);
		$preparation->bindParam('incr', $_SESSION['incr'], PDO::PARAM_STR);
		$preparation->execute();
	}
	session_destroy();
	header("Location: Login.php");
	exit;
}
//Si on est pas passé par la page de LOGIN
if(!isset($_SESSION["logged"]) || $_SESSION['logged'] == false){
	header("Location: Login.php");
	exit;
}else{
	//Recupération du highscore de la BDD
	$reponse = ('SELECT `bestscore`, `incr`, `nb` FROM utilisateurs WHERE `id` = :id');
	$preparation = $database->prepare($reponse);
	$preparation->bindParam('id', $_SESSION['id'], PDO::PARAM_STR);
	$preparation->execute();
	//fetch sans boucle pour recup le best score.
	$donnees = $preparation->fetch();
	$bestscore = $donnees['bestscore'];
	echo ("<p>Votre meilleur score : ".$bestscore."<br><p>");
	if(!isset($_SESSION['incr'])){
		$_SESSION['incr'] = $donnees['incr'];
	}
	//Récupération de la partie en cours, ou bien random.
	$nb = $donnees['nb'];
	if($nb != NULL){
		$_SESSION['choice'] = $nb;
		echo("Votre dernier nombre à bien été récupéré !");
		$_SESSION['gagner'] = false;
	}

	if(!isset($_SESSION["choice"])){
		$_SESSION["choice"] = rand(0,100);
		$_SESSION["incr"] = 1;
		$_SESSION['gagner'] = false;
	}
	if(!isset($_POST['guess']) || empty($_POST['guess']) || $_SESSION['gagner'] == true) {
		$response = "Le jeu n'a pas encore choisi de nombre...";
	}
	else{
		if($_POST['guess'] > $_SESSION["choice"]) {
			$response = "C'est moins";
			$_SESSION["incr"]++;
		}
		elseif($_POST['guess'] < $_SESSION["choice"]) {
			$response = "C'est plus";
			$_SESSION["incr"]++;
		}
		else{
			$response = "<br>C'est gagné !<br>"." Vous avez essayé " . $_SESSION["incr"] ." fois";
			$_SESSION['gagner'] = true;
			if($bestscore == 0 || $_SESSION["incr"] < $bestscore){
				$bestscore = $_SESSION["incr"];
				//On efface la sauvegarde de la bdd de nb si le nb à été trouvé.
				$reponse = ('UPDATE `utilisateurs` SET `bestscore`= :bestscore, `incr` = 1 WHERE `id` = :id');
				$preparation = $database->prepare($reponse);
				$preparation->bindParam('bestscore', $bestscore, PDO::PARAM_STR);
				$preparation->bindParam('id', $_SESSION['id'], PDO::PARAM_STR);
				$preparation->execute();
			}
			unset($_SESSION["choice"]);
			unset($_SESSION["incr"]);
			$reponse = ('UPDATE `utilisateurs` SET `nb` = NULL WHERE `id` = :id');
			$preparation = $database->prepare($reponse);
			$preparation->bindParam('id', $_SESSION['id'], PDO::PARAM_STR);
			$preparation->execute();

			$reponse = ('SELECT `bestscore`, `pseudo` FROM utilisateurs ORDER BY `bestscore`');
			$preparation = $database->prepare($reponse);
			$preparation->execute();
			//boucle pour récupérer les meilleurs scores des joueurs et leurs noms dans un tableau
			$incr = 0;
			echo("<br>Les meilleurs scores : <table class='score' border='1px' class='tableBest' border='1px'><tr>");
			while(($donnees = $preparation->fetch()) && $incr<=10){
					$pseudo = $donnees['pseudo'];
					echo("<td>".$pseudo."</td>");
					$score = $donnees['bestscore'];
					echo("<td>".$score."</td></tr>");
					$incr++;
				}
			echo("</table><br>");
}
		}
	}


?>

<script type="text/javascript">
	window.onload = function(){
		document.getElementsByName('guess')[0].focus();
	}
</script>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Des papiers dans un bol</title>
	<link rel="stylesheet" href="index.css">
</head>
<body>
<div class="formu">
	<?php
	echo $response."<br>";
	?>

	<form method="POST">
		<br><input type="number" name="guess"><br><br>
		<input type="submit"><br><br>
		<input type="submit" name="logout" value="Se déconnecter">
	</form>
</div>
</body>
</html>