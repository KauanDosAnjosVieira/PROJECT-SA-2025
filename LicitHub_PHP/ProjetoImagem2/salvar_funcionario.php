<?php
function redimensionarImagem($imagem, $largura, $altura) {
    // obtém as dimensões da imagem original
    list($larguraOriginal, $alturaOriginal) = getimagesize($imagem);

    // cria uma nova imagem com as dimensões especificadas
    $novaImagem = imagecreatetruecolor($largura, $altura);

    // cria uma imagem a partir do arquivo original (FORMATO JPEG)
    $imagemOriginal = imagecreatefromjpeg($imagem);

    // copia e redimensiona a imagem original para a nova imagem
    imagecopyresampled($novaImagem, $imagemOriginal, 0, 0, 0, 0, $largura, $altura, $larguraOriginal, $alturaOriginal);

    // inicia a saída em buffer para capturar os dados da imagem
    ob_start();
    imagejpeg($novaImagem);
    $dadosImagem = ob_get_clean();

    // libera a memória
    imagedestroy($novaImagem);
    imagedestroy($imagemOriginal);
    return $dadosImagem; // retorna os dados da imagem redimensionada
}

// conexão com o banco de dados
$host = 'localhost';
$dbname = 'bd_imagem';
$username = 'root';
$password = '';

try {
    // cria uma nova instância de PDO para conectar ao banco de dados
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // define o modo de erro para exceção

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto'])) {
        $nome = $_POST['nome'];
        $telefone = $_POST['telefone'];

        // Verifica se o arquivo foi enviado sem erros
        if ($_FILES['foto']['error'] == UPLOAD_ERR_OK) {
            $nome_foto = $_FILES['foto']['name']; // pega o nome do arquivo enviado
            $tipo_foto = $_FILES['foto']['type']; // pega o tipo do arquivo enviado

            // redimensiona a imagem para 300x400 pixels
            $foto = redimensionarImagem($_FILES['foto']['tmp_name'], 300, 400);

            // prepara a instrução SQL para inserir os dados do funcionário no banco de dados
            $sql = "INSERT INTO funcionarios (nome, telefone, nome_foto, tipo_foto, foto) VALUES (:nome, :telefone, :nome_foto, :tipo_foto, :foto)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':nome_foto', $nome_foto);
            $stmt->bindParam(':tipo_foto', $tipo_foto);
            $stmt->bindParam(':foto', $foto, PDO::PARAM_LOB); // usa o tipo de dado LOB para armazenar a imagem binária

            if ($stmt->execute()) {
                echo "Funcionário cadastrado com sucesso!";
            } else {
                echo "Erro ao cadastrar funcionário.";
            }
        } else {
            echo "Erro no upload da foto! Código do erro: " . $_FILES['foto']['error'];
        }
    }
} catch (PDOException $e) {
    echo "Erro. " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Imagens</title>
</head>
<body>
    <h1>Lista de Imagens</h1>

    <a href="consultar_funcionario.php">Listar Funcionários</a>
</body>
</html>