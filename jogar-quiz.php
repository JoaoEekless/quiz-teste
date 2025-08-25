<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}

// Verifica se foi passado um quiz
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}
$quiz_id = (int)$_GET['id'];

// Busca informações do quiz
$stmt = $pdo->prepare("
    SELECT quizzes.*, temas.nome as tema_nome 
    FROM quizzes 
    JOIN temas ON quizzes.tema_id = temas.id 
    WHERE quizzes.id = ?
");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();

if (!$quiz) {
    header("Location: dashboard.php");
    exit();
}

// Busca perguntas do quiz
$stmt = $pdo->prepare("SELECT * FROM perguntas WHERE quiz_id = ? ORDER BY id");
$stmt->execute([$quiz_id]);
$perguntas = $stmt->fetchAll();

// Controle do jogo
$pergunta_atual = isset($_POST['pergunta_atual']) ? (int)$_POST['pergunta_atual'] : 0;
$respostas = isset($_POST['respostas']) ? $_POST['respostas'] : [];

// Se enviou uma resposta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resposta'])) {
    $respostas[$pergunta_atual] = $_POST['resposta'];
    $pergunta_atual++;
}

// Se terminou o quiz
if ($pergunta_atual >= count($perguntas)) {
    // Calcula pontuação
    $pontuacao_total = 0;
    
    foreach ($perguntas as $index => $pergunta) {
        if (isset($respostas[$index]) && $respostas[$index] === $pergunta['resposta_correta']) {
            $pontuacao_total += $pergunta['pontos'];
        }
    }
    
    // Salva resultado no banco
    $stmt = $pdo->prepare("INSERT INTO resultados (usuario_id, quiz_id, pontuacao) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['usuario_id'], $quiz_id, $pontuacao_total]);
    
    // Mostra resultado
    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Resultado - <?= $quiz['titulo'] ?></title>
        <link rel="stylesheet" href="css/dashboard.css">
        <style>
            .resultado-box {
                background-color: #3e2d88ff;
                padding: 40px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                text-align: center;
                margin: 40px auto;
                max-width: 500px;
            }
            .pontuacao {
                font-size: 48px;
                font-weight: bold;
                color: #6c5ce7;
                margin: 20px 0;
            }
            .btn-group {
                display: flex;
                gap: 15px;
                justify-content: center;
                margin-top: 30px;
            }
        </style>
    </head>
    <body>
        <header class="header">
            <div class="header-content">
                <a href="dashboard.php" class="logo">Quizify</a>
            </div>
        </header>
        
        <div class="container">
            <div class="resultado-box">
                <h1>Quiz Finalizado!</h1>
                <h2><?= $quiz['titulo'] ?></h2>
                
                <div class="pontuacao"><?= $pontuacao_total ?></div>
                <p>pontos conquistados</p>
                
                <div class="btn-group">
                    <a href="jogar-quiz.php?id=<?= $quiz_id ?>" class="btn-quiz">Jogar Novamente</a>
                    <a href="quizzes.php?tema=<?= $quiz['tema_id'] ?>" class="btn-quiz">Outros Quizzes</a>
                    <a href="dashboard.php" class="btn-quiz">Dashboard</a>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Mostra pergunta atual
$pergunta = $perguntas[$pergunta_atual];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $quiz['titulo'] ?> - Pergunta <?= $pergunta_atual + 1 ?></title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="icon" type="image/svg+xml" href="img/cerebro1.svg">
</head>
<body>
    <header class="header">
        <div class="header-content">
            <a href="dashboard.php" class="logo"> Quizify</a>
            <div class="user-menu">
                <span><?= $quiz['tema_nome'] ?></span>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="quiz-container">
            <div class="quiz-header">
                <h2><?= $quiz['titulo'] ?></h2>
                <p>Pergunta <?= $pergunta_atual + 1 ?> de <?= count($perguntas) ?></p>
                
                <div class="progress">
                    <div class="progress-bar" style="width: <?= (($pergunta_atual + 1) / count($perguntas)) * 100 ?>%"></div>
                </div>
            </div>

            <form method="POST">
                <div class="pergunta">
                    <?= $pergunta['pergunta'] ?>
                </div>

                <div class="alternativas">
                    <div class="alternativa">
                        <input type="radio" name="resposta" value="a" id="alt_a" required>
                        <label for="alt_a"><?= $pergunta['alternativa_a'] ?></label>
                    </div>
                    
                    <div class="alternativa">
                        <input type="radio" name="resposta" value="b" id="alt_b" required>
                        <label for="alt_b"><?= $pergunta['alternativa_b'] ?></label>
                    </div>
                    
                    <div class="alternativa">
                        <input type="radio" name="resposta" value="c" id="alt_c" required>
                        <label for="alt_c"><?= $pergunta['alternativa_c'] ?></label>
                    </div>
                </div>

                <!-- Campos ocultos para manter estado -->
                <input type="hidden" name="pergunta_atual" value="<?= $pergunta_atual ?>">
                <?php foreach ($respostas as $index => $resposta): ?>
                    <input type="hidden" name="respostas[<?= $index ?>]" value="<?= $resposta ?>">
                <?php endforeach; ?>

                <button type="submit" class="btn-proximo">
                    <?= $pergunta_atual < count($perguntas) - 1 ? 'Próxima Pergunta' : 'Finalizar Quiz' ?>
                </button>
            </form>
        </div>
    </div>

   <style>
    .quiz-container {
        background: linear-gradient(135deg, #2f2841, #514869);
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        margin: 40px auto;
        max-width: 600px;
        color: #fff;
    }

    .quiz-header {
        text-align: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #40395c;
    }

    .progress {
        background-color: #3b3454;
        height: 8px;
        border-radius: 4px;
        margin: 20px 0;
    }

    .progress-bar {
        background: linear-gradient(135deg, #00ff88, #00e676);
        height: 100%;
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .pergunta {
        font-size: 20px;
        font-weight: bold;
        color: #ffffff;
        margin-bottom: 30px;
        text-align: center;
        line-height: 1.4;
    }

    .alternativas {
        margin-bottom: 30px;
    }

    .alternativa {
        background-color: #352f44;
        border: 2px solid transparent;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: flex-start;
        color: #ccc;
    }

    .alternativa:hover {
        background-color: #40395c;
        border-color: #00ff88;
    }

    .alternativa input[type="radio"] {
        margin-right: 15px;
        margin-top: 3px;
        flex-shrink: 0;
    }

    .alternativa label {
        flex: 1;
        cursor: pointer;
        line-height: 1.5;
        word-wrap: break-word;
    }

    .btn-proximo {
        background: linear-gradient(135deg, #00ff88, #00e676);
        color: #2b134b;
        padding: 12px 30px;
        border: none;
        border-radius: 10px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        width: 100%;
        transition: background 0.3s ease;
    }

    .btn-proximo:hover {
        background: linear-gradient(135deg, #00e676, #00c853);
    }
</style>

</body>
</html>