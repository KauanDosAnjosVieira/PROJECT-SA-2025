<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário Cadastro</title>
    <link rel="stylesheet" href="css/cadastro.css">
</head>
<body>
    <!-- Container principal para centralizar tudo -->
    <div class="main-wrapper">
        <!-- Menu Dropdown - Agora acima do formulário -->
        <div class="navbar">
            <div class="dropdown">
                <button class="dropbtn">
                    <span>Menu de Navegação</span>
                </button>
                <div class="dropdown-content">
                    <a href="index.php">Início</a>
                    <a href="cadastro_funcionario.php">Cadastrar Funcionário</a>
                    <a href="consultar_funcionario.php">Consultar Funcionários</a>
                    <a href="visualizar_funcionario.php">Visualizar Funcionário</a>
                </div>
            </div>
        </div>

        <div class="container">
            <h1>Cadastro</h1>
            <h2>Funcionário</h2>
            <!-- Formulário para Cadastrar Funcionário -->
            <form action="salvar_funcionario.php" method="POST" enctype="multipart/form-data">
                <div>
                    <label for="nome">Nome:</label>
                    <input type="text" name="nome" id="nome" required placeholder="Digite o nome completo">
                </div>
                
                <div>
                    <label for="telefone">Telefone:</label>
                    <input type="tel" name="telefone" id="telefone" required placeholder="(00) 00000-0000">
                </div>
                
                <div>
                    <label for="foto">Foto:</label>
                    <div class="file-input-wrapper">
                        <div class="file-input-button">
                            <span class="file-input-text" id="file-name">Selecione uma foto</span>
                            <span class="file-input-icon">📁</span>
                        </div>
                        <input type="file" name="foto" id="foto" required accept="image/*">
                    </div>
                </div>
                
                <button type="submit">Cadastrar</button>
            </form>
        </div>
    </div>

    <script>
        // Script para mostrar o nome do arquivo selecionado
        document.getElementById('foto').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : 'Selecione uma foto';
            document.getElementById('file-name').textContent = fileName;
        });
    </script>
</body>
</html>