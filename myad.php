<?php 
include_once('header.php');
function get_advertisement($id)
{
    //echo $id;
    global $conn;
    $query = "SELECT ads.*, users.username 
    FROM ads 
    LEFT JOIN users ON users.id = ads.user_id 
    WHERE ads.user_id = $id;";
    $res = $conn->query($query);
    $ads = array();
    while($ad = $res->fetch_object()) {
        array_push($ads,$ad);
    }
    return $ads;
}
function delete($id)
{
    global $conn;
    $query = "DELETE FROM categories_ads WHERE id_ad='$id';";
    if (!$conn->query($query)) {
        return false;
    }
    $query = "DELETE FROM ads WHERE id='$id';";
    if (!$conn->query($query)) {
        return false;
    }
    return true;
}
if (isset($_POST["delete"])) {
    $ad_id = $_POST["ad_id"];
    if (delete($ad_id)) {
        header("Location: index.php");
    } else {
        $error = "Prišlo je do napake pri brisanju.";
    }
}
$ads = get_advertisement($_SESSION["USER_ID"]);

foreach($ads as $ad){
	?>
	<div class="ad">
		<h4><?php echo $ad->title;?></h4>
		<p><?php echo $ad->description;?></p>
		<img src="<?php echo $ad->image;?>"/>
		<p>Ogledi: <?php echo $ad->ogledi?></p>
        <form method="post">
    <input type="hidden" name="ad_id" value="<?php echo $ad->id; ?>">
    <button type="submit" name="delete">Briši</button>
</form>
<a href="edit_ad.php?id=<?php echo $ad->id; ?>"><button>Uredi</button></a>
		
	</div>
	<hr/>
	<?php }
?>

<?php
include_once('footer.php')
?>