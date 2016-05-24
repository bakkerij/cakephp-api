<?php
namespace Api\Shell\Task;

use Bake\Shell\Task\SimpleBakeTask;
use Cake\Console\Shell;
use Cake\Core\App;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

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
        return 'Api.transformer';
    }
}
