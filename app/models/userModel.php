<!-- De code die wordt gebruikt voor de gebruikers 
 De code hierin is voor inloggen, regristreren en pofiel foto veranderen
-->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Voegt de database uit database.php toe aan deze code (gefixt pad naar core)
require_once __DIR__ . '/../../core/Database.php';

// ==========================================
// 1. CLASS: USERMANAGER
// ==========================================

class UserManager {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
        // Dwing PDO om fouten als uitzonderingen te werpen zodat je catch-blok werkt!
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Regelt de registratie van een nieuwe gebruiker.
     */
    public function register(string $username, string $email, string $password, string $confirmPassword): bool|string {
        // Checkt of alle velden leeg zijn voordat er iets gebeurd
        if (empty($username) || empty($email) || empty($password)) {
            return "Vul alstublieft alle velden in.";
        }

        $username = htmlspecialchars($username);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        if ($password === $confirmPassword) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // SQL-query voor registreren
            $stmt = $this->db->prepare("INSERT INTO users (username, `email`, `password`) VALUES (:username, :email, :password)");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $hashedPassword);

            $stmt->execute();
            return true;
        } else {
            return "<p style='color: red;'>De wachtwoorden komen niet overeen. Probeer het opnieuw.</p>";
        }
    }

    /**
     * Regelt het inloggen van een gebruiker.
     */
    public function login(string $loginInput, string $password): bool|string {
        if (empty($loginInput) || empty($password)) {
            return "Vul alstublieft je gebruikersnaam/e-mail en wachtwoord in.";
        }

        // Haalt id en username uit de database
        $stmt = $this->db->prepare("SELECT `id`, `username`, `password` FROM users WHERE `email` = :input OR username = :input");
        $stmt->bindParam(':input', $loginInput);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashedPassword = $row['password'];

            if (password_verify($password, $hashedPassword)) {
                // Slaat gegevens van de ingelogde gebruiker op in een sessie
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                return true;
            } else {
                return "<p style='color: red;'>Ongeldig wachtwoord. Probeer opnieuw.</p>";
            }
        } else {
            return "<p style='color: red;'>Gebruikersnaam of e-mailadres niet gevonden.</p>";
        }
    }
}

// ==========================================
// 2. CONTROLLER: VERWERKING & REDIRECTS
// ==========================================

// Global feedback variabele die je in je HTML kunt tonen via $_SESSION
$feedbackMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Maak de databaseverbinding aan
        $database = new Database();
        $conn = $database->connect();
        
        // Initialiseer de manager
        $userManager = new UserManager($conn);

        // -- REGISTRATIE CODE --
        if (isset($_POST['action']) && $_POST['action'] === 'register') {
            $result = $userManager->register(
                $_POST['username'] ?? '',
                $_POST['email'] ?? '',
                $_POST['password'] ?? '',
                $_POST['confirm-password'] ?? ''
            );

            if ($result === true) {
                // Wordt doorgestuurd na registreren
                header("Location: login.php?registration=success");
                exit();
            } else {
                $_SESSION['auth_error'] = $result;
                header("Location: " . $_SERVER['HTTP_REFERER']); // Stuur terug naar register pagina met de fout
                exit();
            }
        } 
        
        // -- LOGIN CODE --
        elseif (isset($_POST['action']) && $_POST['action'] === 'login') {
            $result = $userManager->login(
                $_POST['username'] ?? '',
                $_POST['password'] ?? ''
            );

            if ($result === true) {
                header("Location: ../public/index.php");
                exit();
            } else {
                $_SESSION['auth_error'] = $result;
                header("Location: login.php"); // Stuur terug naar login pagina met de fout
                exit();
            }
        }

    } catch (PDOException $e) {
        $_SESSION['auth_error'] = "<p style='color: red;'>Er is een fout opgetreden: " . htmlspecialchars($e->getMessage()) . "</p>";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}