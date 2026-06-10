<!-- De code die wordt gebruikt voor de videos
Deze code kan voor de videos titels toevoegen, descriptie toevoegen, 
de view count laten zien en de video uploaden en laden. 
-->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Voegt de database uit database.php toe aan deze code
require_once __DIR__ . '/../../core/database.php';
 
if(isset($_POST['but_upload'])){
   $maxsize = 5242880; // 5MB
   
   // Controleren of XAMPP/PHP het bestand heeft geblokkeerd vanwege de grootte
   if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
       if ($_FILES['file']['error'] === UPLOAD_ERR_INI_SIZE) {
           $_SESSION['message'] = "Fout: Het bestand is te groot voor de XAMPP-instellingen (meestal max 2MB). Pas je php.ini aan.";
       } else {
           $_SESSION['message'] = "PHP Upload Foutcode: " . $_FILES['file']['error'];
       }
   }   
   elseif(isset($_FILES['file']['name']) && $_FILES['file']['name'] != ''){
       $name = $_FILES['file']['name'];
       $target_dir = "../uploads/";
       
       // Zorg dat de map echt bestaat voordat we uploaden
       if (!is_dir($target_dir)) {
           $_SESSION['message'] = "Fout: De map '" . $target_dir . "' bestaat niet vanaf deze locatie!";
       } else {
           $target_file = $target_dir . $_FILES["file"]["name"];

           // Select file type
           $extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

           // Valid file extensions
           $extensions_arr = array("mp4", "avi", "3gp", "mov", "mpeg");

           // Check extension
           if( in_array($extension, $extensions_arr) ){
     
                // Check file size
                if(($_FILES['file']['size'] >= $maxsize) || ($_FILES["file"]["size"] == 0)) {
                     $_SESSION['message'] = "File too large. File must be less than 5MB.";
                }else{
                     // Upload
                     if(move_uploaded_file($_FILES['file']['tmp_name'], $target_file)){
                        
                        try {
                            // !!! HIER IS DE FIX: user_id toegevoegd aan de SQL query !!!
                            $query = "INSERT INTO videos (title, filename, user_id) VALUES (:title, :filename, :user_id)";
                            $stmt = $conn->prepare($query);
                            
                            // Haal de ingevulde titel op uit het formulier, of pak de bestandsnaam als hij leeg is
                            $videoTitle = !empty($_POST['video_title']) ? htmlspecialchars($_POST['video_title']) : $name;

                            // !!! HIER GEVEN WE DE WAARDES MEE, INCLUSIEF JE USER_ID UIT DE SESSIE !!!
                            $stmt->execute([
                                ':title'    => $videoTitle,
                                ':filename' => $target_file,
                                ':user_id'  => $_SESSION['user_id'] 
                            ]);

                            $_SESSION['message'] = "Upload successfully.";
                        } catch (PDOException $e) {
                            $_SESSION['message'] = "Database error: " . $e->getMessage(); 
                        }
                     } else {
                         // Foutopvang als het verplaatsen mislukt
                         $_SESSION['message'] = "Fout: move_uploaded_file is mislukt. Controleer de schrijfrechten van de map '" . $target_dir . "'.";
                     }
                }

           }else{
               $_SESSION['message'] = "Invalid file extension.";
           }
       }
   }else{
       $_SESSION['message'] = "Please select a file.";
   }
   
   header('location: index.php');
   exit;
}


?>