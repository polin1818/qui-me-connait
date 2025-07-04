<?php
// save_quiz.php
require_once 'db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $questions = $_POST['questions'];
    $answers = $_POST['answers'];

    // Vérification de base
    if (empty($username) || count($questions) !== count($answers)) {
        die("Formulaire invalide !");
    }

    // Génération d’un code unique
    $quiz_code = substr(md5(uniqid()), 0, 8); // 8 caractères

    try {
        // Démarrer une transaction
        $pdo->beginTransaction();

        // Insertion dans la table users
        $stmt = $pdo->prepare("INSERT INTO users (username, quiz_code) VALUES (?, ?)");
        $stmt->execute([$username, $quiz_code]);
        $user_id = $pdo->lastInsertId();

        // Insertion des questions
        $stmt_q = $pdo->prepare("INSERT INTO questions (user_id, question_text, correct_answer) VALUES (?, ?, ?)");

        for ($i = 0; $i < count($questions); $i++) {
            $q = trim($questions[$i]);
            $a = trim($answers[$i]);

            if (!empty($q) && !empty($a)) {
                $stmt_q->execute([$user_id, $q, $a]);
            }
        }

        $pdo->commit();

        // Redirection vers la page du quiz
       header("Location: share_quiz.php?code=" . urlencode($quiz_code) . "&username=" . urlencode($username));

        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Erreur lors de l’enregistrement : " . $e->getMessage());
    }

} else {
    die("Accès non autorisé !");
}
