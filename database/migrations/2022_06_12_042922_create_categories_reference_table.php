<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesReferenceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categories_reference', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_ref_id')->nullable()->default(null)->constrained('components_reference');
            $table->foreignId('group_ref_id')->nullable()->default(null)->constrained('groups_reference');
            $table->string('title');
            $table->tinyInteger('hidden')->default(0);
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories_reference');
    }
}
