<?php
namespace <%= $namespace %>\Transformer;

use Api\Transformer\Transformer;
use <%= $entity_namespace %>;

/**
 * <%= $name %> transformer.
 */
class <%= $name %>Transformer extends Transformer
{

    /**
     * Transformer
     *
     * @param <%= $name %> $entity Item.
     * @return array
     */
    public function transform(<%= $name %> $entity)
    {
        return[
<% foreach($columns as $column): %>
            '<%= $column %>' => $entity->get('<%= $column %>'),
<% endforeach; %>
        ];
    }

}