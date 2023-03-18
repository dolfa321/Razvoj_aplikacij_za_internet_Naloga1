<?php
include_once('header.php');

// Funkcija vstavi nov oglas v bazo. Preveri tudi, ali so podatki pravilno izpolnjeni. 
// Vrne false, če je prišlo do napake oz. true, če je oglas bil uspešno vstavljen.
function publish($title, $categories, $desc, $img)
{
    global $conn;
    $title = mysqli_real_escape_string($conn, $title);
    $desc = mysqli_real_escape_string($conn, $desc);
    $user_id = $_SESSION["USER_ID"];

    /*$categories = substr($categories, 0, -1);

    $categories_arr = explode("/", $categories);
    $categories_ids = [];
    foreach ($categories_arr as $cat) {
        $query = "SELECT id FROM categories WHERE ime='$cat';";
        $result = $conn->query($query);
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        array_push($categories_ids, $row[0]);
        // echo $result;
    }*/

    /*
    //Pravilneje bi bilo, da sliko shranimo na disk. Poskrbeti moramo, da so imena slik enolična. V bazo shranimo pot do slike.
    //Paziti moramo tudi na varnost: v mapi slik se ne smejo izvajati nobene scripte (če bi uporabnik naložil PHP kodo). Potrebno je urediti ustrezna dovoljenja (permissions).
    // ********** Narejeno *************
    */
    $imagePath = 'C:/laragon/www/N1/slike\ ';
    $uniquesavename = time() . uniqid(rand());
    $destFile = $imagePath . $uniquesavename . '.jpg';
    $filename = $img["tmp_name"];
    list($width, $height) = getimagesize($filename);
    move_uploaded_file($filename, $destFile);


    $pot = "slike/ " . $uniquesavename . '.jpg';
    //echo "Pot slika1: $pot ";
    //echo "$destFile";
    //V bazo shranimo $pot
    //echo  $pot;

    //Ostale slike

    $doc = new DOMDocument();
    $html = "<div id='galerija'></div>";
    $doc->loadHTML($html);

    $fragment = $doc->createDocumentFragment();

    $now = new DateTime();
    $expiration_date = new DateTime();
    $expiration_date->add(new DateInterval('P30D'));
    $now = $now->format('Y-m-d');    // MySQL datetime format
    $expiration_date = $expiration_date->format('Y-m-d');

	$query = "INSERT INTO ads (title, description, user_id, image, end_date, date,ogledi)
    VALUES('$title', '$desc', '$user_id', '$pot','$expiration_date','$now','0');";
	if (!$conn->query($query)) {
    return false;
	}

	$regex = '/\d+/';

	$numbers = array();
	if (preg_match_all($regex, $categories, $matches)) {
	  // Loop through all matches and add them to the numbers array
	  foreach ($matches[0] as $match) {
		$numbers[] = intval($match); // Convert the match to an integer before adding it to the array
	  }
	}

	print_r($numbers);
	$id_ad = mysqli_insert_id($conn);
	foreach($numbers as $number){
	$query = "INSERT INTO categories_ads (id_ad, id_category) VALUES('$id_ad', '$number');";	
	if (!$conn->query($query)) {
		return false;
		}
	}
	

    return true;

}

function get_grouped_categories()
{
	global $conn;
	$query = "SELECT * FROM categories;";
	$result = $conn->query($query);
	$result->fetch_all(MYSQLI_ASSOC);
	
	$html = '<ul class="Menu -vertical" id="kategorija"></ul>';
	$doc = new DOMDocument();
	$doc->loadHTML($html);
	
	foreach ($result as $side => $direc) {
		if ($direc["parent_id"] == null) {
			$fragment = $doc->createDocumentFragment();
			$kategorija = $doc->getElementById('kategorija');
			$fragment->appendXML('<li id="el_' . $direc["id"] . '">' . $direc["id"] . ' - ' . $direc["ime"] . '</li>');
			$kategorija->appendChild($fragment);
		} else {
			$submenu_fragment = $doc->createDocumentFragment();
			$submenu = $doc->getElementById("el_" . $direc["parent_id"]);
			$submenu->setAttribute('class', '-hasSubmenu');
			if ($doc->getElementById("me_" . $direc['parent_id'] . '_' . $direc["id"]) == null) {
				$submenu_fragment->appendXML("<ul id='me_" . $direc["parent_id"] . '_' . $direc["id"] . "'> <li id='el_" . $direc["id"] . "'>" . $direc["id"] . ' - ' . $direc["ime"] . "</li> </ul>");
				$submenu->appendChild($submenu_fragment);
			} else {
				$kategorija = $doc->getElementById("me_" . $direc['parent_id'] . '_' . $direc["id"]);
				$submenu_fragment->appendXML('<li id="el_' . $direc["id"] . '">' . $direc["id"] . ' - ' . $direc["ime"] . '</li>');
				$kategorija->appendChild($submenu_fragment);
			}
		}
		$html = $doc->saveHTML();
		$doc->loadHTML($html);
	}
	echo $doc->saveHTML();
	
}

$error = "";
if (isset($_POST["poslji"])) {
    if (publish($_POST["title"], $_POST["categories"], $_POST["description"], $_FILES["image"],)) {
        header("Location: index.php");
        die();
    } else {
        $error = "Prišlo aje do napake pri objavi oglasa.";
    }
}

?>
    <h2>Objavi oglas</h2>
    <form action="publish.php" method="POST" enctype="multipart/form-data">
        <h3>Izberi kategorije</h3>
        <?php get_grouped_categories(); ?><br>
        <label>Kategorije</label><input type="text" name="categories" required/> <br/>
        <label>Naslov</label><input type="text" name="title" required/> <br/>
        <label>Vsebina</label><textarea name="description" rows="10" cols="50" required></textarea> <br/>
        <label>Naslovna Slika</label><input type="file" name="image" accept="image/jpeg" required/> <br/>
        <input type="submit" name="poslji" value="Objavi"/> <br/>
        <label><?php echo $error; ?></label>
    </form>
<?php
include_once('footer.php');
?>