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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('country', 100);
            $table->string('city', 100);
            $table->string('post_code', 50);
            $table->string('street', 255);
            $table->timestamps();
            
            // Add indexes for search performance (limited length to avoid MySQL key length limit)
            $table->index('user_id');
            $table->index(['country'], 'addresses_country_index');
            $table->index(['city'], 'addresses_city_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
