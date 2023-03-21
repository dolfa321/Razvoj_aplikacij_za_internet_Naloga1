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
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-6">
	<h2 class="mb-4">Prijava</h2>
<form action="login.php" method="POST">
	<div class="form-group">
		<label for="username">Uporabniško ime</label>
		<input type="text" class="form-control" id="username" name="username">
	</div>
	<div class="form-group">
		<label for="password">Geslo</label>
		<input type="password" class="form-control" id="password" name="password">
	</div>
	<button type="submit" class="btn btn-primary" name="submit">Pošlji</button>
	<div class="form-group mt-3">
		<label><?php echo $error; ?></label>
	</div>
	</div>
	</div>
	</div>
</form>

<?php
include_once('footer.php');
?>