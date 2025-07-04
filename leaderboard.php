<?php
require_once 'db/config.php';

$quiz_code = $_GET['code'] ?? '';

if (empty($quiz_code)) {
    die("Code quiz manquant !");
}

$stmt_creator = $pdo->prepare("SELECT username FROM users WHERE quiz_code = ?");
$stmt_creator->execute([$quiz_code]);
$creator = $stmt_creator->fetch(PDO::FETCH_ASSOC);

if (!$creator) {
    die("Quiz introuvable.");
}
$creator_name = $creator['username'];

$stmt = $pdo->prepare("
    SELECT friend_name, COUNT(*) AS total_questions, SUM(is_correct) AS score
    FROM answers
    WHERE quiz_code = ?
    GROUP BY friend_name
    ORDER BY score DESC, friend_name ASC
");
$stmt->execute([$quiz_code]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Classement - Quiz de <?= htmlspecialchars($creator_name) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(to right, #667eea, #764ba2);
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
        }

        .leaderboard-container {
            max-width: 800px;
            margin: 40px auto;
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 0 25px rgba(0,0,0,0.2);
            text-align: center;
        }

        h2 {
            font-size: 1.8rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            color: #333;
            border-radius: 10px;
            overflow: hidden;
            font-size: 1rem;
        }

        th, td {
            padding: 12px 8px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #00c9a7;
            color: white;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .btn-home {
            display: inline-block;
            margin-top: 25px;
            background: #764ba2;
            color: white;
            padding: 12px 24px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .btn-home:hover {
            background: #5e3b91;
        }

        #backToTop {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #ff9800;
            color: white;
            border: none;
            padding: 12px 14px;
            border-radius: 50%;
            font-size: 18px;
            cursor: pointer;
            display: none;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            z-index: 999;
        }

        .fireworks {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 998;
        }

        .fireworks canvas {
            width: 100%;
            height: 100%;
        }

        @media (max-width: 768px) {
            .leaderboard-container {
                margin: 20px 10px;
                padding: 20px 15px;
            }

            h2 {
                font-size: 1.5rem;
            }

            table {
                font-size: 0.85rem;
            }

            .btn-home {
                padding: 10px 5px;
                font-size: 0.9rem;
            }

            th, td {
                padding: 10px 6px;
            }
        }

        @media (max-width: 480px) {
            table {
                font-size: 0.8rem;
            }

            th, td {
                padding: 8px 5px;
            }

            .btn-home {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="leaderboard-container">
        <h2>🎉 Classement du quiz de <strong><?= htmlspecialchars($creator_name) ?></strong></h2>

        <?php if (count($results) === 0): ?>
            <p>Aucun participant pour le moment.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nom de l’ami</th>
                        <th>Score</th>
                        <th>Sur</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $index => $row): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($row['friend_name']) ?></td>
                            <td><strong><?= $row['score'] ?></strong></td>
                            <td><?= $row['total_questions'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <a href="index.php" class="btn-home">🏠 Retour à l’accueil</a>
    </div>

    <button id="backToTop" onclick="scrollToTop()">
        <i class="fas fa-arrow-up"></i>
    </button>

    <div class="fireworks">
        <canvas id="fireworksCanvas"></canvas>
    </div>

    <script>
        const backToTop = document.getElementById('backToTop');
        window.addEventListener('scroll', () => {
            backToTop.style.display = window.scrollY > 200 ? 'block' : 'none';
        });

        function scrollToTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        const canvas = document.getElementById("fireworksCanvas");
        const ctx = canvas.getContext("2d");

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        const fireworks = [];

        function createFirework() {
            return {
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height / 2,
                radius: 0,
                maxRadius: 80 + Math.random() * 40,
                alpha: 1,
                color: `hsl(${Math.random() * 360}, 100%, 50%)`
            };
        }

        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            fireworks.forEach((f, index) => {
                ctx.beginPath();
                ctx.arc(f.x, f.y, f.radius, 0, Math.PI * 2);
                ctx.fillStyle = f.color;
                ctx.globalAlpha = f.alpha;
                ctx.fill();
                ctx.globalAlpha = 1;

                f.radius += 2;
                f.alpha -= 0.02;

                if (f.alpha <= 0) fireworks.splice(index, 1);
            });

            if (Math.random() < 0.1) fireworks.push(createFirework());

            requestAnimationFrame(draw);
        }

        draw();

        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
    </script>
</body>
</html>
