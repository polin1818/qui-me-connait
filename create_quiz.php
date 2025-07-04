<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Créer mon quiz</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
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
      justify-content: center;
      align-items: center;
    }

    .form-container {
      width: 100%;
      max-width: 850px;
      background: rgba(0, 0, 0, 0.5);
      padding: 35px;
      border-radius: 18px;
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.3);
      max-height: 90vh;
      overflow-y: auto;
      animation: fadeIn 1s ease;
    }

    h2 {
      text-align: center;
      margin-bottom: 25px;
      font-size: 2rem;
      animation: fadeInTop 1s ease-in-out;
    }

    label {
      font-weight: bold;
      display: block;
      margin: 8px 0 5px;
    }

    input[type="text"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border-radius: 10px;
      border: none;
      font-size: 1em;
    }

    .question-block {
      background-color: rgba(255, 255, 255, 0.08);
      padding: 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      position: relative;
      animation: fadeInUp 0.6s ease;
    }

    .question-block button.remove-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: transparent;
      color: #ffaaaa;
      border: none;
      font-size: 1.1rem;
      cursor: pointer;
    }

    .btn-add,
    .btn-submit {
      padding: 14px 26px;
      font-size: 1em;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      margin: 10px 8px 0 0;
      transition: all 0.3s ease;
      display: inline-block;
    }

    .btn-add {
      background-color: #00c9a7;
      color: white;
    }

    .btn-add:hover {
      background-color: #00b38f;
    }

    .btn-submit {
      background-color: #ff6363;
      color: white;
    }

    .btn-submit:hover {
      background-color: #e63d3d;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInTop {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    /* Responsive */
    @media screen and (max-width: 600px) {
      h2 {
        font-size: 1.5rem;
      }

      input[type="text"] {
        font-size: 0.95rem;
        padding: 10px;
      }

      .btn-add, .btn-submit {
        width: 100%;
        margin-top: 15px;
        font-size: 1rem;
      }
    }
  </style>

  <script>
    let questionCount = 1;

    function addQuestion() {
      questionCount++;
      const container = document.getElementById('questions-container');

      const div = document.createElement('div');
      div.className = 'question-block';
      div.innerHTML = `
        <button type="button" class="remove-btn" onclick="removeQuestion(this)">✖</button>
        <label>Question ${questionCount} :</label>
        <input type="text" name="questions[]" required placeholder="Ex: Quel est mon plat préféré ?">
        <label>Bonne réponse :</label>
        <input type="text" name="answers[]" required placeholder="Ex: Pizza">
      `;

      container.appendChild(div);
      div.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    function removeQuestion(button) {
      if (confirm("❗ Voulez-vous vraiment supprimer cette question ?")) {
        const block = button.parentElement;
        block.remove();
        questionCount--;
        updateQuestionLabels();
      }
    }

    function updateQuestionLabels() {
      const blocks = document.querySelectorAll('.question-block');
      blocks.forEach((block, index) => {
        const label = block.querySelector('label');
        if (label) label.innerText = `Question ${index + 1} :`;
      });
    }
  </script>
</head>
<body>

  <div class="form-container">
    <h2>Créer mon quiz personnalisé 🧠</h2>

    <form action="save_quiz.php" method="POST">
      <label>Ton prénom :</label>
      <input type="text" name="username" required placeholder="Ex: Lorenzo">

      <div id="questions-container">
        <div class="question-block">
          <button type="button" class="remove-btn" onclick="removeQuestion(this)" style="display:none;">✖</button>
          <label>Question 1 :</label>
          <input type="text" name="questions[]" required placeholder="Ex: Quel est mon film préféré ?">
          <label>Bonne réponse :</label>
          <input type="text" name="answers[]" required placeholder="Ex: Inception">
        </div>
      </div>

      <button type="button" class="btn-add" onclick="addQuestion()">➕ Ajouter une question</button>
      <button type="submit" class="btn-submit">🎯 Enregistrer le quiz</button>
    </form>
  </div>

</body>
</html>
