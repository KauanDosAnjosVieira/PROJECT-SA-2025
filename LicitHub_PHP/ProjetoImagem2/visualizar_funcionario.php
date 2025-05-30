<?php
// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'bd_imagem';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $sql = "SELECT nome, telefone, tipo_foto, foto FROM funcionarios WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir_id'])) {
                $excluir_id = $_POST['excluir_id'];
                $sql_excluir = "DELETE FROM funcionarios WHERE id = :id";
                $stmt_excluir = $pdo->prepare($sql_excluir);
                $stmt_excluir->bindParam(':id', $excluir_id, PDO::PARAM_INT);
                $stmt_excluir->execute();
                
                header("Location: consultar_funcionario.php");
                exit();
            }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Funcionário</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f7fa;
        }
        
        .container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 20px;
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        .info-container {
            display: flex;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .info-text {
            flex: 1;
        }
        
        .info-image {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        p {
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .label {
            font-weight: bold;
            color: #2c3e50;
        }
        
        img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            max-height: 300px;
        }
        
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn-excluir {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .btn-excluir:hover {
            background-color: #c0392b;
        }
        
        .btn-voltar {
            display: inline-block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
            font-size: 16px;
        }
        
        .btn-voltar:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Dados do Funcionário</h1>
        
        <div class="info-container">
            <div class="info-text">
                <p><span class="label">Nome:</span> <?= htmlspecialchars($funcionario['nome']) ?></p>
                <p><span class="label">Telefone:</span> <?= htmlspecialchars($funcionario['telefone']) ?></p>
            </div>
            
            <div class="info-image">
                <p class="label">Foto:</p>
                <img src="data:<?= $funcionario['tipo_foto'] ?>;base64,<?= base64_encode($funcionario['foto']) ?>" 
                     alt="Foto do Funcionário">
            </div>
        </div>
        
        <div class="btn-container">
            <form method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este funcionário?');">
                <input type="hidden" name="excluir_id" value="<?= $id ?>">
                <button type="submit" class="btn-excluir">Excluir Funcionário</button>
            </form>
            
            <a href="consultar_funcionario.php" class="btn-voltar">← Voltar para a lista</a>
        </div>
    </div>
    
    <script>
        // Confirmação antes de excluir
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!confirm('Tem certeza que deseja excluir este funcionário?')) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
<?php
        } else {
            echo "<div class='container'><p>Funcionário não encontrado.</p></div>";
        }
    } else {
        echo "<div class='container'><p>ID do funcionário não foi fornecido.</p></div>";
    }
} catch (PDOException $e) {
    echo "<div class='container'><p>Erro: " . $e->getMessage() . "</p></div>";
}
?>