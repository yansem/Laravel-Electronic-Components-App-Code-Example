<?php

namespace App\Services\Program;

use App\Services\ProgramService;

class CoreService extends ProgramService
{
    public function __construct(){
        $this->setUrlHost(config('app.core_server'));
    }

    /**
     * Получить меню
     * @return array
     */
    public function getMenu(): array
    {
        $this->setApiUrl('/api/v1/menu');
        $this->setMethod('GET');
        return $this->request();
    }
}
