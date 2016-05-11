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

use Cake\Datasource\EntityInterface;

class DefaultTransformer extends Transformer
{

    public $resourceKey = 'default';

    public function transform(EntityInterface $entity)
    {
        return $entity->toArray();
    }

}