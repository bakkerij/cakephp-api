<?php

namespace Api\Action;

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
    ];

    /**
     * Execute the index action
     *
     * @return void
     */
    protected function _post()
    {
        $manager = $this->_fractalManager();
        $table = $this->_table();
        $entity = $this->_entity($this->_request()->data());

        $table->save($entity);
        $resource = $this->getResourceItem($entity);
        $data = $manager->createData($resource)->toArray();
        $this->config('serialize', array_keys($data));
        $this->_controller()->set($data);
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