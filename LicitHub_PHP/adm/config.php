<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Configurações - LicitHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"/>
  <style>
    :root {
      --primary-color: #4e73df;
      --dark-bg: #1a1a2e;
      --dark-card: #16213e;
      --dark-text: #e6e6e6;
    }

    body.dark-mode {
      background-color: var(--dark-bg);
      color: var(--dark-text);
    }

    .dark-mode .card {
      background-color: var(--dark-card);
      border-color: #2c2c3a;
    }

    .dark-mode .sidebar {
      background-color: #121212;
      color: var(--dark-text);
    }

    .dark-mode a {
      color: var(--dark-text);
    }

    .dark-mode .sidebar a.active {
      background-color: rgba(255,255,255,0.1);
    }

    .switch-label {
      display: flex;
      align-items: center;
      justify-content: space-between;
      cursor: pointer;
    }

    .sidebar {
      background-color: #f8f9fa;
      height: 100vh;
      padding: 20px;
      position: fixed;
      width: 250px;
    }

    .content {
      margin-left: 250px;
      padding: 20px;
      width: calc(100% - 250px);
    }

    .avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
    }

    .loading {
      display: inline-block;
      width: 20px;
      height: 20px;
      border: 3px solid rgba(255,255,255,.3);
      border-radius: 50%;
      border-top-color: #fff;
      animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="sidebar">
        <h4 class="mb-4"><i class="bi bi-gem me-2"></i>LicitHub</h4>
        <div class="d-flex flex-column">
          <a href="dashboard.html" class="py-2 ps-3 mb-2 rounded"><i class="bi bi-speedometer2 me-2"></i>Painel</a>
          <a href="clientes.html" class="py-2 ps-3 mb-2 rounded"><i class="bi bi-people me-2"></i>Clientes</a>
          <a href="planos.html" class="py-2 ps-3 mb-2 rounded"><i class="bi bi-card-list me-2"></i>Planos</a>
          <a href="contatos.html" class="py-2 ps-3 mb-2 rounded"><i class="bi bi-envelope me-2"></i>Contatos</a>
          <a href="config.html" class="py-2 ps-3 mb-2 rounded active"><i class="bi bi-gear me-2"></i>Configurações</a>
          <a href="#" id="logout" class="py-2 ps-3 mb-2 rounded mt-4"><i class="bi bi-box-arrow-right me-2"></i>Sair</a>
        </div>
      </div>

      <!-- Conteúdo Principal -->
      <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3><i class="bi bi-gear me-2"></i>Configurações</h3>
          <div class="d-flex align-items-center">
            <span class="me-3" id="currentUserName">Admin</span>
            <img src="https://via.placeholder.com/40" alt="Avatar" class="rounded-circle" id="userAvatar">
          </div>
        </div>

        <!-- Perfil do Usuário -->
        <div class="card mb-4">
          <div class="card-header bg-primary text-white">
            <i class="bi bi-person me-2"></i>Perfil do Usuário
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-4 text-center">
                <img src="https://via.placeholder.com/150" alt="Avatar" class="avatar mb-3" id="profileAvatar">
                <input type="file" id="avatarUpload" accept="image/*" style="display: none;">
                <button class="btn btn-sm btn-primary w-100" id="changeAvatarBtn"><i class="bi bi-upload me-1"></i>Alterar Foto</button>
              </div>
              <div class="col-md-8">
                <div class="mb-3">
                  <label for="nome" class="form-label">Nome Completo</label>
                  <input type="text" class="form-control" id="nome" value="Administrador">
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" value="admin@lichthub.com">
                </div>
                <div class="mb-3">
                  <label for="phone" class="form-label">Telefone</label>
                  <input type="tel" class="form-control" id="phone" placeholder="(00) 00000-0000">
                </div>
                <div class="mb-3">
                  <label for="senha" class="form-label">Alterar Senha</label>
                  <input type="password" class="form-control" id="senha" placeholder="Nova senha">
                </div>
                <div class="mb-3">
                  <label for="confirmSenha" class="form-label">Confirmar Senha</label>
                  <input type="password" class="form-control" id="confirmSenha" placeholder="Confirmar nova senha">
                </div>
                <div class="text-end">
                  <button class="btn btn-primary" id="salvarPerfil">
                    <span id="saveProfileText"><i class="bi bi-check-circle me-1"></i>Salvar</span>
                    <span id="saveProfileLoading" class="loading" style="display: none;"></span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Configurações de Tema -->
        <div class="card">
          <div class="card-header bg-primary text-white">
            <i class="bi bi-moon me-2"></i>Preferências de Tema
          </div>
          <div class="card-body">
            <div class="form-check form-switch mb-3">
              <label class="form-check-label switch-label" for="temaToggle">
                <span>Modo Escuro</span>
                <input class="form-check-input" type="checkbox" id="temaToggle" role="switch">
              </label>
            </div>
            <div class="text-end">
              <button class="btn btn-primary" id="salvarTema">
                <span id="saveThemeText"><i class="bi bi-check-circle me-1"></i>Aplicar</span>
                <span id="saveThemeLoading" class="loading" style="display: none;"></span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Variável global para armazenar dados do usuário
    let currentUser = {};
    
    // Função para carregar dados do usuário
    async function loadUserData() {
      try {
        // Simulando requisição ao backend
        const response = await fetch('/api/user/current', {
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          }
        });
        
        if (!response.ok) {
          throw new Error('Erro ao carregar dados do usuário');
        }
        
        const data = await response.json();
        currentUser = data;
        
        // Preencher formulário
        document.getElementById('nome').value = data.name || '';
        document.getElementById('email').value = data.email || '';
        document.getElementById('phone').value = data.phone || '';
        document.getElementById('currentUserName').textContent = data.name || 'Usuário';
        
        // Verificar tema preferido
        if (data.theme_preference === 'dark') {
          document.body.classList.add('dark-mode');
          document.getElementById('temaToggle').checked = true;
        }
        
        // Carregar avatar se existir
        if (data.avatar_url) {
          document.getElementById('profileAvatar').src = data.avatar_url;
          document.getElementById('userAvatar').src = data.avatar_url;
        }
        
      } catch (error) {
        console.error('Erro:', error);
        mostrarFeedback('Erro ao carregar dados do usuário', 'danger');
      }
    }
    
    // Função para salvar perfil
    async function saveProfile() {
      const nome = document.getElementById('nome').value;
      const email = document.getElementById('email').value;
      const phone = document.getElementById('phone').value;
      const senha = document.getElementById('senha').value;
      const confirmSenha = document.getElementById('confirmSenha').value;
      
      // Validação
      if (!nome || !email) {
        mostrarFeedback('Nome e email são obrigatórios!', 'danger');
        return;
      }
      
      if (senha && senha.length < 6) {
        mostrarFeedback('A senha deve ter pelo menos 6 caracteres!', 'danger');
        return;
      }
      
      if (senha && senha !== confirmSenha) {
        mostrarFeedback('As senhas não coincidem!', 'danger');
        return;
      }
      
      // Mostrar loading
      document.getElementById('saveProfileText').style.display = 'none';
      document.getElementById('saveProfileLoading').style.display = 'inline-block';
      
      try {
        const userData = {
          name: nome,
          email: email,
          phone: phone
        };
        
        if (senha) {
          userData.password = senha;
        }
        
        const response = await fetch('/api/user/update', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify(userData)
        });
        
        if (!response.ok) {
          throw new Error('Erro ao atualizar perfil');
        }
        
        const data = await response.json();
        mostrarFeedback('Perfil atualizado com sucesso!');
        
        // Limpar campos de senha
        document.getElementById('senha').value = '';
        document.getElementById('confirmSenha').value = '';
        
        // Atualizar dados locais
        currentUser = { ...currentUser, ...userData };
        document.getElementById('currentUserName').textContent = currentUser.name;
        
      } catch (error) {
        console.error('Erro:', error);
        mostrarFeedback('Erro ao atualizar perfil', 'danger');
      } finally {
        document.getElementById('saveProfileText').style.display = 'inline';
        document.getElementById('saveProfileLoading').style.display = 'none';
      }
    }
    
    // Função para salvar tema
    async function saveTheme() {
      const temaEscuro = document.getElementById('temaToggle').checked;
      
      // Mostrar loading
      document.getElementById('saveThemeText').style.display = 'none';
      document.getElementById('saveThemeLoading').style.display = 'inline-block';
      
      try {
        const response = await fetch('/api/user/theme', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: JSON.stringify({
            theme_preference: temaEscuro ? 'dark' : 'light'
          })
        });
        
        if (!response.ok) {
          throw new Error('Erro ao salvar preferência de tema');
        }
        
        mostrarFeedback('Preferência de tema salva com sucesso!');
        
      } catch (error) {
        console.error('Erro:', error);
        mostrarFeedback('Erro ao salvar preferência de tema', 'danger');
      } finally {
        document.getElementById('saveThemeText').style.display = 'inline';
        document.getElementById('saveThemeLoading').style.display = 'none';
      }
    }
    
    // Função para upload de avatar
    async function uploadAvatar(file) {
      const formData = new FormData();
      formData.append('avatar', file);
      
      try {
        const response = await fetch('/api/user/avatar', {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`
          },
          body: formData
        });
        
        if (!response.ok) {
          throw new Error('Erro ao enviar imagem');
        }
        
        const data = await response.json();
        mostrarFeedback('Avatar atualizado com sucesso!');
        
        // Atualizar imagens
        document.getElementById('profileAvatar').src = data.avatar_url;
        document.getElementById('userAvatar').src = data.avatar_url;
        
      } catch (error) {
        console.error('Erro:', error);
        mostrarFeedback('Erro ao enviar imagem', 'danger');
      }
    }
    
    // Função para logout
    function logout() {
      localStorage.removeItem('token');
      window.location.href = '/login.html';
    }
    
    // Mostrar feedback
    function mostrarFeedback(mensagem, tipo = 'success') {
      const feedback = document.createElement('div');
      feedback.className = `alert alert-${tipo} alert-dismissible fade show position-fixed bottom-0 end-0 m-3`;
      feedback.role = 'alert';
      feedback.innerHTML = `
        ${mensagem}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `;
      
      document.body.appendChild(feedback);
      
      // Remover após 3 segundos
      setTimeout(() => {
        feedback.remove();
      }, 3000);
    }
    
    // Event Listeners
    document.addEventListener('DOMContentLoaded', () => {
      // Carregar dados do usuário
      loadUserData();
      
      // Alternar tema localmente
      document.getElementById('temaToggle').addEventListener('change', function() {
        if (this.checked) {
          document.body.classList.add('dark-mode');
        } else {
          document.body.classList.remove('dark-mode');
        }
      });
      
      // Salvar tema
      document.getElementById('salvarTema').addEventListener('click', saveTheme);
      
      // Salvar perfil
      document.getElementById('salvarPerfil').addEventListener('click', saveProfile);
      
      // Upload de avatar
      document.getElementById('changeAvatarBtn').addEventListener('click', () => {
        document.getElementById('avatarUpload').click();
      });
      
      document.getElementById('avatarUpload').addEventListener('change', (e) => {
        if (e.target.files && e.target.files[0]) {
          const file = e.target.files[0];
          
          // Verificar se é uma imagem
          if (!file.type.match('image.*')) {
            mostrarFeedback('Por favor, selecione uma imagem válida', 'danger');
            return;
          }
          
          // Verificar tamanho (máximo 2MB)
          if (file.size > 2 * 1024 * 1024) {
            mostrarFeedback('A imagem deve ter no máximo 2MB', 'danger');
            return;
          }
          
          // Mostrar preview
          const reader = new FileReader();
          reader.onload = (event) => {
            document.getElementById('profileAvatar').src = event.target.result;
          };
          reader.readAsDataURL(file);
          
          // Enviar para o servidor
          uploadAvatar(file);
        }
      });
      
      // Logout
      document.getElementById('logout').addEventListener('click', logout);
    });
  </script>
</body>
</html>