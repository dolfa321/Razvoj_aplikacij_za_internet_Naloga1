<?php
include_once('header.php');

// Funkcija preveri, ali v bazi obstaja uporabnik z določenim imenom in vrne true, če obstaja.
function username_exists($username){
	global $conn;
	$username = mysqli_real_escape_string($conn, $username);
	$query = "SELECT * FROM users WHERE username='$username'";
	$res = $conn->query($query);
	return mysqli_num_rows($res) > 0;
	//Testiranje adasdaddasd
}

// Funkcija ustvari uporabnika v tabeli users. Poskrbi tudi za ustrezno šifriranje uporabniškega gesla.
function register_user($username, $password, $firstname, $lastname, $post, $phone, $gender, $age){
	global $conn;
	$username = mysqli_real_escape_string($conn, $username);
	$pass = sha1($password);
	/* 
		Tukaj za hashiranje gesla uporabljamo sha1 funkcijo. V praksi se priporočajo naprednejše metode, ki k geslu dodajo naključne znake (salt).
		Več informacij: 
		http://php.net/manual/en/faq.passwords.php#faq.passwords 
		https://crackstation.net/hashing-security.htm
	*/
	$query = "INSERT INTO users (username, password, firstname, lastname, post, telephone, gender, age) VALUES ('$username', '$pass', '$firstname', '$lastname', '$post', '$phone','$gender','$age');";
	if($conn->query($query)){
		return true;
	}
	else{
		echo mysqli_error($conn);
		return false;
	}
}

$error = "";
if(isset($_POST["submit"])){
	/*
		VALIDACIJA: preveriti moramo, ali je uporabnik pravilno vnesel podatke (unikatno uporabniško ime, dolžina gesla,...)
		Validacijo vnesenih podatkov VEDNO izvajamo na strežniški strani. Validacija, ki se izvede na strani odjemalca (recimo Javascript), 
		služi za bolj prijazne uporabniške vmesnike, saj uporabnika sproti obvešča o napakah. Validacija na strani odjemalca ne zagotavlja
		nobene varnosti, saj jo lahko uporabnik enostavno zaobide (developer tools,...).
	*/
	//Preveri če se gesli ujemata
	if($_POST["password"] != $_POST["repeat_password"]){
		$error = "Gesli se ne ujemata.";
	}
	//Preveri ali uporabniško ime obstaja
	else if(username_exists($_POST["username"])){
		$error = "Uporabniško ime je že zasedeno.";
	}
	//Podatki so pravilno izpolnjeni, registriraj uporabnika
	else if(register_user($_POST["username"], $_POST["password"],
	$_POST["firstname"], $_POST["lastname"], $_POST["post"], $_POST["phone"],$_POST["gender"], $_POST["age"])){
		header("Location: login.php");
		die();
	}
	//Prišlo je do napake pri registraciji
	else{
		$error = "Prišlo je do napake med registracijo uporabnika.";
	}
}

?>
	<div class="container">
	<div class="row justify-content-center">
		<div class="col-md-6">
			<h2 class="text-center">Registracija</h2>
			<form action="register.php" method="POST">
				<div class="form-group">
					<label for="username">Uporabniško ime:</label>
					<input type="text" name="username" id="username" class="form-control" required>
				</div>
				<div class="form-group">
					<label for="password">Geslo:</label>
					<input type="password" name="password" id="password" class="form-control" required>
				</div>
				<div class="form-group">
					<label for="repeat_password">Ponovi geslo:</label>
					<input type="password" name="repeat_password" id="repeat_password" class="form-control" required>
				</div>
				<div class="form-group">
					<label for="firstname">Ime:</label>
					<input type="text" name="firstname" id="firstname" class="form-control" required>
				</div>
				<div class="form-group">
					<label for="lastname">Priimek:</label>
					<input type="text" name="lastname" id="lastname" class="form-control" required>
				</div>
				<div class="form-group">
					<label for="post">Pošta:</label>
					<input type="number" name="post" id="post" class="form-control" required>
				</div>
				<div class="form-group">
					<label for="phone">Telefonska številka:</label>
					<input type="tel" name="phone" id="phone" class="form-control" required>
				</div>
				<div class="form-group">
					<label>Spol:</label><br>
					<div class="form-check form-check-inline">
						<input type="radio" name="gender" id="male" value="m" class="form-check-input">
						<label for="male" class="form-check-label">Moški</label>
					</div>
					<div class="form-check form-check-inline">
						<input type="radio" name="gender" id="female" value="f" class="form-check-input">
						<label for="female" class="form-check-label">Ženska</label>
					</div>
				</div>
				<div class="form-group">
					<label for="age">Starost:</label>
					<input type="number" name="age" id="age" class="form-control" required>
				</div>
				<div class="form-group">
					<input type="submit" name="submit" value="Pošlji" class="btn btn-primary">
				</div>
				<div class="form-group">
					<label><?php echo $error; ?></label>
				</div>
			</form>
		</div>
	</div>
</div>

<?php
include_once('footer.php');
?>