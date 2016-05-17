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

use Api\Exception\MissingTransformerException;
use Api\Pagination\CakePaginatorAdapter;
use Api\Transformer\Transformer;
use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Core\App;
use Cake\ORM\ResultSet;
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

    /**
     * Component settings
     *
     * @var array
     */
    protected $_defaultConfig = [
        'actions' => [],
        'eventPrefix' => 'Crud',
        'serializer' => DataArraySerializer::class,
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
        if(!$this->_fractalManager) {
            $manager = new Manager();

            $serializer = $this->config('serializer');
            $manager->setSerializer(new $serializer);

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
     * Generates instance of the Transformer.
     *
     * The method works with the App::className() method.
     * This means that you can call app-related Transformers like `Books`.
     * Plugin-related Transformers can be called like `Plugin.Books`
     *
     * @param $className
     * @return Transformer
     */
    public function getTransformer($className)
    {
        $transformer = App::className(Inflector::classify($className), 'Transformer', 'Transformer');

        if ($transformer === false) {
            throw new MissingTransformerException(['transformer' => $className]);
        }

        return new $transformer;
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
        if(!$transformer instanceof Transformer) {
            $transformer = $this->getTransformer($transformer);
        }

        $resourceKey = $transformer->resourceKey;
        $result = new Collection($collection, $transformer, $resourceKey);

        if($this->config('paginator')) {
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
        if(!$transformer instanceof Transformer) {
            $transformer = $this->getTransformer($transformer);
        }

        $resourceKey = $transformer->resourceKey;
        $result = new Item($item, $transformer, $resourceKey);

        return $result;
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
