<?php
namespace Database\Seeders;

use App\Models\CategoryReference;
use App\Models\Element;
use App\Models\ComponentReference;
use App\Models\FootprintReference;
use App\Models\GroupReference;

use App\Models\LibraryRefReference;
use App\Models\PartStatusReference;
use App\Models\TempRangeReference;
use App\Models\ManufacturerReference;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ElementsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $db_param["name"] = base_path() . '/tmp/bla_ad_lib_main.accdb';
        if(!file_exists($db_param["name"])) {
            die('Error finding access database');
        }

        $sql = 'SELECT
                "ID",
                "Library Ref",
                "Footprint Ref 1",
                "Footprint Ref 2",
                "Footprint Ref 3",
                "Comment",
                "ID склад",
                "Stock",
                "Part Status",
                "Part Number",
                "Components",
                "CT",
                "Groups",
                "Categories",
                "Part Number",
                "Manufacturer",
                "HelpURL",
                "TempRange",
                "Name in stock",
                "TU"
                FROM components';
        $components_main = collect(access_result_array_all($db_param, $sql));

        $db_param["name"] = base_path() . '/tmp/bla_ad_lib_no_bom.accdb';
        if(!file_exists($db_param["name"])) {
            die('Error finding access database');
        }

        $sql = 'SELECT
                "ID",
                "Library Ref",
                "Footprint Ref 1",
                "Footprint Ref 2",
                "Footprint Ref 3",
                "Comment",
                "ID склад",
                "Stock",
                "Part Status",
                "Components",
                "Groups",
                "Categories"
                FROM components';
        $components_no_bom = collect(access_result_array_all($db_param, $sql));

        $components = $components_main->merge($components_no_bom);

        // references
        $components_ref = ComponentReference::all();
        $groups_ref = GroupReference::all();
        $categories_ref = CategoryReference::all();

        $part_statuses_ref = PartStatusReference::all();
        $temp_ranges_ref = TempRangeReference::all();
        $library_refs = LibraryRefReference::all();
        $manufacturers_ref = ManufacturerReference::all();
        $footprints_ref = FootprintReference::all();

        $components->each(function ($item, $key) use
            ($components_ref, $groups_ref, $categories_ref, $part_statuses_ref, $manufacturers_ref,
            $temp_ranges_ref,
            $library_refs,
            $footprints_ref)
        {
            $element = new Element();

            $element->id = $item['ID'];
            $element->component_ref_id = $components_ref->firstWhere('title', $item['Components'])->id;

            if ($item['Groups'] ) {
                $element->group_ref_id = $groups_ref->where('title', $item['Groups'])->firstWhere('component_ref_id', $element->component_ref_id)->id;
            }
            if ($item['Categories'] ) {
                $element->category_ref_id = $categories_ref->where('title', $item['Categories'])->firstWhere('group_ref_id', $element->group_ref_id)->id;
            }
            if( $item['Part Status'] ) {
                $element->part_status_id = $part_statuses_ref->firstWhere('title', $item['Part Status'])->id; // "Активный"
            }

            if($item['Library Ref']) {
                $library_ref = $library_refs->firstWhere('title', '=', $item['Library Ref']);
                if (is_null($library_ref)) {
                    $library_ref = new LibraryRefReference();
                    $library_ref->title = $item['Library Ref'];
                    $library_ref->save();
                    $library_refs->push($library_ref);
                }
                $element->library_ref_id = $library_ref->id;
            }
            else {
                $element->library_ref_id = null;
            }

            if($item['Footprint Ref 1']) {
                $ref = $footprints_ref->firstWhere('title', '=', $item['Footprint Ref 1']);
                if (is_null($ref)) {
                    $ref = new FootprintReference();
                    $ref->title = $item['Footprint Ref 1'];
                    $ref->save();
                    $footprints_ref->push($ref);
                }
                $element->footprint_ref1_id = $ref->id;
            }
            else {
                $element->footprint_ref1_id = null;
            }
            if($item['Footprint Ref 2']) {
                $ref = $footprints_ref->firstWhere('title', '=', $item['Footprint Ref 2']);
                if (is_null($ref)) {
                    $ref = new FootprintReference();
                    $ref->title = $item['Footprint Ref 2'];
                    $ref->save();
                    $footprints_ref->push($ref);
                }
                $element->footprint_ref2_id = $ref->id;
            }
            else {
                $element->footprint_ref2_id = null;
            }
            if($item['Footprint Ref 3']) {
                $ref = $footprints_ref->firstWhere('title', '=', $item['Footprint Ref 3']);
                if (is_null($ref)) {
                    $ref = new FootprintReference();
                    $ref->title = $item['Footprint Ref 3'];
                    $ref->save();
                    $footprints_ref->push($ref);
                }
                $element->footprint_ref3_id = $ref->id;
            }
            else {
                $element->footprint_ref3_id = null;
            }

            $element->comment = $item['Comment'];

            $element->stock_id = intval($item['ID склад']);
            $element->stock_count = intval($item['Stock']);

            // CT and others are missing for items in bla_ad_lib_no_bom.accdb
            if (!isset($item['CT'])) {
                $element->deleted_at = Carbon::now();
            }
            else {
                $element->help_url = $item['HelpURL'];
                $element->part_number = $item['Part Number'];
                if($item['TempRange'] != "" && $item['TempRange'] != "n/a") {
                    $temp_range = $temp_ranges_ref->firstWhere('description', '=', $item['TempRange']); //"от -40 до +125"
                    if (is_null($temp_range)) {
                        $temp_range = new TempRangeReference();
                        $temp_range->title = '';
                        $temp_range->description = $item['TempRange'];
                        $temp_range->min = intval(explode(' ', $temp_range->description)[1]);
                        $temp_range->max = intval(explode(' ', $temp_range->description)[3]);
                        $temp_range->save();
                        $temp_ranges_ref->push($temp_range);
                    }
                    $element->temp_range_id = $temp_range->id;
                }
                else {
                    $element->temp_range_id = null;
                }

                $manufacturer = $manufacturers_ref->firstWhere('title', $item['Manufacturer']); //TI, США
                if (is_null($manufacturer)) {
                    $manufacturer = new ManufacturerReference();
                    $manufacturer->title = $item['Manufacturer'];
                    $manufacturer->save();
                    $manufacturers_ref->push($manufacturer);
                }
                $element->manufacturer_id = $manufacturer->id;
            }

            $element->save();

        });

        DB::statement("ALTER TABLE elements AUTO_INCREMENT=10000;");
    }
}
