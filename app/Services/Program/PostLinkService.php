<?php

namespace App\Services\Program;

use App\Services\ProgramService;
class PostLinkService extends ProgramService
{
    public function __construct(){
        $this->setUrlHost(config('app.spo_protocol','https').'://'.config('app.spo_domen'));
    }
    /**
     * Отправить сообщение
     * @param array $params параметры сообщения
     *  <pre>
     *  "user_id" => int Индентификатор пользователя ,
     *  "header" => string Заголовок сообщения ,
     *  "text" => text Описание сообщения ,
     * </pre>
     * @return array
     * <pre>
     *  "error" => string описание ошибки (в случае ошибки),
     * </pre>
     */
    public function send($params=[],$test=true){
        $this->setApiUrl('/api/');
        $data = array(
            'info'=>'notify',
            'uid' =>$params['user_id'],
            'msg_header' => $params['header'],
            'msg_text' => $params['text']
            );
        $this->setParams($data);
        $this->setMethod('POST');
        $this->setMerge(false);
        return $this->request();
    }
}
