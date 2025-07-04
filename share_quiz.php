<?php
$code = $_GET['code'] ?? '';
$username = $_GET['username'] ?? 'Ton ami';
$quiz_url = "http://" . $_SERVER['HTTP_HOST'] . "/qui-me-connait/quiz.php?code=" . urlencode($code);
$full_text = "$username a créé un quiz ! Est-ce que tu le connais vraiment ? 👉 $quiz_url";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Partage ton quiz</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .share-container {
            background: rgba(0, 0, 0, 0.5);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 30px rgba(0,0,0,0.3);
            animation: fadeIn 1s ease-in-out;
            max-width: 600px;
            width: 100%;
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 15px;
        }

        p {
            font-size: 1.1rem;
            margin-bottom: 25px;
        }

        .link-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .quiz-link {
            padding: 14px;
            border-radius: 10px;
            border: none;
            width: 100%;
            max-width: 420px;
            font-size: 1rem;
            text-align: center;
            color: #333;
            background: #fff;
        }

        .copy-button {
            background-color: #ff9800;
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .copy-button:hover {
            background-color: #e68a00;
        }

        .share-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 15px;
            margin-top: 20px;
        }

        .share-buttons a {
            padding: 14px 24px;
            border-radius: 30px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: 0.3s ease;
        }

        .whatsapp { background-color: #25D366; }
        .facebook { background-color: #4267B2; }
        .results { background-color: #9c27b0; }

        .whatsapp:hover { background-color: #1ebc5b; }
        .facebook:hover { background-color: #375ca1; }
        .results:hover { background-color: #7e1d9c; }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        @media (max-width: 600px) {
            h2 {
                font-size: 1.5rem;
            }

            p {
                font-size: 1rem;
            }

            .quiz-link {
                font-size: 1rem;
                width: 100%;
            }

            .copy-button,
            .share-buttons a {
                width: 100%;
                justify-content: center;
                font-size: 0.95rem;
            }

            .link-wrapper {
                flex-direction: column;
                gap: 12px;
            }

            .share-container {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="share-container">
        <h2>🎉 Ton quiz est prêt, <?= htmlspecialchars($username) ?> !</h2>
        <p>Partage ce lien à tes amis :</p>

        <div class="link-wrapper">
            <input type="text" id="quizLink" class="quiz-link" value="<?= $quiz_url ?>" readonly>
            <button class="copy-button" onclick="copyLink()">📋 Copier</button>
        </div>

        <div class="share-buttons">
            <a class="whatsapp" target="_blank"
               href="https://api.whatsapp.com/send?text=<?= urlencode($full_text) ?>">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>

            <a class="facebook" target="_blank"
               href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($quiz_url) ?>">
                <i class="fab fa-facebook"></i> Facebook
            </a>

            <a class="results" href="leaderboard.php?code=<?= urlencode($code) ?>">
                <i class="fas fa-chart-bar"></i> Voir les résultats
            </a>
        </div>
    </div>

    <script>
        function copyLink() {
            const input = document.getElementById("quizLink");
            input.select();
            input.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("✅ Lien copié !");
        }
    </script>
</body>
</html>
