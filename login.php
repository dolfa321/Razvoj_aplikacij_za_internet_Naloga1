<?php
include_once('header.php');

function validate_login($username, $password){
	global $conn;
	$username = mysqli_real_escape_string($conn, $username);
	$pass = sha1($password);
	$query = "SELECT * FROM users WHERE username='$username' AND password='$pass'";
	$res = $conn->query($query);
	if($user_obj = $res->fetch_object()){
		return $user_obj->id;
	}
	return -1;
}

$error="";
if(isset($_POST["submit"])){
	//Preveri prijavne podatke
	if(($user_id = validate_login($_POST["username"], $_POST["password"])) >= 0){
		//Zapomni si prijavljenega uporabnika v seji in preusmeri na index.php
		$_SESSION["USER_ID"] = $user_id;
		header("Location: index.php");
		die();
	} else{
		$error = "Prijava ni uspela.";
	}
}
?>
	<h2>Prijava</h2>
	<form action="login.php" method="POST">
		<label>Uporabniško ime</label><input type="text" name="username" /> <br/>
		<label>Geslo</label><input type="password" name="password" /> <br/>
		<input type="submit" name="submit" value="Pošlji" /> <br/>
		<label><?php echo $error; ?></label>
	</form>
<?php
include_once('footer.php');
?>