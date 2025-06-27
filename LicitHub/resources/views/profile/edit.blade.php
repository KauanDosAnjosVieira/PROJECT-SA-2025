<head>
@vite(['resources/css/login.css'])

    
</head>
<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <h2 class="profile-title">Informações do Perfil</h2>
            <p class="profile-subtitle">Atualize suas informações de perfil e endereço de e-mail</p>
        </div>
        
        <div class="profile-form">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    <div class="profile-card">
        <div class="profile-header">
            <h2 class="profile-title">Atualizar Senha</h2>
            <p class="profile-subtitle">Certifique-se de usar uma senha longa e aleatória para manter a segurança</p>
        </div>
        
        <div class="profile-form">
            @include('profile.partials.update-password-form')
        </div>
    </div>

    <div class="profile-card">
        <div class="delete-account-section">
            <div class="delete-account-header">
                <i class="fas fa-exclamation-triangle"></i>
                <h2 class="profile-title">Excluir Conta</h2>
            </div>
            
            <div class="delete-account-content">
                <p>Uma vez que sua conta for excluída, todos os seus dados serão permanentemente removidos.</p>
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>