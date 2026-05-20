<!-- De code die wordt gebruikt voor de gebruikers 
 De code hierin is voor inloggen, regristreren, videos uploaden en pofiel foto veranderen
-->
<?php
# Voegt de database uit database.php toe aan deze code
require_once __DIR__ . '/../../core/database.php';

// Meldingen worden in html getoond
$feedbackMessage = "";

// Wanneer de gebruiker heeft gerigistreerd wordt dit geschreven in de login pagina
if (isset($_GET['registration']) && $_GET['registration'] === 'success') {
    $feedbackMessage = "<p style='color: green;'>Registratie succesvol! Je kunt nu inloggen.</p>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Dwing PDO om fouten als uitzonderingen te werpen zodat je catch-blok werkt!
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // -- REGISTRATIE CODE --
        if (isset($_POST['action']) && $_POST['action'] === 'register') {
            
            // Checkt of alle velden leeg zijn voordat er iets gebeurd
            if (!empty($_POST['username']) && !empty($_POST['email']) && !empty($_POST['password'])) {
                
                $username = htmlspecialchars($_POST['username']);
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'];
                $confirmPassword = $_POST['confirm-password'];

                if ($password === $confirmPassword) {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                    // SQL-query voor registreren
                    $stmt = $conn->prepare("INSERT INTO users (username, `email`, `password`) VALUES (:username, :email, :password)");
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password', $hashedPassword);

                    $stmt->execute();
                    
                    // Wordt doorgestuurd na registreren 
                    header("Location: login.php?registration=success");
                    exit();
                } else {
                    $feedbackMessage = "<p style='color: red;'>De wachtwoorden komen niet overeen. Probeer het opnieuw.</p>";
                }
            }
        } 
        
        // -- LOGIN CODE --
        elseif (isset($_POST['action']) && $_POST['action'] === 'login') {
            if (!empty($_POST['username']) && !empty($_POST['password'])) {
                
                $loginInput = $_POST['username']; // Dit kan een gebruikersnaam of e-mail zijn
                $password = $_POST['password'];

                // Wordt gezocht in beide kolommen naar de gebruikersnaam of email adres
                $stmt = $conn->prepare("SELECT `password` FROM users WHERE `email` = :input OR username = :input");
                $stmt->bindParam(':input', $loginInput);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $hashedPassword = $row['password'];

                    if (password_verify($password, $hashedPassword)) {
                        // Als er succesvol is ingelogd wordt de gebruiker doorgestuurd naar index.php
                        header("Location: ../public/index.php");
                        exit();
                    } else {
                        $feedbackMessage = "<p style='color: red;'>Ongeldig wachtwoord. Probeer opnieuw.</p>";
                    }
                } else {
                    $feedbackMessage = "<p style='color: red;'>Gebruikersnaam of e-mailadres niet gevonden.</p>";
                }
            }
        }
    } catch (PDOException $e) {
        $feedbackMessage = "<p style='color: red;'>Er is een fout opgetreden: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

