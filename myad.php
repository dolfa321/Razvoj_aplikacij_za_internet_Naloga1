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

if (isset($_POST['id'], $_POST['title'], $_POST['description'], $_POST['image'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $image = mysqli_real_escape_string($conn, $_POST['image']);
    $query = "UPDATE ads SET title='$title', description='$description', image='$image' WHERE id='$id';";
    $conn->query($query);
    header("Refresh:0");

}

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



<form method="post">
    <input type="hidden" name="id" value="<?php echo $ad->id; ?>">
    <label for="title">Title:</label>
    <input type="text" name="title" id="title" value="<?php echo $ad->title; ?>"><br>
    <label for="description">Description:</label>
    <textarea name="description" id="description"><?php echo $ad->description; ?></textarea><br>
    <label for="image">Image:</label>
    <input type="text" name="image" id="image" value="<?php echo $ad->image; ?>"><br>
    <input type="submit" value="Update">
</form>
<button>Uredi</button></a>
		
	</div>
	<hr/>
	<?php }
?>

<?php
include_once('footer.php')
?>