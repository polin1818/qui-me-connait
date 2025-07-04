<?php
require_once 'db/config.php';

$quiz_code = $_GET['code'] ?? '';
$friend_name = $_GET['friend'] ?? '';

if (empty($quiz_code) || empty($friend_name)) {
    die("Informations manquantes !");
}

$stmt = $pdo->prepare("
    SELECT a.selected_answer, a.is_correct, q.question_text, q.correct_answer
    FROM answers a
    JOIN questions q ON a.question_id = q.id
    WHERE a.quiz_code = ? AND a.friend_name = ?
");
$stmt->execute([$quiz_code, $friend_name]);
$responses = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($responses)) {
    die("Aucune réponse trouvée.");
}

$total = count($responses);
$correct = array_reduce($responses, fn($carry, $r) => $carry + $r['is_correct'], 0);
$percentage = round(($correct / $total) * 100);

$stmt_creator = $pdo->prepare("SELECT username FROM users WHERE quiz_code = ?");
$stmt_creator->execute([$quiz_code]);
$creator = $stmt_creator->fetch(PDO::FETCH_ASSOC);
$creator_name = $creator ? $creator['username'] : 'ton ami';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultat de <?= htmlspecialchars($friend_name) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #667eea, #764ba2);/* ✅ même fond que demm.php */
            color: white;
            min-height: 100vh;
        }

        .result-container {
            max-width: 800px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.08);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
            animation: fadeInUp 1s ease;
        }

        h2 {
            text-align: center;
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        .summary {
            text-align: center;
            font-size: 1.2em;
            background: rgba(0, 0, 0, 0.3);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            animation: popIn 1s ease;
        }

        .answers-wrapper {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 10px;
        }

        .question-block {
            background: rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            animation: fadeIn 0.6s ease;
        }

        .correct {
            color: #00e676;
            font-weight: bold;
        }

        .incorrect {
            color: #ff5252;
            font-weight: bold;
        }

        .btns {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }

        .btn-link {
            background-color: #00c9a7;
            color: white;
            padding: 14px 28px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 1em;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: background-color 0.3s ease;
        }

        .btn-link:hover {
            background-color: #00b38f;
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes popIn {
            0% { transform: scale(0.8); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 768px) {
            .result-container {
                margin: 20px 10px;
                padding: 20px;
            }

            .btns {
                flex-direction: column;
                align-items: center;
            }

            .btn-link {
                width: auto;
                min-width: 220px;
                text-align: center;
                padding: 12px 20px;
                font-size: 0.95em;
            }
        }
    </style>
</head>
<body>
    <div class="result-container">
        <h2><i class="fas fa-star icon"></i> Résultat pour <strong><?= htmlspecialchars($friend_name) ?></strong></h2>

        <div class="summary">
            <p><i class="fas fa-trophy"></i> Tu as eu <strong><?= $correct ?>/<?= $total ?></strong> bonnes réponses</p>
            <p><i class="fas fa-user-friends"></i> Tu connais <strong><?= htmlspecialchars($creator_name) ?></strong> à <strong><?= $percentage ?>%</strong></p>
        </div>

        <div class="answers-wrapper">
            <?php foreach ($responses as $index => $r): ?>
                <div class="question-block">
                    <p><strong>Q<?= $index + 1 ?> :</strong> <?= htmlspecialchars($r['question_text']) ?></p>
                    <p>
                        Ta réponse :
                        <?php if ($r['is_correct']): ?>
                            <span class="correct"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($r['selected_answer']) ?></span>
                        <?php else: ?>
                            <span class="incorrect"><i class="fas fa-times-circle"></i> <?= htmlspecialchars($r['selected_answer']) ?></span><br>
                            <small>✔️ Bonne réponse : <strong><?= htmlspecialchars($r['correct_answer']) ?></strong></small>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="btns">
            <a class="btn-link" href="leaderboard.php?code=<?= urlencode($quiz_code) ?>"><i class="fas fa-chart-line"></i> Classement</a>
            <a class="btn-link" href="quiz.php?code=<?= urlencode($quiz_code) ?>"><i class="fas fa-redo"></i> Rejouer</a>
            <a class="btn-link" href="create_quiz.php"><i class="fas fa-pen"></i> Créer mon quiz</a>
        </div>
    </div>
</body>
</html>
