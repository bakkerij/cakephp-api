<?php
/**
 * Bakkerij (https://github.com/bakkerij)
 * Copyright (c) https://github.com/bakkerij
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) https://github.com/bakkerij
 * @link          https://github.com/bakkerij Bakkerij Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Bakkerij\Api\Model\Behavior;

use Cake\Database\Query;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\Routing\Router;

/**
 * CursorHelper behavior
 */
class CursorHelperBehavior extends Behavior
{

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Request instance.
     *
     * @var Request|null
     */
    protected $request;

    public function __construct(Table $table, array $config)
    {
        parent::__construct($table, $config);

        $this->request = Router::getRequest();
    }

    /**
     * beforeFind event.
     *
     * @param Event $event
     * @param Query $query
     * @param $options
     * @param $primary
     * @return void
     */
    public function beforeFind(Event $event, Query $query, $options, $primary)
    {
        return $query->find('cursor');
    }

    public function findCursor(Query $query)
    {
        $current = $this->request->query('cursor');
        $limit = $this->request->query('limit') ?: 10;

        if ($current) {
            $query->where([
                'id >' => $current
            ]);
        }

        $query->limit($limit);

        return $query;
    }
}
