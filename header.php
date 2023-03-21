<?php
session_start();

// Function to get username by USER_ID
function getUsername($conn, $user_id)
{
	$sql = "SELECT username FROM users WHERE id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->bind_param("i", $user_id);
	$stmt->execute();
	$stmt->bind_result($username);
	$stmt->fetch();
	$stmt->close();
	return $username;
}


//Seja poteče po 30 minutah - avtomatsko odjavi neaktivnega uporabnika
if (isset($_SESSION['LAST_ACTIVITY']) && time() - $_SESSION['LAST_ACTIVITY'] < 1800) {
	session_regenerate_id(true);
}
$_SESSION['LAST_ACTIVITY'] = time();

//Poveži se z bazo
$conn = new mysqli('localhost', 'root', '', 'vaja1');
//Nastavi kodiranje znakov, ki se uporablja pri komunikaciji z bazo
$conn->set_charset("UTF8");

?>
<!DOCTYPE html>
<html>

<head>
	<title>Vaja 1</title>
	<!-- Add Bootstrap CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="#">Oglasnik</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
			aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav">
				<li class="nav-item active">
					<a class="nav-link" href="index.php">Domov</a>
				</li>
				<?php if (isset($_SESSION["USER_ID"])) { ?>
					<li class="nav-item">
						<a class="nav-link" href="publish.php">Objavi oglas</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="myad.php">Moji oglasi</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="logout.php">Odjava</a>
					</li>
					<div class="navbar-nav ml-auto">
						<li class="nav-item">
							<span class="navbar-text">
								Prijavljeni ste kot:
								<?php echo getUsername($conn, $_SESSION["USER_ID"]); ?>
							</span>
						</li>
					</div>
				</ul>
			<?php } else { ?>
				<ul class="navbar-nav ml-auto">
					<li class="nav-item">
						<a class="nav-link" href="login.php">Prijava</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="register.php">Registracija</a>
					</li>
				</ul>
			<?php } ?>
		</div>
	</nav>


	<!-- Add Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>

</html>