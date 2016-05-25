<?php

namespace Api\Action;

use App\Transformer\CategoryTransformer;
use Crud\Traits\FindMethodTrait;
use Crud\Traits\SerializeTrait;
use Crud\Traits\ViewTrait;
use Crud\Traits\ViewVarTrait;

class IndexAction extends Action
{
    use FindMethodTrait;
    use SerializeTrait;
    use ViewTrait;
    use ViewVarTrait;
    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [
        'enabled' => true,
        'scope' => 'table',
        'findMethod' => 'all',
        'serialize' => [],
    ];

    /**
     * Generic handler for all HTTP verbs
     *
     * @return void
     */
    protected function _handle()
    {
        $query = $this->_table()->find($this->findMethod());
        $subject = $this->_subject(['success' => true, 'query' => $query]);

        $this->_trigger('beforePaginate', $subject);
        $items = $this->_controller()->paginate($subject->query);
        $subject->set(['entities' => $items]);

        $this->_trigger('afterPaginate', $subject);
        $this->_trigger('beforeRender', $subject);

        $result = $this->collection($subject->entities, $this->_transformer());
        $data = $this->createData($result)->toArray();
        $this->_controller()->set($data);
    }

}