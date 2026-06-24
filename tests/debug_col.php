<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$col = DB::select("
    SELECT COLUMN_TYPE, COLUMN_DEFAULT, IS_NULLABLE
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME   = 'users'
      AND COLUMN_NAME  = 'role'
")[0];

var_dump([
    'COLUMN_TYPE'    => $col->COLUMN_TYPE,
    'COLUMN_DEFAULT' => $col->COLUMN_DEFAULT,
    'IS_NULLABLE'    => $col->IS_NULLABLE,
]);
