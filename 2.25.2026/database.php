<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
</br>
</br>
    <form method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>
        
        <input type="submit" value="Submit">
    </form>
    <input type="button" value="Delete All" onclick="deleteAll()">
    </body>
</html>
<?php
$dsn = "mysql:host=localhost;dbname=chrastka;charset=utf8";
$username="testuser";
$password="testpass";
try{
    $db=new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    echo "uspesne pripojeni";
}


catch (PDOException $e){
    echo"nelze se pripojit k db:".$e->getmessage();
    
}
function DELETEALL($table) {
    global $db;
    $sql = "DELETE FROM $table"; // Delete all rows from the table
    $stmt = $db->prepare($sql);
    $stmt->execute();
    echo "All rows deleted successfully from '$table'.<br>";
}

function GET ($table,$id){
    $sql="SELECT * FROM $table where id= :id";
    $stmt = $db->prepare($sql); //ochrana pro sql injection
    $stmt->execute(['id' => $id]); //provedeni přikazu
    return $stmt->fetch(PDO::FETCH_ASSOC);
    
}
function GETALL($table){
    global $db;
    $sql="SELECT * FROM $table";
    $stmt = $db->prepare($sql); //ochrana pro sql injection
    $stmt->execute(); //provedeni přikazu
    return $stmt->fetchALL(PDO::FETCH_ASSOC);
}

$tableName = "users";
$data = GETALL($tableName);
function INSERT($table, $data) {
    global $db;
    // Dynamically build the SQL query
    $columns = implode(", ", array_keys($data));
    $placeholders = ":" . implode(", :", array_keys($data));
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $db->prepare($sql);
    $stmt->execute($data);
    echo "New row inserted successfully.<br>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $name = $_POST["name"];
    
    // Prepare data for insertion
    $dataToInsert = [
        'username' => $username,
        'email' => $email,
        'name' => $name
    ];
    
    // Insert data into the database
    INSERT($tableName, $dataToInsert);
}

if (empty($data)) {
    echo "No data found in the table '$tableName'.";
} else {
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>";
    // Display table headers dynamically
    foreach (array_keys($data[0]) as $header) {
        echo "<th>" . htmlspecialchars($header) . "</th>";
    }
    echo "</tr>";
    // Display table rows
    foreach ($data as $row) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}
?>