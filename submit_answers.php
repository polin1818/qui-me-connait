<?php
// submit_answers.php
require_once 'db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quiz_code = $_POST['quiz_code'] ?? '';
    $friend_name = trim($_POST['friend_name'] ?? '');
    $question_ids = $_POST['question_ids'] ?? [];
    $answers = $_POST['answers'] ?? [];

    if (empty($quiz_code) || empty($friend_name) || count($question_ids) !== count($answers)) {
        die("Données invalides !");
    }

    $score = 0;

    try {
        for ($i = 0; $i < count($question_ids); $i++) {
            $question_id = (int)$question_ids[$i];
            $user_answer = trim($answers[$i]);

            // Récupérer la bonne réponse depuis la BDD
            $stmt = $pdo->prepare("SELECT correct_answer FROM questions WHERE id = ?");
            $stmt->execute([$question_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) continue;

            $correct_answer = trim($row['correct_answer']);

            $is_correct = (strtolower($user_answer) === strtolower($correct_answer)) ? 1 : 0;
            if ($is_correct) $score++;

            // Enregistrement dans la table answers
            $stmt_insert = $pdo->prepare("
                INSERT INTO answers (quiz_code, friend_name, question_id, selected_answer, is_correct)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt_insert->execute([
                $quiz_code,
                $friend_name,
                $question_id,
                $user_answer,
                $is_correct
            ]);
        }

        // Rediriger vers la page de résultats avec score
        header("Location: result.php?code=" . urlencode($quiz_code) . "&friend=" . urlencode($friend_name));
        exit;

    } catch (PDOException $e) {
        die("Erreur lors de l'enregistrement des réponses : " . $e->getMessage());
    }

} else {
    die("Méthode non autorisée.");
}
