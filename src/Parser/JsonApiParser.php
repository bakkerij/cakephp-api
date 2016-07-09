<?php

namespace Bakkerij\Api\Parser;

use Cake\Network\Request;

class JsonApiParser extends ParserAbstract
{

    /**
     * Get data of the request
     *
     * @param Request $request
     * @return array
     */
    public function getData(Request $request)
    {
        $data = $request->data('attributes');
        if($request->data('id')) {
            $data['id'] = $request->data('id');
        }
        return $data;
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