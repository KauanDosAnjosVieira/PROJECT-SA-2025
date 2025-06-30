@php
    $user = auth()->user();
    if (!($user->user_type === 'admin')) {
        header('Location: ' . url('/'));
        exit;
    }
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard | Sistema de Gerenciamento</title>
    
    <!-- Fontes e Ícones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Assets -->
    @vite(['resources/css/admin.css'])
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/images/favicon.png">
    
    <!-- Meta Tags -->
    <meta name="description" content="Painel administrativo completo para gerenciamento de clientes, produtos e pedidos">
</head>
<body class="dark-mode">
    <div class="admin-container">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <span class="logo-text">Licit <span class="logo-highlight">Admin</span></span>
                </div>
                <button class="sidebar-close" id="sidebar-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="sidebar-menu">
                <ul>
                    <li class="menu-title">PRINCIPAL</li>
                    <li class="menu-item active">
                        <a href="{{ route('admin.dashboard') }}">
                            <div class="menu-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <span class="menu-text">Dashboard</span>
                            
                        </a>
                    </li>
                    
                    <li class="menu-title">GERENCIAMENTO</li>
                    <li class="menu-item">
                        <a href="{{ route('clients.index') }}">
                            <div class="menu-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <span class="menu-text">Clientes</span>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="{{ route('admins.index') }}">
                            <div class="menu-icon">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <span class="menu-text">Administradores</span>
                        </a>
                    </li>
                    <li class="menu-item">
                    <a href="{{ route('plans.index') }}">
                            <div class="menu-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <span class="menu-text">Planos</span>
                        </a>
                    </li>
                    <!--<li class="menu-item has-submenu">
                        <a href="#">
                            <div class="menu-icon">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <span class="menu-text">Pedidos</span>
                            <div class="menu-arrow">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </a>
                        <ul class="submenu">
                            <li>
                                <a href="#">
                                    <span class="submenu-text">Novos Pedidos</span>
                                    <span class="submenu-badge">5</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="submenu-text">Completos</span>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <span class="submenu-text">Cancelados</span>
                                </a>
                            </li>
                        </ul>
                    </li>-->
                    <li class="menu-item">
                        <a href="#">
                            <div class="menu-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <span class="menu-text">Relatórios</span>
                        </a>
                    </li>
                    
                    <!--<li class="menu-title">SISTEMA</li>
                    <li class="menu-item">
                        <a href="#">
                            <div class="menu-icon">
                                <i class="fas fa-cog"></i>
                            </div>
                            <span class="menu-text">Configurações</span>
                        </a>
                    </li>-->
                    
                </ul>
            </div>
            
            <div class="sidebar-footer">
                <div class="user-panel">
                    <div class="user-avatar">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" alt="{{ Auth::user()->name }}">
                    </div>
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <span class="user-role">{{ Auth::user()->user_type }}</span>
                    </div>
                </div>
                
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sair</span>
                    </button>
                </form>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <div class="header-left">
                    <button class="sidebar-toggle" id="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="header-title">
                        <h1>Dashboard - {{ Auth::user()->name }}</h1>
                        <!-- <nav class="breadcrumb">
                            <a href="#">Home</a>
                            <i class="fas fa-chevron-right"></i>
                            <span>Dashboard</span>
                        </nav>-->
                    </div>
                </div>
                <div class="header-right">
                    <div class="header-actions">
                        <button class="theme-toggle" id="theme-toggle">
                            <i class="fas fa-moon"></i>
                            <i class="fas fa-sun"></i>
                        </button>
                        
                           
                        <div class="header-profile">
                            <div class="profile-avatar">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=random" alt="{{ Auth::user()->name }}">
                            </div>
                            <div class="profile-dropdown">
                                <div class="profile-header">
                                    <div class="profile-info">
                                        <h4>{{ Auth::user()->name }}</h4>
                                        <span>{{ Auth::user()->user_type }}</span>
                                    </div>
                                </div>
                                <div class="profile-links">
                                    <a href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user"></i>
                                        <span>Meu Perfil</span>
                                    </a>
                                    <!--<a href="#">
                                        <i class="fas fa-cog"></i>
                                        <span>Configurações</span>
                                    </a>-->
                                </div>
                                <form method="POST" action="{{ route('logout') }}" class="profile-logout">
                                    @csrf
                                    <button type="submit">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Sair</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <div class="content-wrapper">
                <div class="dashboard-overview">
                    <div class="cards-grid">
                        <div class="card">
                            <div class="card-icon bg-gradient-blue">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="card-info">
                                <h3>Total de Clientes</h3>
                                <span>1,254</span>
                                <div class="card-trend up">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>12.5%</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-icon bg-gradient-green">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="card-info">
                                <h3>Pedidos Hoje</h3>
                                <span>356</span>
                                <div class="card-trend up">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>8.2%</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-icon bg-gradient-orange">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="card-info">
                                <h3>Receita Total</h3>
                                <span>R$12,540</span>
                                <div class="card-trend down">
                                    <i class="fas fa-arrow-down"></i>
                                    <span>3.1%</span>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-icon bg-gradient-purple">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <div class="card-info">
                                <h3>Crescimento</h3>
                                <span>+12.5%</span>
                                <div class="card-trend up">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>4.7%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="dashboard-content">
                        <div class="content-section">
                            <div class="section-header">
                                <h2>Atividade Recente</h2>
                                <div class="section-actions">
                                    <div class="section-filter">
                                        <select class="form-select">
                                            <option>Hoje</option>
                                            <option>Esta Semana</option>
                                            <option selected>Este Mês</option>
                                            <option>Este Ano</option>
                                        </select>
                                    </div>
                                    <a href="#" class="btn btn-link">Ver Tudo</a>
                                </div>
                            </div>
                            <div class="section-content">
                                <div class="activity-list">
                                    <div class="activity-item">
                                        <div class="activity-icon bg-blue">
                                            <i class="fas fa-shopping-cart"></i>
                                        </div>
                                        <div class="activity-content">
                                            <p><strong>Pedido #1254</strong> foi realizado por <a href="#">João Silva</a></p>
                                            <span class="activity-time">2 minutos atrás</span>
                                        </div>
                                        <div class="activity-amount">
                                            R$ 245,90
                                        </div>
                                    </div>
                                    <div class="activity-item">
                                        <div class="activity-icon bg-green">
                                            <i class="fas fa-user-plus"></i>
                                        </div>
                                        <div class="activity-content">
                                            <p><strong>Novo cliente</strong> <a href="#">Maria Souza</a> se registrou</p>
                                            <span class="activity-time">1 hora atrás</span>
                                        </div>
                                    </div>
                                    <div class="activity-item">
                                        <div class="activity-icon bg-orange">
                                            <i class="fas fa-box"></i>
                                        </div>
                                        <div class="activity-content">
                                            <p><strong>Novo produto</strong> <a href="#">iPhone 13</a> adicionado ao catálogo</p>
                                            <span class="activity-time">3 horas atrás</span>
                                        </div>
                                        <div class="activity-amount">
                                            15 unidades
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="content-section">
                            <div class="section-header">
                                <h2>Visão Geral das Vendas</h2>
                                <div class="section-actions">
                                    <div class="section-filter">
                                        <select class="form-select">
                                            <option>Diário</option>
                                            <option selected>Semanal</option>
                                            <option>Mensal</option>
                                            <option>Anual</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="section-content">
                                <div class="chart-container">
                                    <canvas id="salesChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle Sidebar
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebar-toggle');
            const closeBtn = document.getElementById('sidebar-close');
            
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                document.querySelector('.main-content').classList.toggle('expanded');
            });
            
            closeBtn.addEventListener('click', function() {
                sidebar.classList.add('collapsed');
                document.querySelector('.main-content').classList.add('expanded');
            });
            
            // Submenu toggle
            const submenuItems = document.querySelectorAll('.has-submenu > a');
            submenuItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const submenu = this.nextElementSibling;
                    const parent = this.parentElement;
                    
                    parent.classList.toggle('open');
                    submenu.style.maxHeight = parent.classList.contains('open') 
                        ? submenu.scrollHeight + 'px' 
                        : '0';
                });
            });
            
            // Theme Toggle
            const themeToggle = document.getElementById('theme-toggle');
            themeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                
                if (document.body.classList.contains('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light');
                }
            });
            
            // Check for saved theme preference
            if (localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark-mode');
            }
            
            // Initialize Chart
            const ctx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                    datasets: [{
                        label: 'Vendas 2023',
                        data: [12000, 19000, 15000, 18000, 22000, 25000, 30000],
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
            
            // Notification dropdown
            
            // Profile dropdown
            const profileAvatar = document.querySelector('.profile-avatar');
            const profileDropdown = document.querySelector('.profile-dropdown');
            
            profileAvatar.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('show');
            });
            
            // Close dropdowns when clicking outside
            document.addEventListener('click', function() {
                notificationDropdown.classList.remove('show');
                profileDropdown.classList.remove('show');
            });
        });
    </script>
</body>
</html>