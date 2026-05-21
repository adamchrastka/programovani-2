<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $weight = $_POST["weight"];
    $height = $_POST["height"];
    if ($weight < 0) {
        echo "<h2>Neplatna vaha!</h2>";
        exit;
    }
    if ($height < 0) {
        echo "<h2>Neplatna vyska!</h2>";
        exit;
    }
    if ($height > 0) {
        $height = $height / 100; // Prevod z cm na m
        $bmi = $weight / ($height * $height);
        echo "<h2>Vase BMI je: " . round($bmi, 2) . "</h2>";
    } else {
        echo "<h2>Neplatna vyska!</h2>";
    }
    if ($bmi < 18.5) {
        echo "<p>Podvaha</p>";
    } elseif ($bmi >= 18.5 && $bmi < 25) {
        echo "<p>Normalni vaha</p>";
    } elseif ($bmi >= 25 && $bmi < 30) {
        echo "<p>Nadvaha</p>";
    } else {
        echo "<p>Obezita</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    .trida {
        text-align: center;
        margin-top: 50px;
    }
    p {
        font-size: 20px;
        font-weight: bold;
        text-align: center;
    }
    h2 {
        font-size: 24px;
        font-weight: bold;
        text-align: center;
    }
    body {
        background: linear-gradient(to right, #4f1717, #cf5e38);
        width: 20%;
        margin: 0 auto;
        margin-top: 5%;
        border-radius: 20%;
        padding: 1%;
    }
</style>
<body>
    <div class="trida">
        <h1>BMI kalkulacka</h1>
        <form method="POST">
            <label for="height">Vyska (m):</label>
            <input type="number" id="height" name="height" step="0.01" required>
            <br>
            <label for="weight">Vaha (kg):</label>
            <input type="number" id="weight" name="weight" required>
            <br>

            <input type="submit" value="Vypocitat BMI">
        </form>
    </div>
</body>
</html>