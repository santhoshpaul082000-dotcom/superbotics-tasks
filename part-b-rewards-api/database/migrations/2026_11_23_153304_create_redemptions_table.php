
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redemptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('category_id')->constrained();

            $table->unsignedInteger('points_spent');

            $table->timestamps();

            // Helps filter reports by date and category
            $table->index(
                ['created_at', 'category_id'],
                'idx_redemption_report'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redemptions');
    }
};
