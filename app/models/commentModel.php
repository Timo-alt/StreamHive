<!-- De code die wordt gebruikt voor de comments
 hierin worden de comments toegevoegd aan de database 
 gelinkt met index.php waar de comments onder de video worden geladen
-->
<?php

class CommentManager {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Voegt een nieuwe comment toe aan de database.
     * Retourneert true bij succes, of false als de comment leeg is.
     */
    public function addComment(int $userId, int $videoId, string $commentText): bool {
        // Schoon de input op
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