<?php

namespace Api\Traits;

use Api\Core\Exception\MissingTransformerException;
use Api\Transformer\TransformerAbstract;
use Cake\Core\App;
use Cake\Utility\Inflector;

trait TransformerAwareTrait
{

    /**
     * Generates instance of the Transformer.
     *
     * The method works with the App::className() method.
     * This means that you can call app-related Transformers like `Books`.
     * Plugin-related Transformers can be called like `Plugin.Books`
     *
     * @param $className
     * @return TransformerAbstract
     * @throws MissingTransformerException
     */
    public function getTransformer($className)
    {
        $transformer = App::className(Inflector::classify($className), 'Transformer', 'Transformer');

        if ($transformer === false) {
            throw new MissingTransformerException(['transformer' => $className]);
        }

        return new $transformer;
    }

}