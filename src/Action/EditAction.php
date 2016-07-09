<?php

namespace Bakkerij\Api\Action;

use Crud\Traits\FindMethodTrait;

class EditAction extends Action
{

    use FindMethodTrait;

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'enabled' => true,
        'findMethod' => 'all',
        'saveMethod' => 'save',
        'saveOptions' => []
    ];

    /**
     * Execute the index action
     *
     * @return void
     */
    protected function _put($id = null)
    {
        $manager = $this->_fractal();

        $subject = $this->_subject([
            'id' => $id,
            'saveMethod' => $this->config('saveMethod'),
            'saveOptions' => $this->config('saveOptions')
        ]);

        $entity = $this->_table()->patchEntity(
            $this->_findRecord($id, $subject),
            $this->_api()->getRequestData(),
            $this->config('saveOptions')
        );

        $this->_trigger('beforeSave', $subject);
        $saveCallback = [$this->_table(), $subject->saveMethod];

        if (call_user_func($saveCallback, $entity, $subject->saveOptions)) {
            $this->statusCode(200);

            $resource = $this->item($subject->entity, $this->_transformer());
            $data = $manager->createData($resource)->toArray();
            $this->_controller()->set($data);
        } else {
            $this->statusCode(400);

            $this->_controller()->set('errors', $subject->entity->errors());
        }
    }

    protected function _post()
    {
        $this->_put();
    }

}