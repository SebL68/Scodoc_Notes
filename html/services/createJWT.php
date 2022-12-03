<?php
/***************************************/
/* Service de création de token JWT   /*
/* https://github.com/firebase/php-jwt */
/***************************************/

	$path = realpath($_SERVER['DOCUMENT_ROOT'] . '/..');

	include_once "$path/includes/default_config.php";
	include_once "$path/includes/user.class.php";
	$user = new User();

	if(
		$user->getId() != 'sebastien.lehmann@uha.fr' &&
		$user->getId() != 'denis.graef@uha.fr'
	){ 
		die("Ce service n'est autorisé que pour Sébastien Lehmann, vous pouvez le contacter.");
	}

	use \Firebase\JWT\JWT;

	include $path . '/lib/JWT/JWT.php';

	$exp = time() + 7 * 3600 * 24 ; // today + 7 days
    $root_url = (isset($_SERVER["https"]) ? "https://" : "http://" ). $_SERVER["HTTP_HOST"];
	$payload = [
		'id' => 'sebastien.lehmann@uha.fr', // nip, ou idCAS, si la personne n'a pas de nip
		'idCAS' => 'sebastien.lehmann@uha.fr',
		'name' => 'Seb',
		'statut' => 'superadministrateur', // 'etudiant' | 'personnel' | 'administrateur' | 'superadministrateur' | INCONNU
		'exp' => $exp // (optionnel) timestamp d'expiration du token
	];
	echo $root_url."?token=".JWT::encode($payload, $Config->JWT_key);
?>
