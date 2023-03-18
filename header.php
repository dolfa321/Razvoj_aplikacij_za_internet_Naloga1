<?php
	session_start();
	
	//Seja poteče po 30 minutah - avtomatsko odjavi neaktivnega uporabnika
	if(isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] < 1800){
		session_regenerate_id(true);
	}
	$_SESSION['LAST_ACTIVITY'] = time();
	
	//Poveži se z bazo
	$conn = new mysqli('localhost', 'root', '', 'vaja1');
	//Nastavi kodiranje znakov, ki se uporablja pri komunikaciji z bazo
	$conn->set_charset("UTF8");
?>
<html>
<head>
	<title>Vaja 1</title>
</head>
<body>
	<h1>Oglasnik</h1>
	<nav>
		<ul>
			<li><a href="index.php">Domov</a></li>
			<?php
			if(isset($_SESSION["USER_ID"])){
				?>
				<li><a href="publish.php">Objavi oglas</a></li>
				<li><a href="logout.php">Odjava</a></li>
				<li><a href="ad.php">Moji Oglasi</a></li>
				<?php
			} else{
				?>
				<li><a href="login.php">Prijava</a></li>
				<li><a href="register.php">Registracija</a></li>
				<?php
			}
			?>
		</ul>
	</nav>
	<hr/>