<!-- De code die wordt gebruikt voor de videos
Deze code kan voor de videos titels toevoegen, descriptie toevoegen, 
de view count laten zien en de video uploaden en laden. 
-->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Voegt de database uit database.php toe aan deze code
require_once __DIR__ . '/../../core/Database.php';

// ==========================================
// 1. CLASS (DE LOGICA)
// ==========================================

class VideoModel {
    private PDO $db;
    private int $maxSize;
    private array $allowedExtensions;
    private string $targetDir;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->maxSize = 5242880; // 5MB
        $this->allowedExtensions = array("mp4", "avi", "3gp", "mov", "mpeg");
        $this->targetDir = "../uploads/";
    }

    /**
     * Verwerkt de complete bestandsupload en database opslag
     */
    public function uploadVideo(array $file, ?string $customTitle, int $userId): string {
        // Controleren of XAMPP/PHP het bestand heeft geblokkeerd vanwege de grootte
        if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
            if ($file['error'] === UPLOAD_ERR_INI_SIZE) {
                return "Fout: Het bestand is te groot voor de XAMPP-instellingen (meestal max 2MB). Pas je php.ini aan.";
            } else {
                return "PHP Upload Foutcode: " . $file['error'];
            }
        }

        if (!isset($file['name']) || $file['name'] == '') {
            return "Please select a file.";
        }

        // Zorg dat de map echt bestaat voordat we uploaden
        if (!is_dir($this->targetDir)) {
            return "Fout: De map '" . $this->targetDir . "' bestaat niet vanaf deze locatie!";
        }

        $name = $file['name'];
        $targetFile = $this->targetDir . $name;
        $extension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check extension
        if (!in_array($extension, $this->allowedExtensions)) {
            return "Invalid file extension.";
        }

        // Check file size
        if (($file['size'] >= $this->maxSize) || ($file['size'] == 0)) {
            return "File too large. File must be less than 5MB.";
        }

        // Upload naar de map
        if (!move_uploaded_file($file['tmp_name'], $targetFile)) {
            return "Fout: move_uploaded_file is mislukt. Controleer de map '" . $this->targetDir . "'.";
        }

        // Opslaan in database
        try {
            $query = "INSERT INTO videos (title, filename, user_id) VALUES (:title, :filename, :user_id)";
            $stmt = $this->db->prepare($query);
            
            // Haal de ingevulde titel op, of pak de bestandsnaam als hij leeg is
            $videoTitle = !empty($customTitle) ? htmlspecialchars($customTitle) : $name;

            $stmt->execute([
                ':title'    => $videoTitle,
                ':filename' => $targetFile,
                ':user_id'  => $userId
            ]);

            return "Upload successfully.";
        } catch (PDOException $e) {
            return "Database error: " . $e->getMessage();
        }
    }
}

// ==========================================
// 2. CONTROLLER (DE POST VERWERKING)
// ==========================================

if (isset($_POST['but_upload'])) {
    
    // Zorg eerst dat we zelf de verbinding aanmaken, in plaats van te gokken of hij bestaat!
    try {
        $database = new Database();
        $conn = $database->connect();
        
        // Dwing exception mode af
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $videoModel = new VideoModel($conn);
        
        // Voer de upload uit en geef de user_id uit de sessie mee
        $userId = $_SESSION['user_id'] ?? 0;
        $titleInput = $_POST['video_title'] ?? null;
        
        $_SESSION['message'] = $videoModel->uploadVideo($_FILES['file'], $titleInput, (int)$userId);

    } catch (Exception $e) {
        // Mocht de database falen, vangen we dat netjes op
        $_SESSION['message'] = "Kritieke fout bij verbinden: " . $e->getMessage();
    }
    
    // Altijd terugsturen naar index na de verwerking
    header('location: index.php');
    exit;
}
?>