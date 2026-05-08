<!-- De code die wordt gebruikt voor de gebruikers 
 De code hierin is voor inloggen, regristreren, videos uploaden en pofiel foto veranderen
-->
<?php
$servername = "localhost";
$username = "root";
$password = "";

    #Deze code wordt veranderd naar de regristratie code
try {
    $conn = new PDO("mysql:host=$servername;dbname=streamhive", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Verbinding geslaagd!<br>";

    // Variabele voor de nieuwe gebruikersnaam
    $newUsername = ""; 

    $sql = "INSERT INTO users (username) VALUES (:username)";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(':username', $newUsername);
    $stmt->execute();

    echo "Gebruiker toegevoegd: " . $newUsername . "<br>";

} catch (PDOException $e) {
    echo "Foutmelding: " . $e->getMessage();
}
?>
