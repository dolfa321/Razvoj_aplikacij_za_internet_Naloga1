<?php 
include_once('header.php');

//Funkcija izbere oglas s podanim ID-jem. Doda tudi uporabnika, ki je objavil oglas.
function get_ad($id){
	global $conn;
	$id = mysqli_real_escape_string($conn, $id);
	$query = "SELECT ads.*, users.username , users.post , users.telephone FROM ads LEFT JOIN users ON users.id = ads.user_id WHERE ads.id = $id;";
	$res = $conn->query($query);
	if($obj = $res->fetch_object()){
		return $obj;
	}
	return null;
}
function add_view($ad_id)
{

	$cookie_name = "ad_$ad_id";
    if (!isset($_COOKIE[$cookie_name])) {
	global $conn;
    $query = "SELECT ogledi FROM ads WHERE id='$ad_id'";
    $result = $conn->query($query);
    $row = mysqli_fetch_all($result, MYSQLI_NUM);
    $views_num = $row[0][0];
    $query = "UPDATE ads SET ads.ogledi = $views_num+1 WHERE id=$ad_id;";
    if (!$conn->query($query)) {
        return false;
    }
	setcookie($cookie_name, 1, time() + 86400);
    return true;
	}
	return false;
}
if(!isset($_GET["id"])){
	echo "Manjkajoči parametri.";
	die();
}
$id = $_GET["id"];
$ad = get_ad($id);
if($ad == null){
	echo "Oglas ne obstaja.";
	die();
}

add_view($id)
//Base64 koda za sliko (hexadecimalni zapis byte-ov iz datoteke)

?>
	<div class="ad">
		<h4><?php echo $ad->title;?></h4>
		<p><?php echo $ad->description;?></p>
		<p>Poštna številka: <?php echo $ad->post;?></p>
		<p>Telefonska: <?php echo $ad->telephone;?></p>
		<img src="<?php echo $ad->image;?>"/>
		<p>Objavil: <?php echo $ad->username; ?> Ogledi: <?php echo $ad->ogledi?></p>
		<a href="index.php"><button>Nazaj</button></a>
	</div>
	<hr/>
	<?php

include_once('footer.php');
?>