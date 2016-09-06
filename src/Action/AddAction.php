<?php

namespace Bakkerij\Api\Action;

use Cake\Core\Exception\Exception;

class AddAction extends Action
{

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'enabled' => true,
        'saveMethod' => 'save',
        'saveOptions' => []
    ];

    /**
     * Execute the index action
     *
     * @return void
     */
    protected function _post()
    {
        $subject = $this->_subject([
            'entity' => $this->_entity($this->_api()->getRequestData(), $this->config('saveOptions')),
            'saveMethod' => $this->config('saveMethod'),
            'saveOptions' => $this->config('saveOptions')
        ]);

        $this->_trigger('beforeSave', $subject);
        $saveCallback = [$this->_table(), $subject->saveMethod];

        if (call_user_func($saveCallback, $subject->entity, $subject->saveOptions)) {
            return $this->_success($subject);
        } else {
            return $this->_error($subject);
        }
    }

    protected function _success()
    {
        $this->statusCode(201);

        $this->_trigger('afterSave', $subject);

        $resource = $this->item($subject->entity, $this->_transformer());
        $data = $this->_fractal()->createData($resource)->toArray();
        $this->_controller()->set($data);
    }

    protected function _error() 
    {
        $this->statusCode(400);

        $this->_trigger('afterSave', $subject);

        $this->_controller()->set('errors', $subject->entity->errors());
    }

    protected function _put()
    {
        return $this->_post();
    }

    protected function _get()
    {
        throw new Exception("The method 'GET' isn't allowed", 405);
    }

}
