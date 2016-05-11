<?php

namespace Api\Action;

class DeleteAction extends Action
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

        $this->_controller()->set('data', 'execute delete');

    }

}