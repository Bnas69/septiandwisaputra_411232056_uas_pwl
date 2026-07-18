<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $tables = [
            'audit_logs',
            'stock_transactions',
            'sales',
            'products',
            'sessions',
            'password_reset_tokens',
            'users',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        // ── users ──────────────────────────────────────────────
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->notNull();
            $table->string('username', 50)->notNull()->unique();
            $table->string('email', 100)->notNull()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255)->notNull();
            $table->enum('role', ['developer', 'owner', 'pegawai', 'user'])->default('user');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->unsignedTinyInteger('avatar')->default(1);
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->unsignedSmallInteger('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('role');
            $table->index('status');
        });

        // ── password_reset_tokens ──────────────────────────────
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 100)->primary();
            $table->string('token', 255);
            $table->timestamp('created_at')->nullable();
        });

        // ── sessions ───────────────────────────────────────────
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id', 125)->primary();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity');

            $table->index('last_activity');
        });

        // ── products ───────────────────────────────────────────
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_code', 30)->notNull()->unique();
            $table->string('product_name', 100)->notNull();
            $table->string('category', 50)->notNull();
            $table->decimal('price', 15, 2)->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('minimum_stock')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index('product_code');
            $table->index('category');
        });

        // ── sales ──────────────────────────────────────────────
        Schema::create('sales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('transaction_number', 30)->notNull()->unique();
            $table->date('transaction_date')->notNull();
            $table->string('merchant_code', 20)->notNull();
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('qty')->notNull();
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();

            $table->index('transaction_date');
            $table->index('merchant_code');
            $table->index('product_id');
        });

        // ── stock_transactions ─────────────────────────────────
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('stock_code', 30)->notNull()->unique();
            $table->date('stock_date')->notNull();
            $table->enum('type', ['in', 'out', 'adjustment'])->notNull()->default('in');
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('qty')->notNull();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();

            $table->index('stock_date');
            $table->index('type');
            $table->index('product_id');
        });

        // ── audit_logs ─────────────────────────────────────────
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('event', 50)->notNull();
            $table->string('description', 500)->nullable();
            $table->string('auditable_type', 100)->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->index('user_id');
            $table->index('event');
            $table->index(['auditable_type', 'auditable_id']);
        });

        // ── CHECK constraints ──────────────────────────────────
        DB::statement('
            ALTER TABLE `products`
            ADD CONSTRAINT `ck_products_price`
            CHECK (`price` >= 100 AND `price` <= 999999999)
        ');

        DB::statement('
            ALTER TABLE `products`
            ADD CONSTRAINT `ck_products_stock`
            CHECK (`stock` >= 0 AND `stock` <= 99999)
        ');

        DB::statement('
            ALTER TABLE `products`
            ADD CONSTRAINT `ck_products_minimum_stock`
            CHECK (`minimum_stock` >= 1 AND `minimum_stock` <= 1000)
        ');

        DB::statement('
            ALTER TABLE `sales`
            ADD CONSTRAINT `ck_sales_qty`
            CHECK (`qty` >= 1 AND `qty` <= 9999)
        ');

        DB::statement('
            ALTER TABLE `sales`
            ADD CONSTRAINT `ck_sales_price`
            CHECK (`price` >= 100 AND `price` <= 999999999)
        ');

        DB::statement('
            ALTER TABLE `sales`
            ADD CONSTRAINT `ck_sales_subtotal`
            CHECK (`subtotal` >= 1)
        ');

        DB::statement('
            ALTER TABLE `sales`
            ADD CONSTRAINT `ck_sales_grand_total`
            CHECK (`grand_total` >= 1)
        ');

        DB::statement('
            ALTER TABLE `stock_transactions`
            ADD CONSTRAINT `ck_stock_transactions_qty`
            CHECK (`qty` >= 1 AND `qty` <= 99999)
        ');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $tables = [
            'audit_logs',
            'stock_transactions',
            'sales',
            'products',
            'sessions',
            'password_reset_tokens',
            'users',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        // Restore original Laravel base schema
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code', 20)->unique();
            $table->string('product_name');
            $table->string('category');
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('minimum_stock')->default(10);
            $table->timestamps();
        });

        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 30)->unique();
            $table->date('transaction_date');
            $table->string('merchant_code', 20);
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('qty')->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->timestamps();

            $table->index('transaction_date');
            $table->index('merchant_code');
        });

        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('stock_code', 20)->unique();
            $table->date('stock_date');
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('qty')->default(0);
            $table->timestamps();

            $table->index('stock_date');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->nullOnDelete();
            $table->string('event')->index();
            $table->text('description')->nullable();
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }
};
