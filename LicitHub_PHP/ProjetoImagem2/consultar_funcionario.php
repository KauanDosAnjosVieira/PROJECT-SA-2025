<?php
// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'bd_imagem';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recupera todos os funcionários do banco de dados
    $sql = "SELECT id, nome FROM funcionarios";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $funcionarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verifica se foi solicitado a exclusão de um funcionário
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['excluir_id'])) {
        $excluir_id = $_POST['excluir_id'];
        $sql_excluir = "DELETE FROM funcionarios WHERE id = :id";
        $stmt_excluir = $pdo->prepare($sql_excluir);
        $stmt_excluir->bindParam(':id', $excluir_id, PDO::PARAM_INT);
        $stmt_excluir->execute();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Funcionário</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        /* Estilos do Dropdown */
        .navbar {
            background-color: #2c3e50;
            overflow: hidden;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .dropdown {
            float: left;
            overflow: hidden;
        }
        
        .dropdown .dropbtn {
            font-size: 16px;
            border: none;
            outline: none;
            color: white;
            padding: 14px 16px;
            background-color: inherit;
            font-family: inherit;
            margin: 0;
            cursor: pointer;
        }
        
        .navbar a:hover, .dropdown:hover .dropbtn {
            background-color: #3498db;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 0 0 5px 5px;
        }
        
        .dropdown-content a {
            float: none;
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
        }
        
        .dropdown-content a:hover {
            background-color: #ddd;
        }
        
        .dropdown:hover .dropdown-content {
            display: block;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        .funcionarios-list {
            list-style: none;
            padding: 0;
        }
        
        .funcionario-item {
            background: white;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s;
        }
        
        .funcionario-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .funcionario-nome {
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
            flex-grow: 1;
        }
        
        .funcionario-nome:hover {
            text-decoration: underline;
        }
        
        .excluir-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 15px;
            transition: background-color 0.3s;
        }
        
        .excluir-btn:hover {
            background-color: #c0392b;
        }
        
        .mensagem {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        
        .sucesso {
            background-color: #d4edda;
            color: #155724;
        }
        
        .erro {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .voltar-link {
            display: inline-block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }
        
        .voltar-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Menu Dropdown -->
    <div class="navbar">
        <div class="dropdown">
            <button class="dropbtn">Menu 
                <i class="fa fa-caret-down"></i>
            </button>
            <div class="dropdown-content">
                <a href="index.php">Início</a>
                <a href="cadastro_funcionario.php">Cadastrar Funcionário</a>
                <a href="consultar_funcionario.php">Consultar Funcionários</a>
                <a href="visualizar_funcionario.php">Visualizar Funcionário</a>
            </div>
        </div>
    </div>

    <h1>Consulta de Funcionários</h1>

    <?php if (empty($funcionarios)): ?>
        <div class="mensagem">Nenhum funcionário cadastrado.</div>
    <?php else: ?>
        <ul class="funcionarios-list">
            <?php foreach ($funcionarios as $funcionario): ?>
                <li class="funcionario-item">
                    <a href="visualizar_funcionario.php?id=<?= $funcionario['id']; ?>" class="funcionario-nome">
                        <?= htmlspecialchars($funcionario['nome']); ?>
                    </a>
                    <form method="POST" style="display:inline">
                        <input type="hidden" name="excluir_id" value="<?= $funcionario['id']; ?>">
                        <button type="submit" class="excluir-btn" onclick="return confirm('Tem certeza que deseja excluir este funcionário?');">
                            Excluir
                        </button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    
    <a href="index.php" class="voltar-link">← Voltar ao menu principal</a>

    <script>
        // Confirmação antes de excluir
        document.querySelectorAll('.excluir-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                if (!confirm('Tem certeza que deseja excluir este funcionário?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>