<%
use Cake\Utility\Inflector;
%>
<?php
namespace <%= $namespace %>\Transformer;

use Bakkerij\Api\Transformer\TransformerAbstract;
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
<% if(is_array($includes)): %>

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [<%= $this->Bake->stringifyList(array_keys($includes), ['indent' => 2]) %>];
<% endif; %>

    /**
     * Transformer
     *
     * @param <%= $name %> $entity Item.
     * @return array
     */
    public function transform(<%= $name %> $entity)
    {
        return [
<% foreach($columns as $column): %>
            '<%= $column %>' => $entity->get('<%= $column %>'),
<% endforeach; %>
        ];
    }
<% if(is_array($includes)): %>
    
<% foreach($includes as $incl): %>
    /**
     * Include <%= $incl['name']%>

     *
     * @param <%= $name %> $entity
     * @return \<%= $incl['resourceClass']%>

     */
    public function include<%= $incl['name']%>($entity)
    {
        $table = $this->_repository($entity);
        $association = $table->associations()->getByProperty('<%= $incl["variable"]%>');

        $table->loadInto($entity, [$association->name()]);

        return $this-><%= $incl['resource']%>($entity->get('<%= $incl["variable"]%>'), '<%= $incl["name"]%>');
    }
<% endforeach; %>
<% endif; %>

}