<?php

namespace Bakkerij\Api\Action;

use Cake\Routing\RequestActionTrait;
use Crud\Traits\FindMethodTrait;

class ViewAction extends Action
{

    use FindMethodTrait;

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'enabled' => true,
        'scope' => 'entity',
        'findMethod' => 'all',
        'view' => null,
        'viewVar' => null,
        'serialize' => []
    ];

    /**
     * Execute the index action
     *
     * @param null $id
     */
    protected function _handle($id = null)
    {
        $subject = $this->_subject();
        $subject->set(['id' => $id]);

        $this->_findRecord($id, $subject);
        $this->_trigger('beforeRender', $subject);

        $result = $this->item($subject->entity, $this->_transformer());
        $data = $this->createData($result)->toArray();

        $this->_controller()->set($data);
    }

}