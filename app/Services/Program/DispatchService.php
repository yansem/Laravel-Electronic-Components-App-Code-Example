<?php

namespace App\Services\Program;

use App\Services\ProgramService;

class DispatchService extends ProgramService
{
    public function __construct(){
        $this->setUrlHost(config('app.dispatch_server'));
    }
    /**
     * Получить информацию о записи диспетчеризации
     *
     * @param int $history_id идентификатор истории, на основании которой добавляется задача
     *
     * @return mixed $result - массив с информацией о записи истории диспетчеризации
     * <pre>
     * $result[
     *  'error' => string описание ошибки (в случае ошибки)
     *  'item_id' => int идентификатор записи диспетчеризации
     *  'mark' => string текст замечания записи истории, на основании которого добавляется задача
     *  'mark_dep' => int идентификатор участка для исправления замечания, указанный при добавлении записи истории диспетчеризации
     *  'item_number' => string номер изделия записи в диспетчеризации
     * ]
     * </pre>
     */
    public function getDispatchInfo($historyId){
        $this->setApiUrl('/dispatch/api/get_dispatch_info_by_history_id');
        $params = ['history_id'=>$historyId];
        $this->setParams($params);
        $this->setMethod('GET');
        $this->setMerge(false);
        return $this->request();
    }
    /**
     * Добавить информацию о задаче в диспетчеризацию
     * @param int $history_id идентификатор записи истории диспетчеризации
     * @param int $task_id идентификатор задачи
     * <pre>
     *  "error" => string описание ошибки (в случае ошибки)
     * </pre> 
     */
    public function setDispatchHistoryTask($historyId,$taskId){
        $this->setApiUrl('/dispatch/api/set_dispatch_history_task');
        $params = [
            'history_id'=>$historyId,
            'task_id'=>$taskId
        ];
        $this->setParams($params);
        $this->setMethod('GET');
        $this->setMerge(false);
        return $this->request();
    }
    /**
     * Обнулить данные задачи в диспетчеризации (например при отмене или отклонении задачи), чтобы можно было добавить новую
     * @param int $task_id идентификатор задачи
     * <pre>
     *  "error" => string описание ошибки (в случае ошибки)
     * </pre> 
     */
    public function cancelDispatchHistoryTask($taskId){
        $this->setApiUrl('/dispatch/api/cancel_dispatch_history_task');
        $params = [
            'task_id'=>$taskId
        ];
        $this->setParams($params);
        $this->setMethod('GET');
        $this->setMerge(false);
        return $this->request();
    }
    /**
     * Получить информацию о истории диспетчеризации
     * @param $task_id - идентификатор задачи
     * @return array
     * <pre>
     *  "error" => string описание ошибки (в случае ошибки),
     *  "info" => array массив выборки данных из таблицы истории диспетчеризации
     * </pre>
     */
    public function getTaskDispatchInfo($taskId){
        $this->setApiUrl('/dispatch/api/get_dispatch_info_by_task_id');
        $params = [
            'task_id'=>$taskId
        ];
        $this->setParams($params);
        $this->setMethod('GET');
        $this->setMerge(false);
        return $this->request();
    }
    /**
     * Добавить ометку ОТК (закрыть задачу выполнено)
     * @param int $historyId идентификатор записи истории диспетчеризации
     * @return array
     * <pre>
     *  "error" => string описание ошибки (в случае ошибки),
     * </pre>
     */
    public function setOTKMarkCheck($historyId){
        $this->setApiUrl('/dispatch/api/set_otk_mark_check');
        
        $params = [
            'history_id'=>$historyId,
            'uid'=>\Spo::user()->id
        ];
        $this->setParams($params);
        $this->setMethod('GET');
        $this->setMerge(false);
        return $this->request();
    }
    public function checkAccessUserDispatch(){
        $accessUser = [508,561,767];
        $accessDeps = [49];
        return (in_array(\Spo::user()->id,$accessUser) || in_array(\Spo::user()->dep_id,$accessDeps));
    }
}