<?php
namespace <%= $namespace %>\Transformer;

use Api\Transformer\TransformerAbstract;
use <%= $entity_namespace %>;

/**
 * <%= $name %> transformer.
 */
class <%= $name %>Transformer extends TransformerAbstract
{

    /**
     * Getter for the resourceKey.
     *
     * @return string
     */
    public function resourceKey()
    {
        return '<%= $resourceKey %>';
    }

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