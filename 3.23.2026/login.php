<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if ($username == "admin" && $password == "admin") {
        $success = true;
        $redirect_url = "admin.php";
    } elseif ($username == "user" && $password == "user") {
        $success = true;
        $redirect_url = "private.html";
    } elseif ($username == "guest" && $password == "guest") {
        $success = true;
        $redirect_url = "info.php";
    } else {
        $error = "Neplatne prihlasovaci udaje.";
    }
}
?>
<body>
    <h1>Prihlaseni</h1>
    <?php if (isset($success)) { ?>
        <p>Prihlaseni uspesne! Presmerovani za 3 sekundy...</p>
        <meta http-equiv="refresh" content="3;url=<?php echo $redirect_url; ?>">
    <?php } elseif (isset($error)) { ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php } ?>
    <form action="login.php" method="post">
        <label for="username">Uzivatelske jmeno:</label>
        <input type="text" id="username" name="username"><br><br>
        <label for="password">Heslo:</label>
        <input type="password" id="password" name="password"><br><br>
        <input type="submit" value="Prihlasit se">
    </form>
</body>