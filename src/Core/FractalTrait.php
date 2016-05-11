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
namespace Api\Core;

use Api\Core\Exception\MissingTransformerException;
use Api\Transformer\Transformer;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\ORM\ResultSet;
use Cake\Utility\Inflector;

trait FractalTrait
{

    /**
     * Returns instance of a Transformer-class by its className.
     *
     * @param string $className Name of the class for example `Api.Index` or `Index`.
     * @return Transformer
     */
    public function getTransformer($className)
    {
        $transformer = App::className(Inflector::classify($className), 'Transformer', 'Transformer');

        if ($transformer === false) {
            $allowDefault = (array) Configure::read('Api.AllowDefaultTransformer');

            $defaultTransformer = (string) Configure::read('Api.DefaultTransformer') ?: 'Api.Default';

            if (in_array($className, $allowDefault)) {
                $transformer = App::className($defaultTransformer, 'Transformer', 'Transformer');
            } else {
                throw new MissingTransformerException(['transformer' => $className]);
            }
        }

        return new $transformer;
    }

    /**
     * Private method to find the right transformer class.
     *
     * @param null $transformer
     * @param ResultSet $query
     * @return Transformer|bool|null
     * @internal param null $list
     */
    private function __getTransformer($transformer = null, $query = null)
    {
        if ($query) {
            if ($query instanceof EntityInterface) {
                $entity = $query;
            } else if (is_array($query) && (reset($query) instanceof EntityInterface)) {
                $entity = reset($query);
            } else {
                $entity = $query->first();
            }
        }

        if (!$transformer) {
            if (isset($entity)) {
                $transformer = $this->getTransformer($entity->source());
                if ($transformer) {
                    return $transformer;
                }
            } else if (isset($this->transformer)) {
                $transformer = $this->transformer;
            }
        }

        if (!$transformer) {
            return $this->getTransformer('Api.Default');
        }

        if ($transformer instanceof Transformer) {
            return $transformer;
        }

        $instance = $this->getTransformer($transformer);

        if ($instance) {
            return $instance;
        }

        if (!$instance) {
            return new $transformer;
        }

        return false;
    }

}