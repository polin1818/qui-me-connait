<?php
require_once 'db/config.php';

if (!isset($_GET['code']) || empty($_GET['code'])) {
    die("Aucun quiz trouvé !");
}

$quiz_code = $_GET['code'];

$stmt = $pdo->prepare("
    SELECT u.id AS user_id, u.username, q.id AS question_id, q.question_text 
    FROM users u 
    JOIN questions q ON u.id = q.user_id 
    WHERE u.quiz_code = ?
");
$stmt->execute([$quiz_code]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($questions)) {
    die("Quiz introuvable !");
}

$creator_name = $questions[0]['username'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz de <?= htmlspecialchars($creator_name) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0 20px;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(to right, #667eea, #764ba2);
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }

        .quiz-container {
            background-color: rgba(0, 0, 0, 0.4);
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
            animation: fadeIn 1s ease;
        }

        h2 {
            font-size: 2em;
            margin-bottom: 25px;
            animation: fadeInTop 1.2s ease;
        }

        .question-block {
            background-color: rgba(255, 255, 255, 0.08);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            display: none;
            animation: fadeInUp 0.6s ease;
        }

        .question-block.active {
            display: block;
        }

        .question-text {
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            font-size: 1em;
            margin-top: 8px;
        }

        .btn-next {
            background-color: #00c9a7;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 30px;
            font-size: 1em;
            margin-top: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-next:hover {
            background-color: #00b38f;
        }

        label {
            font-weight: bold;
            margin-top: 15px;
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInTop {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 600px) {
            .quiz-container {
                padding: 20px;
            }

            h2 {
                font-size: 1.5rem;
            }

            input[type="text"] {
                font-size: 0.95rem;
            }

            .btn-next {
                width: 100%;
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="quiz-container">
        <h2><i class="fas fa-question-circle"></i> Quiz : Qui connaît le mieux <?= htmlspecialchars($creator_name) ?> ?</h2>
        <form action="submit_answers.php" method="POST" id="quizForm">
            <input type="hidden" name="quiz_code" value="<?= htmlspecialchars($quiz_code) ?>">

            <div id="step-name">
                <label><i class="fas fa-user"></i> Ton prénom ou pseudo :</label>
                <input type="text" name="friend_name" id="friendNameInput" required placeholder="Ex: Alex">
                <button type="button" class="btn-next" onclick="validateName()">Commencer le quiz</button>
            </div>

            <?php foreach ($questions as $index => $q): ?>
                <div class="question-block" data-step="<?= $index ?>">
                    <p class="question-text">Question <?= $index + 1 ?> : <?= htmlspecialchars($q['question_text']) ?></p>
                    <input type="hidden" name="question_ids[]" value="<?= $q['question_id'] ?>">
                    <input type="text" name="answers[]" required placeholder="Ta réponse ici...">
                    <button type="button" class="btn-next" onclick="showNext(<?= $index ?>)">Suivant</button>
                </div>
            <?php endforeach; ?>

            <div class="question-block" id="final-step" style="display:none;">
                <p>✅ Bravo ! Tu as terminé le quiz.</p>
                <button type="submit" class="btn-next">Soumettre mes réponses</button>
            </div>
        </form>
    </div>

    <script>
        function validateName() {
            const nameInput = document.getElementById('friendNameInput');
            const name = nameInput.value.trim();

            if (name !== "") {
                fetch('delete_previous_answers.php?code=<?= $quiz_code ?>&friend=' + encodeURIComponent(name))
                    .then(() => {
                        document.getElementById('step-name').style.display = 'none';
                        document.querySelector('.question-block[data-step="0"]').classList.add('active');
                    });
            } else {
                alert("Merci d’entrer ton prénom ou pseudo.");
            }
        }

        function showNext(currentIndex) {
            const current = document.querySelector('.question-block[data-step="' + currentIndex + '"]');
            const next = document.querySelector('.question-block[data-step="' + (currentIndex + 1) + '"]');

            if (next) {
                current.classList.remove('active');
                next.classList.add('active');
                next.scrollIntoView({ behavior: 'smooth' });
            } else {
                current.classList.remove('active');
                document.getElementById('final-step').style.display = 'block';
                document.getElementById('final-step').scrollIntoView({ behavior: 'smooth' });
            }
        }
    </script>
</body>
</html>
