<?php
/**
 * CakePlugins (http://cakeplugins.org)
 * Copyright (c) http://cakeplugins.org
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) http://cakeplugins.org
 * @link          http://cakeplugins.org CakePlugins Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Api\Controller\Component;

use Api\Action\Action;
use Api\Core\FractalTrait;
use Api\Cursor\CakeCursorAdapter;
use Api\Pagination\CakePaginatorAdapter;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\App;
use Cake\Core\Exception\Exception;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\ORM\ResultSet;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Serializer\DataArraySerializer;

/**
 * ApiBuilder component
 *
 * Helps building the API and do automatic stuff.
 */
class ApiBuilderComponent extends Component
{

    use FractalTrait;

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'serializer' => DataArraySerializer::class,
        'pagination' => true,
        'cursor' => false,
    ];

    /**
     * Current action.
     *
     * @var string
     */
    protected $_action;

    /**
     * List of Api action instances.
     *
     * @var array
     */
    protected $_actions;

    /**
     * Controller instance.
     *
     * @var \Cake\Controller\Controller
     */
    protected $_controller;

    /**
     * EventManager instance.
     *
     * @var \Cake\Event\EventManager
     */
    protected $_eventManager;

    /**
     * Request instance.
     *
     * @var Request
     */
    protected $_request;

    /**
     * Fractals Manager instance.
     *
     * @var Manager
     */
    protected $_fractalManager;

    /**
     * Constructor
     *
     * @param \Cake\Controller\ComponentRegistry $collection A ComponentCollection this component
     *   can use to lazy load its components.
     * @param array $config Array of configuration settings.
     */
    public function __construct(ComponentRegistry $collection, $config = [])
    {
        $config += ['actions' => [], 'listeners' => []];
        $config['actions'] = $this->normalizeArray($config['actions']);
        $config['listeners'] = $this->normalizeArray($config['listeners']);
        $this->_controller = $collection->getController();
        $this->_eventManager = $this->_controller->eventManager();
        parent::__construct($collection, $config);

        if (!$collection->has('RequestHandler')) {
            $this->_controller->loadComponent('RequestHandler');
        }

        if (!$collection->has('Paginator')) {
            $this->_controller->loadComponent('Paginator');
        }
    }

    /**
     * Normalize config array
     *
     * @param array $array List to normalize
     * @return array
     */
    public function normalizeArray(array $array)
    {
        $normal = [];
        foreach ($array as $action => $config) {
            if (is_string($config)) {
                $config = ['className' => $config];
            }
            if (is_int($action)) {
                list(, $action) = pluginSplit($config['className']);
            }
            $action = Inflector::variable($action);
            $normal[$action] = $config;
        }
        return $normal;
    }

    /**
     * BeforeFilter event.
     *
     * @param Event $event Event.
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        $this->_action = $this->_controller->request->action;
        $this->_request = $this->_controller->request;

        if (!isset($this->_controller->dispatchComponents)) {
            $this->_controller->dispatchComponents = [];
        }

        $this->_controller->dispatchComponents['ApiBuilder'] = true;
    }

    /**
     * Check if an Api action has been mapped.
     *
     * @param string $action If null, use the current action.
     * @return bool
     */
    public function isActionMapped($action = null)
    {
        if (!$action) {
            $action = $this->_action;
        }

        $action = Inflector::variable($action);
        $actionConfig = $this->config('actions.' . $action);
        if (!$actionConfig) {
            return false;
        }

        return $this->action($action)->config('enabled');
    }

    /**
     * Get an ApiAction object by the action name.
     *
     * @param string $name Action name.
     * @return Action
     */
    public function action($name = null)
    {
        if (!$name) {
            $name = $this->_action;
        }
        $name = Inflector::variable($name);
        return $this->_loadAction($name);
    }

    /**
     * executes the used action.
     *
     * @param string|null $action Action
     * @param array $arguments Arguments
     *
     * @return bool|\Cake\Network\Response
     * @throws Exception
     */
    public function execute($action = null, $arguments = [])
    {
//        $this->_loadListeners();

        $this->_action = $action ?: $this->_action;
        $action = $this->_action;

        if (!$arguments) {
            $arguments = $this->_controller->request->params['pass'];
        }

        try {
//            $this->trigger('Api.beforeExecute');

            $response = $this->action($action)->execute($arguments);
            if ($response instanceof Response) {
                return $response;
            }
        } catch (Exception $e) {
            if (isset($e->response)) {
                return $e->response;
            }
            throw $e;
        }

        return $this->_controller->response = $this->_controller->render(null);
    }

    /**
     * Setter for the Manager instance of Fractal.
     *
     * @param Manager $manager
     * @return Manager
     */
    public function setFractalManager(Manager $manager)
    {
        return $this->_fractalManager = $manager;
    }

    /**
     * Returns instance of fractals manager class.
     *
     * @return Manager
     */
    public function getFractalManager()
    {
        if (!$this->_fractalManager) {
            $this->_fractalManager = new Manager();

            $serializer = $this->config('serializer');
            $this->_fractalManager->setSerializer(new $serializer(Router::fullBaseUrl()));

            $include = $this->_controller->request->query('include');
            if ($include) {
                $this->_fractalManager->parseIncludes($include);
            }

        }

        return $this->_fractalManager;
    }

    /**
     * Returns Item instance of Fractal.
     *
     * @param EntityInterface $entity
     * @param null $transformer
     * @param null $resourceKey
     * @return Item
     * @internal param ResultSet $data
     */
    public function getItem(EntityInterface $entity, $transformer = null, $resourceKey = null)
    {
        $item = new Item($entity, $transformer, $resourceKey);

        return $item;
    }

    /**
     * Returns Collection instance of Fractal.
     *
     * @param ResultSet $query
     * @param null $transformer
     * @param null $resourceKey
     * @return Collection
     * @internal param Query $data
     */
    public function getCollection(ResultSet $query, $transformer = null, $resourceKey = null)
    {
        $data = $query->toArray();

        $collection = new Collection($data, $transformer, $resourceKey);

        if ($this->config('pagination')) {
            $collection->setPaginator(new CakePaginatorAdapter($this->_controller->Paginator, $this->_controller->request));
        }
        if ($this->config('cursor')) {
            $model = $this->_controller->{$this->_controller->modelClass};
            $model->addBehavior('Api.CursorHelper');
            $collection->setCursor(new CakeCursorAdapter($query, $this->_controller->request));
        }
        return $collection;
    }

    /**
     * Returns resource Item.
     *
     * @param EntityInterface $entity
     * @param null|string $transformer
     * @param null|string $resourceKey
     * @return Item
     */
    public function getResourceItem(EntityInterface $entity, $transformer = null, $resourceKey = null)
    {
        $transformer = $this->__getTransformer($transformer, [$entity]);
        $resourceKey = ($resourceKey ?: Inflector::dasherize($transformer->resourceKey));
        $resource = $this->getItem($entity, $transformer, $resourceKey ?: $resourceKey);

        return $resource;
    }

    /**
     * Returns resource Collection.
     *
     * @param ResultSet $query
     * @param null|string $transformer
     * @param null|string $resourceKey
     * @return Collection
     * @internal param array $list
     */
    public function getResourceCollection(ResultSet $query, $transformer = null, $resourceKey = null)
    {
        $transformer = $this->__getTransformer($transformer, $query);
        $resourceKey = ($resourceKey ?: Inflector::dasherize($transformer->resourceKey));
        $resource = $this->getCollection($query, $transformer, $resourceKey);

        return $resource;
    }

    /**
     * Load an Api action instance
     *
     * @param string $name Api action name
     * @throws Exception
     *
     * @return Action
     */
    protected function _loadAction($name)
    {
        if (!isset($this->_actions[$name])) {
            $config = $this->config('actions.' . $name);

            $className = App::classname($config['className'], 'Action', 'Action');

            if (!$className) {
                throw new Exception('Api Action not found');
            }

            $this->_actions[$name] = new $className($this->_controller);
        }

        return $this->_actions[$name];
    }

}
