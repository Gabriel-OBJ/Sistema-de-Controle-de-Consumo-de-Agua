<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "╔══════════════════════════════════════════════════╗\n";
echo "║    FASE 1 — CRIAÇÃO DE USUÁRIOS DE TESTE         ║\n";
echo "╚══════════════════════════════════════════════════╝\n\n";

// Remove usuários de teste anteriores
User::where('email', 'admin@teste.com')->orWhere('email', 'leiturista@teste.com')->forceDelete();

// Cria admin
$admin = User::create([
    'name'     => 'Admin Teste',
    'email'    => 'admin@teste.com',
    'password' => Hash::make('password123'),
    'role'     => 'admin',
]);
echo "  ✅ Admin criado      → email: admin@teste.com | senha: password123 | role: {$admin->role}\n";
echo "     isAdmin(): " . ($admin->isAdmin() ? 'TRUE ✅' : 'FALSE ❌') . "\n\n";

// Cria leiturista
$leiturista = User::create([
    'name'     => 'Leiturista Teste',
    'email'    => 'leiturista@teste.com',
    'password' => Hash::make('password123'),
    'role'     => 'leiturista',
]);
echo "  ✅ Leiturista criado → email: leiturista@teste.com | senha: password123 | role: {$leiturista->role}\n";
echo "     isAdmin(): "      . ($leiturista->isAdmin()      ? 'TRUE ❌ (errado)' : 'FALSE ✅') . "\n";
echo "     isLeiturista(): " . ($leiturista->isLeiturista() ? 'TRUE ✅'          : 'FALSE ❌') . "\n\n";

echo "Usuários prontos para teste no browser em http://localhost:8000/login\n";
