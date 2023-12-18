<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateElementsView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR REPLACE VIEW `elements_view` AS 
        SELECT 
            `id`,
            (SELECT `title` FROM `components_reference` WHERE `id` = `elements`.`component_ref_id`) AS `component_ref`,
            (SELECT `title` FROM `groups_reference` WHERE `id` = `elements`.`group_ref_id`) AS `group_ref`,
            (SELECT `title` FROM `categories_reference` WHERE `id` = `elements`.`category_ref_id`) AS `category_ref`,
            (SELECT `title` FROM `manufacturers_reference` WHERE `id` = `elements`.`manufacturer_id`) AS `manufacturer_ref`,
            (SELECT `title` FROM `part_statuses_reference` WHERE `id` = `elements`.`part_status_id`) AS `part_status`,
            (SELECT `title` FROM `library_ref_reference` WHERE `id` = `elements`.`library_ref_id`) AS `library_ref`,
            `part_number`,
            (SELECT `title` FROM `footprints_reference` WHERE `id` = `elements`.`footprint_ref1_id`) AS `footprint_ref1`,
            (SELECT `title` FROM `footprints_reference` WHERE `id` = `elements`.`footprint_ref2_id`) AS `footprint_ref2`,
            (SELECT `title` FROM `footprints_reference` WHERE `id` = `elements`.`footprint_ref3_id`) AS `footprint_ref3`,
            (SELECT `description` FROM `temp_ranges_reference` WHERE `id` = `elements`.`temp_range_id`) AS `temp_range`,
            `comment`,
            `help_url`,
            `stock_barcode`,
            `stock_title`,
            `stock_count`
        FROM `elements`
        WHERE 
            deleted_at IS NULL
            AND component_ref_id != 1");
    }
}
