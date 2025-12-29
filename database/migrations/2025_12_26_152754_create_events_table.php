<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up()
{
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->text('description');
        $table->dateTime('start_date');
        $table->dateTime('end_date');
        $table->string('place');
        // Ceci devrait être dans la migration
$table->decimal('price', 10, 2)->default(0); // ⬅️ 10 chiffres au total, 2 décimales
        $table->boolean('is_free')->default(true);
        $table->integer('capacity');
        $table->integer('available_spaces');
        $table->string('image')->nullable();
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        
        // MODIFIE CETTE LIGNE :
        $table->unsignedBigInteger('created_by');
        $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });
}

    public function down()
    {
        Schema::dropIfExists('events');
    }
};
