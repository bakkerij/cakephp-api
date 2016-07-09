<?php

namespace Bakkerij\Api\Parser;

use Cake\Network\Request;

class ArrayParser extends ParserAbstract
{

    /**
     * Get data of the request
     *
     * @param Request $request
     * @return array
     */
    public function getData(Request $request)
    {
        return $request->data();
    }

    /**
     * Get relationships of the request.
     *
     * @param Request $request
     * @return array
     */
    public function getRelationships(Request $request)
    {
        // TODO: Implement getRelationships() method.
    }
}