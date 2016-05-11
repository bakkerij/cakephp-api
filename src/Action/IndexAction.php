<?php

namespace Api\Action;

use Cake\Routing\DispatcherFactory;
use Cake\Routing\Router;

class IndexAction extends Action
{

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'enabled' => true,
        'findMethod' => 'all',
    ];

    /**
     * Execute the index action
     *
     * @return void
     */
    protected function _execute()
    {
        $manager = $this->_fractalManager();

        $query = $this->_table()->find($this->findMethod());

        if ($this->_api()->config('pagination')) {
            $query = $this->_controller()->paginate($query);
        }

        $resource = $this->getResourceCollection($query);

        $data = $manager->createData($resource)->toArray();
        $this->_controller()->set($data);
    }

}