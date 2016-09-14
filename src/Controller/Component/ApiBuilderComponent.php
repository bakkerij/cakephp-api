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
namespace Bakkerij\Api\Controller\Component;

use Bakkerij\Api\Pagination\CakePaginatorAdapter;
use Bakkerij\Api\Parser\DataArrayParser;
use Bakkerij\Api\Parser\ParserAbstract;
use Bakkerij\Api\Traits\TransformerAwareTrait;
use Bakkerij\Api\Transformer\Transformer;
use Bakkerij\Api\Transformer\TransformerAbstract;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\App;
use Cake\Datasource\ModelAwareTrait;
use Cake\ORM\ResultSet;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Scope;
use League\Fractal\Serializer\DataArraySerializer;

/**
 * ApiBuilder component
 *
 * Helps building the API and do automatic stuff.
 */
class ApiBuilderComponent extends Component
{

    use TransformerAwareTrait;

    /**
     * Component settings
     *
     * @var array
     */
    protected $_defaultConfig = [
        'actions' => [],
        'eventPrefix' => 'Crud',
        'serializer' => DataArraySerializer::class,
        'parser' => DataArrayParser::class,
        'recursionLimit' => '10',
        'baseUrl' => null,
        'paginator' => CakePaginatorAdapter::class,
    ];

    /**
     * Controller instance.
     *
     * @var \Cake\Controller\Controller
     */
    protected $_controller;

    /**
     * EvenManager instance.
     *
     * @var \Cake\Event\EventManager
     */
    protected $_eventManager;

    /**
     * FractalManager instance.
     *
     * @var Manager
     */
    protected $_fractalManager;

    /**
     * Constructor
     *
     * @param \Cake\Controller\ComponentRegistry $collection Component Registry
     * @param array $config Array of configuration settings.
     */
    public function __construct(ComponentRegistry $collection, $config = [])
    {
        $this->_controller = $collection->getController();
        $this->_eventManager = $this->_controller->eventManager();

        parent::__construct($collection, $config);

        $this->_createCrudComponentInstance();
    }

    /**
     * Creates instance of the Crud Component if it doesn't exist yet.
     *
     * @return void
     */
    protected function _createCrudComponentInstance()
    {
        if (!$this->_registry->has('Crud.Crud')) {
            $this->_controller->loadComponent('Crud.Crud', $this->config());
        }
    }

    /**
     * Getter for FractalManager instance.
     *
     * @return Manager
     */
    public function getFractalManager()
    {
        if (!$this->_fractalManager) {
            $manager = new Manager();

            $serializer = $this->config('serializer');
            $manager->setSerializer(new $serializer($this->config('baseUrl') ?: Router::fullBaseUrl()));

            $manager->parseIncludes((array)explode(',', $this->request->query('include')));
            $manager->setRecursionLimit($this->config('recursionLimit'));

            $this->_fractalManager = $manager;
        }
        return $this->_fractalManager;
    }

    /**
     * Setter for FractalManager instance.
     *
     * @param Manager $manager
     * @return Manager
     */
    public function setFractalManager(FractalManager $manager)
    {
        return $this->_fractalManager = $manager;
    }

    /**
     * Transforms the given collection.
     *
     * @param $collection
     * @param string|Transformer $transformer
     * @param callable|null $callable
     * @return Collection
     */
    public function collection($collection, $transformer, callable $callable = null)
    {
        if (!$transformer instanceof TransformerAbstract) {
            $transformer = $this->getTransformer($transformer);
        }

        $resourceKey = $transformer->resourceKey();
        $result = new Collection($collection, $transformer, $resourceKey);

        if ($this->config('paginator')) {
            $result->setPaginator($this->createPaginator());
        }

        return $result;
    }

    /**
     * Transforms the give item.
     *
     * @param $item
     * @param string|Transformer $transformer
     * @param array $options
     * @return Item
     */
    public function item($item, $transformer, array $options = [])
    {
        if (!$transformer instanceof TransformerAbstract) {
            $transformer = $this->getTransformer($transformer);
        }

        $resourceKey = $transformer->resourceKey();
        $result = new Item($item, $transformer, $resourceKey);

        return $result;
    }

    public function getRequestData()
    {
        $parser = $this->config('parser');
        if (!$parser instanceof ParserAbstract) {
            $parser = new $parser;
        }

        return (array)$parser->getData($this->request);
    }

    /**
     * Link to `createData` method of Fractals' Manager.
     *
     * @param ResourceInterface $resource
     * @return Scope
     */
    public function createData(ResourceInterface $resource)
    {
        return $this->getFractalManager()->createData($resource);
    }

    /**
     * Creates instance of Paginator.
     *
     * @return CakePaginatorAdapter
     */
    protected function createPaginator()
    {
        $paginator = $this->config('paginator');

        return new $paginator(
            $this->_controller->Paginator,
            $this->_controller->request
        );
    }

}
