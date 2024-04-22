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
        Schema::create('stuff_stocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("stuff_id");
            $table->integer("total availeble");
            $table->integer("totol_dafec");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stuff_stocks');
    }
};
