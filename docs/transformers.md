Transformers
============

Transformers are classes, which are responsible for taking one instance of the resource data and converting it to a basic array. Transformers are implemented by [Fractal](http://fractal.thephpleague.com/transformers/).

## Transformer Class

A Transformer class should be placed under de namespace `App\Transformer`. They 

## Getting Transformers

## Bake

You can easily bake Transformers with the command:

```
$ bin/cake bake transformer Blog
```

Note that you should add Transformers in singular form.

### Options
The following options are available:

- `--no-includes` - By default the bake method will add include methods for every relationship. If you don't want this, add this flag.

### Result

The result will look like:

```
namespace App\Transformer;

use Bakkerij\Api\Transformer\TransformerAbstract;
use App\Model\Entity\Blog;

/**
 * Blog transformer.
 */
class BlogTransformer extends TransformerAbstract
{

    /**
     * Getter for the resourceKey.
     *
     * @return string
     */
    public function resourceKey()
    {
        return 'blogs';
    }

    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        'categories'
    ];

    /**
     * Transformer
     *
     * @param Blog $entity Item.
     * @return array
     */
    public function transform(Blog $entity)
    {
        return [
            'id' => $entity->get('id'),
            'name' => $entity->get('name'),
            'body' => $entity->get('body'),
            'category_id' => $entity->get('category_id'),
            'created' => $entity->get('created'),
            'modified' => $entity->get('modified'),
        ];
    }
    
    /**
     * Include Category
     *
     * @param Blog $entity
     * @return \League\Fractal\Resource\Item
     */
    public function includeCategory($entity)
    {
        $table = $this->_repository($entity);
        $association = $table->associations()->getByProperty('category');

        $table->loadInto($entity, [$association->name()]);

        return $this->item($entity->get('category'), 'Category');
    }

}
```

## Read more

You can read more about Transformers at [the docs of Fractal](http://fractal.thephpleague.com/transformers/).