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
namespace Api\Action;

use Api\Controller\Component\ApiBuilderComponent;
use Api\Core\FractalTrait;
use Api\Core\Object;
use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Core\Exception\Exception;
use Cake\Core\InstanceConfigTrait;
use Cake\Datasource\EntityInterface;
use Cake\Network\Request;
use Cake\ORM\ResultSet;
use Cake\Utility\Inflector;
use League\Fractal\Manager;

class Action extends Object
{

    use InstanceConfigTrait;
    use FractalTrait;

    /**
     * Default configuration
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     * Used controller
     *
     * @var \Cake\Controller\Controller
     */
    protected $_controller;

    /**
     * Action constructor.
     *
     * @param \Cake\Controller\Controller $controller
     */
    public function __construct(Controller $controller)
    {
        $this->_controller = $controller;
    }

    /**
     * Enable Api action
     *
     * @return void
     */
    public function enable()
    {
        $this->config('enabled', true);
    }

    /**
     * Disable Api action
     *
     * @return void
     */
    public function disable()
    {
        $this->config('enabled', false);
    }

    /**
     * Execute the Api action
     *
     * @param array $arguments Arguments to be parsed.
     * @return bool
     */
    public function execute(array $arguments = [])
    {
        if (!$this->config('enabled')) {
            return false;
        }

        if (!is_array($arguments)) {
            $arguments = (array)$arguments;
        }

        $method = '_' . strtolower($this->_request()->method());

        if (method_exists($this, $method)) {
            $this->_controller()->eventManager()->on($this);
            return call_user_func_array([$this, $method], $arguments);
        }

        if (method_exists($this, '_execute')) {
            $this->_controller()->eventManager()->on($this);
            return call_user_func_array([$this, '_execute'], $arguments);
        }

        throw new Exception('Action could not be executed. _execute method does not exist.');
    }

    /**
     * Returns findMethod string.
     *
     * @param string $method
     * @return string|null
     */
    public function findMethod($method = null)
    {
        if ($method === null) {
            return $this->config('findMethod');
        }
        return $this->config('findMethod', $method);
    }

    /**
     * Additional auxiliary events emitted if certain traits are loaded
     *
     * @return array
     */
    public function implementedEvents()
    {
        $events = parent::implementedEvents();
        $events['Controller.beforeFilter'] = 'beforeRender';

        return $events;
    }

    /**
     * Triggers an event.
     *
     * @param \Cake\Event\Event $event Event
     * @param mixed $subject
     *
     * @return \Cake\Event\Event
     */
    protected function _trigger($event, $subject = null)
    {
        if (!$subject) {
            $subject = $this;
        }
        return $this->_controller()
            ->ApiBuilder->trigger($event, $subject);
    }

    /**
     * Returns a Table instance
     *
     * @return \Cake\ORM\Table
     */
    protected function _table()
    {
        return $this->_controller()->{$this->_controller()->modelClass};
    }

    /**
     * Returns an Entity instance
     *
     * @param array|null $data
     * @param array $options
     * @return EntityInterface|\Cake\ORM\Entity
     */
    protected function _entity(array $data = null, array $options = [])
    {
        return $this->_table()->newEntity($data, $options);
    }

    /**
     * Returns a Transformer instance
     *
     * @return mixed
     */
    protected function _transformer()
    {
        $modelClass = $this->_controller()->modelClass;

        $transformer = App::className(Inflector::classify($modelClass), 'Transformer', 'Transformer');

        if ($transformer === false) {
            $transformer = App::className('Api.Default', 'Transformer', 'Transformer');
        }

        return new $transformer;
    }

    /**
     * Returns the Controller instance
     *
     * @return \Cake\Controller\Controller
     */
    protected function _controller()
    {
        return $this->_controller;
    }

    /**
     * Api Builder instance.
     *
     * @return ApiBuilderComponent
     */
    protected function _api()
    {
        return $this->_controller()->ApiBuilder;
    }

    /**
     * FractalManager instance.
     *
     * @return Manager
     */
    protected function _fractalManager()
    {
        return $this->_api()->getFractalManager();
    }

    /**
     * Request instance.
     *
     * @return Request
     */
    protected function _request()
    {
        return $this->_controller()->request;
    }

    /**
     * Returns Item instance of Fractal.
     *
     * @param EntityInterface $entity
     * @param null $transformer
     * @param null $resourceKey
     * @return Item
     * @internal param null $data
     */
    public function getItem(EntityInterface $entity, $transformer = null, $resourceKey = null)
    {
        return $this->_api()->getItem($entity, $transformer, $resourceKey);
    }

    /**
     * Returns Collection instance of Fractal.
     *
     * @param ResultSet $query
     * @param null $transformer
     * @param null $resourceKey
     * @return Collection
     * @internal param null $data
     */
    public function getCollection(ResultSet $query, $transformer = null, $resourceKey = null)
    {
        $this->_api()->getCollection($query, $transformer, $resourceKey);
    }

    /**
     * Returns resource Item.
     *
     * @param EntityInterface $entity
     * @param null|string $transformer
     * @param null|string $resourceKey
     * @return Item
     * @internal param ResultSet $query
     */
    public function getResourceItem(EntityInterface $entity, $transformer = null, $resourceKey = null)
    {
        return $this->_api()->getResourceItem($entity, $transformer, $resourceKey);
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
        return $this->_api()->getResourceCollection($query, $transformer, $resourceKey);
    }

    public function beforeRender($event)
    {
        $controller = $event->subject();
        $controller->set('_serialize', $this->config('serialize'));
    }

}