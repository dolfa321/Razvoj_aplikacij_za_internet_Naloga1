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
	<h2>Registracija</h2>
	<form action="register.php" method="POST">
		<label>Uporabniško ime: </label><input type="text" name="username" /> <br/>
		<label>Geslo: </label><input type="password" name="password" /> <br/>
		<label>Ponovi geslo: </label><input type="password" name="repeat_password" /> <br/>	
		<label>Ime: </label><input type="text" name="firstname" /><br/>
		<label>Priimek: </label><input type="text" name="lastname" /><br/>
		<lable>Pošta: </label><input type="number" name="post"/><br/>
		<label>Telefonska številka: </label> <input type="tel" name="phone"/> <br/>
		<lable>Spol: </label></br>
		<label>Moški</label><input type="radio" name="gender" id="male" value="m"/>
		<label>Ženska</label><input type="radio" name="gender" id="femal" value="m"> <br/>
		<label>Starost: </label><input type="number" name="age"/><br/>
		<input type="submit" name="submit" value="Pošlji" /> <br/>
		<label><?php echo $error; ?></label>
	</form>
<?php
include_once('footer.php');
?>