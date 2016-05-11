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
namespace Api\Cursor;

use Cake\Network\Request;
use Cake\ORM\ResultSet;
use Cake\ORM\Table;
use League\Fractal\Pagination\CursorInterface;

class CakeCursorAdapter implements CursorInterface
{

    /**
     * Table instance.
     *
     * @var Table
     */
    protected $query;

    /**
     * Request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * CakeCursorAdapter constructor.
     */
    public function __construct(ResultSet $query, Request $request)
    {
        $this->query = $query;
        $this->request = $request;
    }

    /**
     * Get the current cursor value.
     *
     * @return mixed
     */
    public function getCurrent()
    {
        return $this->request->query('cursor') ?: 10;
    }

    /**
     * Get the prev cursor value.
     *
     * @return mixed
     */
    public function getPrev()
    {
        return $this->request->query('previous');
    }

    /**
     * Get the next cursor value.
     *
     * @return mixed
     */
    public function getNext()
    {
        return $this->query->next();
    }

    /**
     * Returns the total items in the current cursor.
     *
     * @return int
     */
    public function getCount()
    {
        return $this->query->find()->count();
    }
}