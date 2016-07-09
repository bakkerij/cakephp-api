<?php

namespace Bakkerij\Api\Parser;


use Cake\Network\Request;

abstract class ParserAbstract
{

    /**
     * Get data of the request
     *
     * @param Request $request
     * @return array
     */
    abstract public function getData(Request $request);

    /**
     * Get relationships of the request.
     *
     * @param Request $request
     * @return array
     */
    abstract public function getRelationships(Request $request);

}