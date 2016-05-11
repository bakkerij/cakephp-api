<?php

namespace Api\Action;

class EditAction extends Action
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
    protected function _put()
    {
        $manager = $this->_fractalManager();
        $id = $this->_controller()->request->param('id');
        $table = $this->_table();
        $entity = $table = $table->get($id);

        $entity = $table->patchEntity($entity, $this->_request()->data());

        $table->save($entity);
        $resource = $this->getResourceItem($entity);
        $data = $manager->createData($resource)->toArray();
        $this->config('serialize', array_keys($data));
        $this->_controller()->set($data);
    }

    protected function _post()
    {
        $this->_put();
    }

}