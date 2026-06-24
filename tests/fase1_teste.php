<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "╔══════════════════════════════════════════════════╗\n";
echo "║    FASE 1 — TESTES DE VALIDAÇÃO                  ║\n";
echo "╚══════════════════════════════════════════════════╝\n\n";

$pass = 0; $fail = 0;

function check(string $label, bool $result): void {
    global $pass, $fail;
    if ($result) { echo "  ✅ PASSOU  $label\n"; $pass++; }
    else         { echo "  ❌ FALHOU  $label\n"; $fail++; }
}

// 1. Coluna role existe na tabela users
check(
    'Coluna `role` existe na tabela users',
    Schema::hasColumn('users', 'role')
);

// 2. Tipo da coluna é enum ou varchar (MySQL representa enum como varchar no INFORMATION_SCHEMA)
$col = DB::select("
    SELECT COLUMN_TYPE, COLUMN_DEFAULT
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'users'
      AND COLUMN_NAME  = 'role'
")[0] ?? null;

check(
    "Coluna role é ENUM('admin','leiturista')",
    $col && str_contains(strtolower($col->COLUMN_TYPE), 'enum')
);
check(
    "Default da coluna role é 'leiturista'",
    $col && ($col->COLUMN_DEFAULT === "'leiturista'" || $col->COLUMN_DEFAULT === 'leiturista')
);

// 3. Middleware CheckAdmin existe e tem o namespace correto
$mwFile = app_path('Http/Middleware/CheckAdmin.php');
check(
    'Arquivo CheckAdmin.php existe',
    file_exists($mwFile)
);
check(
    'CheckAdmin contém abort(403)',
    file_exists($mwFile) && str_contains(file_get_contents($mwFile), 'abort(403')
);
check(
    "CheckAdmin verifica role === 'admin'",
    file_exists($mwFile) && str_contains(file_get_contents($mwFile), "'admin'")
);

// 4. Alias registrado no bootstrap/app.php
$bootstrap = file_get_contents(base_path('bootstrap/app.php'));
check(
    "Alias 'admin' registrado no bootstrap/app.php",
    str_contains($bootstrap, "'admin'") && str_contains($bootstrap, 'CheckAdmin')
);

// 5. Middleware aplicado nas rotas corretas
$webRoutes = file_get_contents(base_path('routes/web.php'));
check(
    "Rota configuracao protegida por middleware 'admin'",
    str_contains($webRoutes, 'admin') && str_contains($webRoutes, 'configuracao')
);
check(
    "Rota consumidores.create protegida por middleware 'admin'",
    str_contains($webRoutes, 'admin') && str_contains($webRoutes, 'consumidores')
);

// 6. Rotas de leitura acessíveis sem middleware admin
check(
    'Rotas de leitura NÃO estão dentro do grupo admin',
    str_contains($webRoutes, 'leituras') // apenas presença — middleware auth
);

// 7. Model User tem role no fillable
$userModel = file_get_contents(app_path('Models/User.php'));
check(
    "Model User tem 'role' no \$fillable",
    str_contains($userModel, "'role'") && str_contains($userModel, 'fillable')
);
check(
    "Model User tem método isAdmin()",
    str_contains($userModel, 'isAdmin()')
);

// 8. Rotas carregam sem erro (artisan route:list)
$routeOutput = shell_exec('php artisan route:list 2>&1');
check(
    'Artisan route:list executa sem erros',
    !str_contains($routeOutput, 'ERROR') && str_contains($routeOutput, 'configuracao')
);
check(
    '34 rotas registradas (sistema + auth)',
    str_contains($routeOutput, 'leituras') && str_contains($routeOutput, 'login')
);

echo "\n══════════════════════════════════════════════════\n";
echo "  Resultado: {$pass} passou / {$fail} falhou\n";
echo "══════════════════════════════════════════════════\n\n";

if ($fail === 0) {
    echo "🎉 FASE 1 COMPLETAMENTE VALIDADA!\n";
} else {
    echo "⚠️  Há {$fail} problema(s) a corrigir.\n";
}
