<?php

namespace Bakkerij\Api\Shell\Task;

use Bake\Shell\Task\SimpleBakeTask;
use Cake\Core\App;
use Cake\ORM\Association;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Association\HasOne;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\ResourceAbstract;

/**
 * Transformer shell task.
 */
class TransformerTask extends SimpleBakeTask
{
    /**
     * @var string
     */
    public $pathFragment = 'Transformer/';

    public function main($name = null)
    {
        $this->BakeTemplate->set('entity_namespace', $this->entity_namespace($name));
        $this->BakeTemplate->set('columns', $this->columns($name));
        $this->BakeTemplate->set('resourceKey', $this->resourceKey($name));
        $this->BakeTemplate->set('includes', $this->includes($name));

        parent::main($name);
    }

    /**
     * Get the generated object's name.
     *
     * @return string
     */
    public function name()
    {
        return 'transformer';
    }

    /**
     * Return namespace of Entity.
     *
     * @param $name
     * @return bool|string
     */
    public function entity_namespace($name)
    {
        $class = '';
        $class .= ($this->plugin ? $this->plugin . '' : '');
        $class .= Inflector::singularize($name);

        $namespace = App::className($class, 'Model/Entity');

        if (!$namespace) {
            $namespace = 'array';
        }

        return $namespace;
    }

    /**
     * Return list of columns.
     *
     * @param string $name Name.
     * @return array
     */
    public function columns($name)
    {
        $table = TableRegistry::get(Inflector::pluralize($name));
        $columns = $table->schema()->columns();
        return $columns;
    }

    /**
     * Return resource key.
     *
     * @param string $name Name.
     * @return string
     */
    public function resourceKey($name)
    {
        return Inflector::pluralize(Inflector::dasherize($name));
    }

    /**
     * Return includes.
     *
     * @param string $name Name.
     * @return array|bool
     */
    public function includes($name)
    {
        if ($this->param('no-includes')) {
            return false;
        }

        $includes = [];
        $table = TableRegistry::get(Inflector::pluralize($name));

        foreach ($table->associations()->keys() as $name) {
            $association = $table->association($name);

            $includes[$name] = [
                'variable' => $this->_rightForm($name, $association),
                'name' => $this->_rightForm($association->name(), $association),
                'type' => $this->_getAssociationType($association),
                'foreignKey' => $association->foreignKey(),
                'resource' => $this->_getResourceType($association),
                'resourceClass' => $this->_getResourceClass($association),
            ];
        };

        return $includes;
    }

    /**
     * Get the generated object's filename without the leading path.
     *
     * @param string $name The name of the object being generated
     * @return string
     */
    public function fileName($name)
    {
        return $name . 'Transformer.php';
    }

    /**
     * Get the template name.
     *
     * @return string
     */
    public function template()
    {
        return 'Bakkerij/Api.transformer';
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addOption('no-includes', [
            'help' => 'Disable generating associations',
            'default' => false,
            'boolean' => true
        ]);

        return $parser;
    }

    /**
     * Gets the association type.
     *
     * @param Association $association Association.
     * @return string
     */
    private function _getAssociationType($association)
    {
        switch (get_class($association)) {
            case HasOne::class:
                return 'HasOne';
            case BelongsTo::class:
                return 'BelongsTo';
            case HasMany::class:
                return 'HasMany';
            case BelongsToMany::class:
                return 'BelongsToMany';
        }

        return null;
    }

    /**
     * Get the resource type.
     *
     * @param Association $association
     * @return null|string
     */
    private function _getResourceType($association)
    {
        $type = $this->_getAssociationType($association);

        switch ($type) {
            case 'HasOne':
                return 'item';
            case 'BelongsTo':
                return 'item';
            case 'HasMany':
                return 'collection';
            case 'BelongsToMany':
                return 'collection';
        }

        return null;
    }

    /**
     * Get the resource class.
     *
     * @param Association $association
     * @return null|ResourceAbstract
     */
    private function _getResourceClass($association)
    {
        $type = $this->_getResourceType($association);

        switch ($type) {
            case 'item':
                return Item::class;
            case 'collection':
                return Collection::class;
        }

        return null;
    }

    /**
     * Return right form (singularize or pluralize).
     *
     * @param string $word Word.
     * @param Association $association Association.
     * @return string
     */
    private function _rightForm($word, $association)
    {
        if($this->_getResourceType($association) === 'item') {
            return Inflector::singularize($word);
        }
        if($this->_getResourceType($association) === 'collection') {
            return Inflector::pluralize($word);
        }
        return $word;
    }
}
