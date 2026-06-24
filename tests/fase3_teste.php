<?php
echo "╔══════════════════════════════════════════════════╗\n";
echo "║   FASE 3 — SEGURANÇA BÁSICA (TESTES)             ║\n";
echo "╚══════════════════════════════════════════════════╝\n\n";

$pass = 0; $fail = 0;

function check(string $label, bool $result, string $detail = ''): void {
    global $pass, $fail;
    if ($result) { echo "  ✅ PASSOU  $label" . ($detail ? " → $detail" : '') . "\n"; $pass++; }
    else         { echo "  ❌ FALHOU  $label" . ($detail ? " → $detail" : '') . "\n"; $fail++; }
}

$baseDir = __DIR__ . '/..';

// 1. Verificando .env (APP_DEBUG=false)
$envContent = file_exists("$baseDir/.env") ? file_get_contents("$baseDir/.env") : '';
check('APP_DEBUG deve estar desligado no .env', str_contains($envContent, 'APP_DEBUG=false'));

// 2. Verificando @csrf nos formulários POST/PUT/DELETE
$viewsDir = "$baseDir/resources/views";
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$missingCsrf = [];

foreach ($files as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $content = file_get_contents($file->getPathname());
        
        // Se tem <form method="POST" mas não tem @csrf
        if (preg_match_all('/<form[^>]*method=[\'"]?(POST|PUT|DELETE)[\'"]?[^>]*>/i', $content)) {
            if (!str_contains($content, '@csrf')) {
                $missingCsrf[] = $file->getFilename();
            }
        }
    }
}
check('Todos os formulários POST possuem @csrf', count($missingCsrf) === 0, count($missingCsrf) > 0 ? "Faltando em: " . implode(', ', $missingCsrf) : '');

// 3. Verificando vulnerabilidade XSS ({!! !!)
$hasXss = false;
foreach ($files as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $content = file_get_contents($file->getPathname());
        if (str_contains($content, '{!!')) {
            $hasXss = true;
            break;
        }
    }
}
check('Nenhum output sem escape (XSS {!! !!}) detectado', !$hasXss);

// 4. Verificando vulnerabilidade de SQL Injection
$appDir = "$baseDir/app";
$phpFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($appDir));
$hasSqlInjection = false;

foreach ($phpFiles as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.php')) {
        $content = file_get_contents($file->getPathname());
        if (str_contains($content, 'DB::raw') || str_contains($content, 'whereRaw') || str_contains($content, 'orderByRaw')) {
            $hasSqlInjection = true;
            break;
        }
    }
}
check('Nenhuma query bruta detectada (SQL Injection mitigado)', !$hasSqlInjection);

echo "\n══════════════════════════════════════════════════\n";
echo "  Resultado: {$pass} passou / {$fail} falhou\n";
echo "══════════════════════════════════════════════════\n\n";

if ($fail === 0) echo "🎉 FASE 3 COMPLETAMENTE VALIDADA!\n";
else            echo "⚠️  Há {$fail} problema(s) a corrigir.\n";
