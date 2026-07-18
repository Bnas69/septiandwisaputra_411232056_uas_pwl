<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `products` MODIFY COLUMN `price` DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `sales` MODIFY COLUMN `price` DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `sales` MODIFY COLUMN `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `sales` MODIFY COLUMN `grand_total` DECIMAL(15,2) NOT NULL DEFAULT 0');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `products` MODIFY COLUMN `price` INT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `sales` MODIFY COLUMN `price` INT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `sales` MODIFY COLUMN `subtotal` INT UNSIGNED NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE `sales` MODIFY COLUMN `grand_total` INT UNSIGNED NOT NULL DEFAULT 0');
    }
};
