<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema') — Sistema de Água</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --agua-primary: #0077B6;
            --agua-dark:    #023E8A;
            --agua-light:   #90E0EF;
            --sidebar-w:    260px;
        }
        body { font-family: 'Inter', sans-serif; background: #f0f4f8; }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--agua-dark) 0%, var(--agua-primary) 100%);
            position: fixed;
            top: 0; left: 0;
            display: flex; flex-direction: column;
            z-index: 1040;
        }
        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.15);
            color: #fff;
            text-decoration: none;
        }
        .sidebar-brand h5 { font-weight: 700; font-size: .95rem; margin: 0; }
        .sidebar-brand small { opacity: .7; font-size: .72rem; }
        .sidebar-nav { flex: 1; padding: 1rem 0; }
        .nav-section {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.45);
            padding: .75rem 1.25rem .25rem;
            font-weight: 600;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            padding: .55rem 1.25rem;
            border-radius: .5rem;
            margin: .1rem .75rem;
            font-size: .875rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            transition: background .2s, color .2s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,.18);
            color: #fff;
        }
        .sidebar .nav-link i { font-size: 1rem; width: 1.2rem; }
        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,.15);
            font-size: .78rem;
            color: rgba(255,255,255,.6);
        }

        /* ── Main content ── */
        .main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: .875rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .topbar h4 { margin: 0; font-weight: 600; font-size: 1.1rem; color: #1a202c; }
        .page-body { padding: 2rem; flex: 1; }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: .875rem;
            box-shadow: 0 1px 4px rgba(0,0,0,.07);
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 1.5rem;
        }

        /* ── Botão tema água ── */
        .btn-agua {
            background: var(--agua-primary);
            color: #fff;
            border: none;
        }
        .btn-agua:hover { background: var(--agua-dark); color: #fff; }

        /* ── Badge role ── */
        .badge-admin     { background:#e0f2fe; color:#0369a1; }
        .badge-leiturista{ background:#f0fdf4; color:#166534; }

        /* ── Alerts ── */
        .alert { border-radius: .75rem; border: none; }

        /* ── Table ── */
        .table > :not(caption) > * > * { padding: .75rem 1rem; }

        /* ── fw-500 ── */
        .fw-500 { font-weight: 500; }
    </style>
</head>
<body>

<!-- ══ Sidebar ══════════════════════════════════════════════════════════════ -->
<nav class="sidebar">
    <a class="sidebar-brand" href="{{ route('consumidores.index') }}">
        <div class="d-flex align-items-center gap-2 mb-1">
            <i class="bi bi-droplet-fill fs-4 text-info"></i>
            <h5>Sistema de Água</h5>
        </div>
        <small>Controle de Consumo</small>
    </a>

    <div class="sidebar-nav">
        <div class="nav-section">Cadastros</div>
        <a href="{{ route('consumidores.index') }}"
           class="nav-link {{ request()->routeIs('consumidores.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Consumidores
        </a>

        <div class="nav-section mt-2">Operações</div>
        <a href="{{ route('leituras.index') }}"
           class="nav-link {{ request()->routeIs('leituras.*') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i> Leituras
        </a>
        <a href="{{ route('faturas.index') }}"
           class="nav-link {{ request()->routeIs('faturas.*') ? 'active' : '' }}">
            <i class="bi bi-receipt"></i> Faturas
        </a>

        @if(auth()->check() && auth()->user()->isAdmin())
        <div class="nav-section mt-2">Administração</div>
        <a href="{{ route('configuracao.index') }}"
           class="nav-link {{ request()->routeIs('configuracao.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i> Configuração de Taxa
        </a>
        @endif
    </div>

    <div class="sidebar-footer">
        @auth
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <div class="fw-500 text-white" style="font-size:.82rem">{{ auth()->user()->name }}</div>
                <span class="badge badge-{{ auth()->user()->role }} mt-1">
                    {{ auth()->user()->role }}
                </span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light" title="Sair">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>
        @endauth
    </div>
</nav>

<!-- ══ Main Content ══════════════════════════════════════════════════════════ -->
<div class="main-content">
    <div class="topbar">
        <h4>@yield('page-title', 'Sistema de Água')</h4>
        <div class="d-flex align-items-center gap-3">
            @auth
            <span class="text-muted" style="font-size:.85rem">
                <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
            </span>
            @endauth
        </div>
    </div>

    <div class="page-body">
        {{-- Mensagens de feedback --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
