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
namespace Api\Transformer;

use Api\Traits\TransformerAwareTrait;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract as FractalTransformerAbstract;

abstract class TransformerAbstract extends FractalTransformerAbstract
{
    use TransformerAwareTrait;

    /**
     * Getter for the resourceKey.
     *
     * @return string
     */
    abstract public function resourceKey();

    /**
     * Transforms the give item.
     *
     * @param mixed $item
     * @param string|Transformer $transformer
     * @param null|string $resourceKey
     * @return Item
     */
    public function item($item, $transformer, $resourceKey = null)
    {
        if(!$transformer instanceof TransformerAbstract) {
            $transformer = $this->getTransformer($transformer);
        }

        $resourceKey = $transformer->resourceKey();
        return parent::item($item, $transformer, $resourceKey);
    }

    /**
     * Transforms the given collection.
     *
     * @param mixed $collection
     * @param string|Transformer $transformer
     * @param null|string $resourceKey
     * @return Collection
     */
    public function collection($collection, $transformer, $resourceKey = null)
    {
        if(!$transformer instanceof TransformerAbstract) {
            $transformer = $this->getTransformer($transformer);
        }

        $resourceKey = $transformer->resourceKey();
        return parent::collection($collection, $transformer, $resourceKey);
    }


    /**
     * Gets the repository for this entity
     *
     * @param EntityInterface $entity
     * @return Table
     */
    protected function _repository($entity)
    {
        $source = $entity->source();
        if ($source === null) {
            list(, $class) = namespaceSplit(get_class($entity));
            $source = Inflector::pluralize($class);
        }
        return TableRegistry::get($source);
    }
}