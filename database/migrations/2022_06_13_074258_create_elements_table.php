<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Console\Output\ConsoleOutput;

class CreateElementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elements', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_bin';

            $table->id();

            // component classification
            $table->foreignId('component_ref_id')->default(1)->constrained('components_reference'); // 'Не распределенные'
            $table->foreignId('group_ref_id')->nullable()->default(null)->constrained('groups_reference');
            $table->foreignId('category_ref_id')->nullable()->default(null)->constrained('categories_reference');

            // component info
            $table->string('part_number')->nullable();            
            $table->foreignId('part_status_id')->default(1)->constrained('part_statuses_reference');
            $table->foreignId('library_ref_id')->nullable()->constrained('library_ref_reference');
            $table->foreignId('footprint_ref1_id')->nullable()->constrained('footprints_reference');
            $table->foreignId('footprint_ref2_id')->nullable()->constrained('footprints_reference');
            $table->foreignId('footprint_ref3_id')->nullable()->constrained('footprints_reference');
            $table->foreignId('temp_range_id')->nullable()->constrained('temp_ranges_reference');
            $table->string('comment')->nullable();
            $table->string('description')->nullable();
            $table->string('help_url')->nullable();
            $table->foreignId('manufacturer_id')->nullable()->constrained('manufacturers_reference');

            $table->integer('part_count')->unsigned()->nullable()->default(1);
            $table->integer('count')->unsigned()->nullable()->default(0);

            // stock values
            $table->string('stock_title')->nullable();
            $table->bigInteger('stock_id')->unsigned()->nullable();
            $table->bigInteger('stock_barcode')->unsigned()->nullable();
            $table->bigInteger('stock_count')->unsigned()->nullable();
            $table->string('stock_count_type', 30)->nullable();
            $table->bigInteger('stock_part_count')->unsigned()->nullable();
            $table->string('stock_part_count_type', 30)->nullable();
            $table->string('stock_link', 200)->nullable()->default('');

            // service fields
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
        Schema::dropIfExists('elements');
    }
}
