<?php
$servername = "localhost";
$username = "root";
$password = "";
#Naam van de database
$dbname = "streamhive";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
#echo "Connected successfully";

#Dit laat nu de usernames zien op de website  
try {
            $conn = new PDO("mysql:host=$servername;dbname=streamhive", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected successfully<br>";

            // Haalt alleen Naam werknemers op.
            $sql = "SELECT `username` FROM users";
            $stmt = $conn->query($sql);

            // Resultaten weergeven
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "users: " . $row["username"] . "<br>";
            }

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }

        
?>