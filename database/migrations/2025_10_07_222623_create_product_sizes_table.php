<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create product_sizes table
        Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            
            // Size Details
            $table->string('size_name', 100)->nullable()->comment('e.g. A4 Standard, Custom Box');
            $table->string('ukuran_potongan', 100)->comment('e.g. 21 x 29.7 cm');
            $table->string('ukuran_plano', 100)->nullable()->comment('e.g. 65 x 100 cm');
            
            // Calculation Fields
            $table->decimal('width', 10, 2)->nullable()->comment('Width in cm');
            $table->decimal('height', 10, 2)->nullable()->comment('Height in cm');
            $table->decimal('plano_width', 10, 2)->nullable()->comment('Plano width in cm');
            $table->decimal('plano_height', 10, 2)->nullable()->comment('Plano height in cm');
            $table->integer('quantity_per_plano')->nullable()->comment('How many pieces per plano');
            $table->decimal('waste_percentage', 5, 2)->nullable()->comment('Waste/scrap percentage');
            
            // Metadata
            $table->text('notes')->nullable();
            $table->boolean('is_default')->default(false);
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index('product_id', 'idx_product_id');
            $table->index('is_default', 'idx_is_default');
        });

        // Migrate existing data from products table
        DB::statement("
            INSERT INTO product_sizes (product_id, size_name, ukuran_potongan, ukuran_plano, is_default, sort_order, created_at, updated_at)
            SELECT 
                id as product_id,
                'Default' as size_name,
                COALESCE(ukuran, '-') as ukuran_potongan,
                ukuran_plano_1 as ukuran_plano,
                TRUE as is_default,
                0 as sort_order,
                NOW() as created_at,
                NOW() as updated_at
            FROM products
        ");

        // Add second size if exists
        DB::statement("
            INSERT INTO product_sizes (product_id, size_name, ukuran_potongan, ukuran_plano, is_default, sort_order, created_at, updated_at)
            SELECT 
                id as product_id,
                'Variant 2' as size_name,
                ukuran_potongan_2 as ukuran_potongan,
                ukuran_plano_2 as ukuran_plano,
                FALSE as is_default,
                1 as sort_order,
                NOW() as created_at,
                NOW() as updated_at
            FROM products
            WHERE ukuran_potongan_2 IS NOT NULL AND ukuran_potongan_2 != ''
        ");

        // Drop old columns from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'ukuran',
                'ukuran_potongan_1',
                'ukuran_plano_1',
                'ukuran_potongan_2',
                'ukuran_plano_2'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore old columns to products table
        Schema::table('products', function (Blueprint $table) {
            $table->string('ukuran')->nullable();
            $table->string('ukuran_potongan_1')->nullable();
            $table->string('ukuran_plano_1')->nullable();
            $table->string('ukuran_potongan_2')->nullable();
            $table->string('ukuran_plano_2')->nullable();
        });

        // Restore data from product_sizes (only default sizes)
        // Note: PostgreSQL uses UPDATE...FROM instead of INNER JOIN
        DB::statement("
            UPDATE products p
            SET 
                ukuran = ps.ukuran_potongan,
                ukuran_plano_1 = ps.ukuran_plano
            FROM product_sizes ps
            WHERE p.id = ps.product_id AND ps.is_default = TRUE
        ");

        Schema::dropIfExists('product_sizes');
    }
};
