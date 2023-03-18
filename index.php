<?php
include_once('header.php');

// Funkcija prebere oglase iz baze in vrne polje objektov
function get_adS(){
	global $conn;
	$query = "SELECT * FROM ads ORDER BY date DESC;";
	$res = $conn->query($query);
	$ads = array();
	while($ad = $res->fetch_object()){
		array_push($ads, $ad);
	}
	return $ads;
}
function get_categories(){
	global $conn;
	$query = "SELECT categories_ads.ime AS category_name
	FROM categories_ads categories
	INNER JOIN categories categories_ads ON categories.id_category = categories_ads.id;";
	$res = $conn->query($query);
	$categories = array();
	while($categorie = $res->fetch_object()){
		array_push($categories, $categorie);
	}
	return $categories;
}
//Preberi oglase iz baze
$ads = get_ads();
$categories = get_categories();

//Izpiši oglase
//Doda link z GET parametrom id na oglasi.php za gumb 'Preberi več'
foreach($ads as $ad){
	?>
	<div class="ad">
		<h4><?php echo $ad->title;?></h4>
		
		<img src="<?php echo $ad->image;?>"/>
		<p><?php echo $ad->description;?></p>
		<a href="ad.php?id=<?php echo $ad->id;?>"><button>Preberi več</button></a>
	</div>
	<hr/>
	<?php
}

//>> git commit -m "first commit"
//>> git branch -M main
// git push -u origin main
include_once('footer.php');
?>
