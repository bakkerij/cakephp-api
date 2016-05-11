<?php

namespace Api\Action;

use Cake\Routing\RequestActionTrait;

class ViewAction extends Action
{

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'enabled' => true,
    ];

    /**
     * Execute the index action
     *
     * @return void
     */
    protected function _execute()
    {
        $manager = $this->_fractalManager();

        $id = $this->_controller()->request->param('id');

        $model = $this->_table();
        $entity = $model->get($id);

        $resource = $this->getResourceItem($entity);
        $data = $manager->createData($resource)->toArray();
        $this->config('serialize', array_keys($data));
        $this->_controller()->set($data);
    }

}