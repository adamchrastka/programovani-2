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

    header("Location: ".$_SERVER['PHP_SELF']);
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

        if (is_file($filePath)) {

            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

            if (in_array($ext,['jpg','jpeg','png','gif'])) {
                $images[] = $file;
            }
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

body{
font-family: Arial;
}

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

}

.download{
    background:#007bff;
}

.link{
    background:#28a745;
    border:none;
    cursor:pointer;
}

.delete{
    background:#dc3545;
}

</style>

<script>

function generateLink(imageName){

const downloadUrl =
window.location.origin +
'/download.php?file=' +
encodeURIComponent(imageName);
navigator.clipboard.writeText(downloadUrl)
.then(()=>{
alert('Odkaz zkopírován:\\n'+downloadUrl);

})

.catch(()=>{

const textArea =
document.createElement('textarea');

textArea.value=downloadUrl;

document.body.appendChild(textArea);

textArea.select();

document.execCommand('copy');

document.body.removeChild(textArea);

alert('Odkaz zkopírován:\\n'+downloadUrl);

});

}

</script>

</head>

<body>

<h1>Seznam nahraných obrázků</h1>

<p>

<a href="admin.php">Zpět do Admin Panelu</a>

|

<a href="login.php">Odhlásit</a>

</p>

<?php if(empty($images)): ?>

<p>Žádné obrázky nebyly nahrány.</p>

<?php else: ?>

<div>

<?php foreach($images as $image): ?>

<div class="image-container">

<img
src="<?php echo htmlspecialchars($uploadDir.$image); ?>"
alt="<?php echo htmlspecialchars($image); ?>"
>

<br>

<small>
<?php echo htmlspecialchars($image); ?>
</small>

<br>

<a
class="btn download"
href="download.php?file=<?php echo urlencode($image); ?>"
>
Stáhnout
</a>

<br>

<button
class="btn link"
onclick="generateLink('<?php echo htmlspecialchars($image); ?>')"
>
Generovat odkaz
</button>

<br>

<a
class="btn delete"
href="?delete=<?php echo urlencode($image); ?>"
onclick="return confirm('Opravdu chceš tento obrázek smazat?');"
>
Smazat
</a>

</div>

<?php endforeach; ?>

</div>

<?php endif; ?>

</body>

</html>