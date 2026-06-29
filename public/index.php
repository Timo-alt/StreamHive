<?php
// Start sessie
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inladen van je Database klasse (die houden we centraal voor alle bestanden)
require_once __DIR__ . '/../core/Database.php';

// ==========================================
// 1. CLASSES (DE LOGICA)
// ==========================================

class StreamHiveHome {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Haalt alle video's op uit de database
     */
    public function getAllVideos(): array {
        $stmt = $this->db->query("SELECT * FROM videos ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Haalt alleen de comments op die bij deze specifieke video horen
     */
    public function getCommentsByVideoId(int $videoId): array {
        $stmt = $this->db->prepare("SELECT * FROM comments WHERE video_id = :video_id ORDER BY id DESC");
        $stmt->execute([':video_id' => $videoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Voegt de comments toe aan de database
     */
    public function addComment(int $userId, int $videoId, string $commentText): bool {
        $commentText = htmlspecialchars($commentText);

        if (!empty(trim($commentText))) {
            $query = "INSERT INTO comments (user_id, video_id, content) VALUES (:user_id, :video_id, :content)";
            $stmt = $this->db->prepare($query);

            return $stmt->execute([
                ':user_id'  => $userId,
                ':video_id' => $videoId,
                ':content'  => $commentText
            ]);
        }
        return false;
    }
}

// ==========================================
// 2. CONTROLLER (DE VERWERKING)
// ==========================================

try {
    // Verbinding maken via de database klasse
    $database = new Database();
    $conn = $database->connect();

    // Initialiseer onze gecombineerde Home klasse
    $homeManager = new StreamHiveHome($conn);

    // Als er een comment wordt gepost
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
        if (isset($_SESSION['user_id'])) {
            $videoId = (int)$_POST['video_id'];
            $commentText = $_POST['content_comment'];
            
            $homeManager->addComment($_SESSION['user_id'], $videoId, $commentText);
            
            // Redirect om dubbele POST te voorkomen
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            echo "<script>alert('Je moet ingelogd zijn om een reactie te plaatsen.');</script>";
        }
    }

    // Haal alle video's op voor de weergave
    $videos = $homeManager->getAllVideos();

} catch (PDOException $e) {
    die("<p style='color:red;'>Fout: " . $e->getMessage() . "</p>");
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="assets/home.css">
</head>
<body>

<div class="sidebar">
    <a href="upload_videos.php" class="btn-nav-left">Video Uploaden</a>
</div>

<div class='video-grid'>
    <?php if (!empty($videos)): ?>
        <?php foreach($videos as $row): 
            $location = htmlspecialchars($row['filename']); 
            $name = htmlspecialchars($row['title']); 
            $videoId = $row['id']; 
        ?>
            <div class='video-item'>
                <video src='<?php echo $location; ?>' controls width='320px' height='320px'></video>    
                <br>
                <span><?php echo $name; ?></span>
                <br>

                <form method='POST' action=''>
                    <input type='hidden' name='video_id' value='<?php echo $videoId; ?>'>
                    <textarea name='content_comment' rows='1' cols='20' placeholder='Typ een comment...' required></textarea>
                    <button type='submit' name='submit_comment'>Comment</button>
                </form>

                <div class='comments-section'>
                    <h4>Reacties:</h4>

                    <?php 
                    // Haal de comments op via de klasse
                    $comments = $homeManager->getCommentsByVideoId($videoId);
                    $hasComments = false;

                    if (!empty($comments)):
                        foreach($comments as $commentRow): 
                            $hasComments = true;
                        ?>
                            <div class='comment-box' style='background: #707070; padding: 5px; margin-top: 5px;'>
                                <p><?php echo htmlspecialchars($commentRow['content']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!$hasComments): ?>
                        <p><em>Nog geen reacties.</em></p>
                    <?php endif; ?>

                </div> 
            </div>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Er zijn nog geen video's geüpload.</p>
    <?php endif; ?>
</div>

</body>
</html>