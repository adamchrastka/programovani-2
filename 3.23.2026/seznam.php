<?php

$uploadDir = 'uploads/';
$images = [];

/* DELETE FILE */
if (isset($_GET['delete'])) {
    $file = basename($_GET['delete']);
    $filePath = $uploadDir . $file;

    if (is_file($filePath)) {
        unlink($filePath);
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

/* LOAD IMAGES */
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $uploadDir . $file;

        if (!is_file($filePath)) {
            continue;
        }

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (in_array($ext, ['jpg','jpeg','png','gif'])) {
            $images[] = $file;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="cs">

<head>

<meta charset="UTF-8">

<title>Seznam nahraných obrázků</title>

<style>

body{font-family:Arial}

.image-container{
display:inline-block;
margin:10px;
text-align:center;
padding:10px;
border:1px solid #ddd;
border-radius:6px;
background:#f9f9f9;
}

.image-container img{
max-width:200px;
max-height:200px;
border:1px solid #ccc;
border-radius:4px;
}

.btn{
display:inline-block;
margin-top:5px;
padding:5px 10px;
color:white;
text-decoration:none;
border-radius:4px;
font-size:13px;
border:none;
cursor:pointer;
}

.download{background:#007bff;}
.link{background:#28a745;}
.delete{background:#dc3545;}

</style>

<script>

function generateLink(imageName){

const url =
window.location.origin +
'/download.php?file=' +
encodeURIComponent(imageName);

navigator.clipboard.writeText(url)
.then(()=>alert('Odkaz zkopírován:\n'+url))
.catch(()=>{

let t=document.createElement('textarea');

t.value=url;

document.body.appendChild(t);

t.select();

document.execCommand('copy');

document.body.removeChild(t);

alert('Odkaz zkopírován:\n'+url);

});

}

</script>

</head>

<body>

<h1>Seznam nahraných obrázků</h1>

<p>
<a href="admin.php">Admin panel</a> |
<a href="login.php">Odhlásit</a>
</p>

<?php if(empty($images)): ?>

<p>Žádné obrázky.</p>

<?php else: ?>

<div>

<?php foreach($images as $image): ?>

<div class="image-container">

<img src="<?=htmlspecialchars($uploadDir.$image)?>">

<br>

<small><?=htmlspecialchars($image)?></small>

<br>

<a class="btn download"
href="download.php?file=<?=urlencode($image)?>">
Stáhnout
</a>

<br>

<button class="btn link"
onclick="generateLink('<?=htmlspecialchars($image)?>')">
Generovat odkaz
</button>

<br>

<a class="btn delete"
href="?delete=<?=urlencode($image)?>"
onclick="return confirm('Smazat obrázek?');">
Smazat
</a>

</div>

<?php endforeach; ?>

</div>

<?php endif; ?>

</body>
</html>