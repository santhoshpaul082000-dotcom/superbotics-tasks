
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points_ledger', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained();

            $table->foreignId('redemption_id')
                ->nullable()
                ->constrained();

            $table->enum('type', ['earn', 'spend']);

            $table->unsignedInteger('points');

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_ledger');
    }
};
