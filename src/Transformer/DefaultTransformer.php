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
namespace Bakkerij\Api\Transformer;

use Cake\Datasource\EntityInterface;

class DefaultTransformer extends Transformer
{

    public $resourceKey = 'default';

    public function transform(EntityInterface $entity)
    {
        return $entity->toArray();
    }

}