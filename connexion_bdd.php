
<?php
	define('host', 'localhost');
	define('user', 'root');
	define('pass', '');
	define('DBname', 'dolibarr');

	$con = mysqli_connect(host, user, pass, DBname);
	

/* vérifier que la connexion a reussi */
	if ($con->connect_errno) {
		echo("connection reussie");
		printf("Connect failed: %s\n", $con->connect_error);
		exit();
	}

?>