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

if (isset($_POST['id'], $_POST['title'], $_POST['description'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    if(isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK){
        $tmp_name = $_FILES["image"]["tmp_name"];
        $savename = uniqid();
        $image_path = "slike/" . $savename . ".jpg";
        move_uploaded_file($tmp_name, $image_path);
        $query = "UPDATE ads SET title='$title', description='$description', image='$image_path' WHERE id='$id';";
    } else {
        $query = "UPDATE ads SET title='$title', description='$description' WHERE id='$id';";
    }

    $conn->query($query);
    header("Refresh:0");
}


foreach($ads as $ad){
	?>
<div class="card mx-auto" style="width: 60rem;">
    <img src="<?php echo $ad->image;?>" class="card-img-top" alt="Ad Image">
    <div class="card-body">
        <h5 class="card-title"><?php echo $ad->title;?></h5>
        <p class="card-text"><?php echo $ad->description;?></p>
        <p class="card-text">Ogledi: <?php echo $ad->ogledi?></p>
        <form method="post">
            <input type="hidden" name="ad_id" value="<?php echo $ad->id; ?>">
            <button type="submit" name="delete" class="btn btn-danger">Briši</button>
        </form>
        <form method="post">
            <input type="hidden" name="id" value="<?php echo $ad->id; ?>">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" value="<?php echo $ad->title; ?>" class="form-control">
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" class="form-control"><?php echo $ad->description; ?></textarea>
            </div>
            <div class="form-group">
                <label for="image">Image:</label>
                <input type="file" name="image" id="image"><br>

            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

	<hr/>
	<?php }
?>

<?php
include_once('footer.php')
?>