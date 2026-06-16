<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Controle de Consumo de Água — Gestão de leituras e faturas da associação comunitária">
    <title>@yield('title', 'Painel') — Sistema de Água</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --agua-primary:   #0077b6;
            --agua-secondary: #00b4d8;
            --agua-accent:    #90e0ef;
            --agua-dark:      #03045e;
            --agua-light:     #caf0f8;
            --sidebar-width:  260px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Sidebar ──────────────────────────────── */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--agua-dark) 0%, var(--agua-primary) 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
        }

        .sidebar-brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand h5 {
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            margin: 0;
            line-height: 1.3;
        }

        .sidebar-brand small {
            color: var(--agua-accent);
            font-size: 0.75rem;
        }

        .sidebar-brand .brand-icon {
            font-size: 2rem;
            color: var(--agua-accent);
            display: block;
            margin-bottom: 0.5rem;
        }

        .sidebar-nav { padding: 1rem 0.75rem; flex: 1; }

        .sidebar-nav .nav-label {
            color: rgba(255,255,255,0.45);
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0.75rem 0.75rem 0.25rem;
        }

        .sidebar-nav .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            padding: 0.6rem 0.85rem;
            margin-bottom: 2px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
            transform: translateX(3px);
        }

        .sidebar-nav .nav-link i { font-size: 1rem; width: 20px; text-align: center; }

        /* ── Main content ─────────────────────────── */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.875rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 1px 8px rgba(0,0,0,0.06);
        }

        .topbar-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--agua-dark);
            margin: 0;
        }

        .content-area { padding: 1.75rem; flex: 1; }

        /* ── Cards ────────────────────────────────── */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            border-radius: 12px 12px 0 0 !important;
            padding: 1rem 1.25rem;
            font-weight: 600;
            color: var(--agua-dark);
        }

        /* ── Stat cards ───────────────────────────── */
        .stat-card {
            background: linear-gradient(135deg, var(--agua-primary), var(--agua-secondary));
            color: #fff;
            border-radius: 12px;
            padding: 1.25rem;
            box-shadow: 0 4px 15px rgba(0,119,182,0.3);
        }

        .stat-card.success {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
            box-shadow: 0 4px 15px rgba(46,204,113,0.3);
        }

        .stat-card.warning {
            background: linear-gradient(135deg, #f39c12, #e67e22);
            box-shadow: 0 4px 15px rgba(243,156,18,0.3);
        }

        .stat-card .stat-value { font-size: 1.75rem; font-weight: 700; }
        .stat-card .stat-label { font-size: 0.8rem; opacity: 0.85; font-weight: 500; }

        /* ── Buttons ──────────────────────────────── */
        .btn-agua {
            background: linear-gradient(135deg, var(--agua-primary), var(--agua-secondary));
            color: #fff;
            border: none;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-agua:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,119,182,0.4);
            color: #fff;
        }

        /* ── Badges ───────────────────────────────── */
        .badge-pendente { background: #fef3c7; color: #92400e; font-weight: 600; padding: 0.35em 0.7em; border-radius: 6px; }
        .badge-pago     { background: #d1fae5; color: #065f46; font-weight: 600; padding: 0.35em 0.7em; border-radius: 6px; }

        /* ── Tables ───────────────────────────────── */
        .table th {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
        }

        .table td { vertical-align: middle; font-size: 0.875rem; }

        /* ── Alerts ───────────────────────────────── */
        .alert { border: none; border-radius: 10px; font-size: 0.875rem; }

        /* ── Forms ────────────────────────────────── */
        .form-control, .form-select {
            border-radius: 8px;
            border-color: #e2e8f0;
            font-size: 0.875rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--agua-secondary);
            box-shadow: 0 0 0 3px rgba(0,180,216,0.15);
        }

        .form-label { font-weight: 500; font-size: 0.85rem; color: #374151; }

        /* ── Footer ───────────────────────────────── */
        footer {
            background: #fff;
            border-top: 1px solid #e2e8f0;
            padding: 0.75rem 1.5rem;
            font-size: 0.75rem;
            color: #94a3b8;
            text-align: center;
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ── Sidebar ──────────────────────────────────────────── --}}
<aside class="sidebar">
    <div class="sidebar-brand">
        <i class="bi bi-droplet-fill brand-icon"></i>
        <h5>Sistema de Água</h5>
        <small>Controle de Consumo</small>
    </div>

    <nav class="sidebar-nav">
        <p class="nav-label">Menu Principal</p>

        <a href="{{ route('consumidores.index') }}"
           class="nav-link {{ request()->routeIs('consumidores.*') ? 'active' : '' }}"
           id="nav-consumidores">
            <i class="bi bi-people-fill"></i> Consumidores
        </a>

        <a href="{{ route('leituras.create') }}"
           class="nav-link {{ request()->routeIs('leituras.create') ? 'active' : '' }}"
           id="nav-nova-leitura">
            <i class="bi bi-speedometer2"></i> Nova Leitura
        </a>

        <a href="{{ route('leituras.index') }}"
           class="nav-link {{ request()->routeIs('leituras.index') ? 'active' : '' }}"
           id="nav-leituras">
            <i class="bi bi-list-check"></i> Leituras
        </a>

        <a href="{{ route('faturas.index') }}"
           class="nav-link {{ request()->routeIs('faturas.*') ? 'active' : '' }}"
           id="nav-faturas">
            <i class="bi bi-receipt"></i> Faturas
        </a>

        <p class="nav-label mt-3">Configurações</p>

        <a href="{{ route('configuracao.index') }}"
           class="nav-link {{ request()->routeIs('configuracao.*') ? 'active' : '' }}"
           id="nav-configuracao">
            <i class="bi bi-gear-fill"></i> Taxa de Cobrança
        </a>
    </nav>
</aside>

{{-- ── Main ──────────────────────────────────────────────── --}}
<div class="main-wrapper">
    <header class="topbar">
        <h1 class="topbar-title">@yield('page-title', 'Painel')</h1>
        <span class="text-muted small"><i class="bi bi-calendar3"></i> {{ now()->translatedFormat('F \d\e Y') }}</span>
    </header>

    <main class="content-area">
        {{-- Alertas globais --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="bi bi-info-circle-fill"></i>
                {{ session('info') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ session('error') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer>
        &copy; {{ date('Y') }} Sistema de Controle de Consumo de Água — Associação Comunitária
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
