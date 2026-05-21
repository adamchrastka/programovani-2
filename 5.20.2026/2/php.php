<?php
$pocetradku = 8;
$pocetsloupcu = 13;
$max = 104;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php echo "Počet řádků: $pocetradku"; ?>
    <?php echo "Počet sloupců: $pocetsloupcu"; ?>
    <table>
        <?php
        for ($i = 1; $i <= $pocetradku; $i++) {
            echo "<tr>";
            for ($j = 1; $j <= $pocetsloupcu; $j++) {
                $value = ($i - 1) * $pocetsloupcu + $j;
                if ($value > $max) {
                    break;
                }
                if  (($value % 2 == 0) && ($value <= 100)) {
                    echo "<td class='sudy'>$value</td>";
                }
                else if (($value % 2 == 1) && ($value <= 100)) {
                    echo "<td class='lichy'>$value</td>";
                }
                else if ($value > 100) {
                    echo "<td class='sto'>$value</td>";
                }

            }
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>
