<?php

namespace App\Services\Program;

use App\Services\ProgramService;

class StockService extends ProgramService
{
    public function __construct(){
        $this->setUrlHost(config('app.stock_server'));
    }
 
    /**
     * Получить данные со склада по списку категорий
     * @param array $categories
     * @return array
     * <pre>
     *  "error" => string описание ошибки (в случае ошибки),
     * </pre>
     */
    public function getStockPositions(array $categories): array
    {
        $params = ['act' => 'get_positions_by_categories', 'categories' => $categories];       
        $this->setParams($params);
        $this->setApiUrl('/api/ware/');
        $this->setMethod('GET');
        $this->setMerge(false);
        $result = $this->request();
        return $result;
    }

    /**
     * Получить данные со склада по артикулу
     * @param array $categories
     * @return array
     * <pre>
     *  "error" => string описание ошибки (в случае ошибки),
     * </pre>
     */
    public function getStockPosition(int $barcode): array
    {
        $params = [
            'act' => 'get_ware_info_by_barcode',
            'barcode' => $barcode,
            'title' => true,
            'count' => true
        ];
        $this->setParams($params);
        $this->setApiUrl('/api/ware/');
        $this->setMethod('GET');
        $this->setMerge(false);
        $result = $this->request();
        return $result;
    }

    /**
     * Получить список категорий
     * @return array
     * <pre>
     *  "error" => string описание ошибки (в случае ошибки),
     * </pre>
     */
    public function getStockCategories(){
        $params = ['act' => 'get_categories'];       
        $this->setParams($params);
        $this->setApiUrl('/api/ware');
        $this->setMethod('GET');
        $this->setMerge(false);
        $result = $this->request();
        return $result;
    }
}
