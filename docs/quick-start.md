# Quick Start

In this section we will show a quick example of the use of the plugin.

After [installation](installation.md), you are ready to create your API endpoints.

## Routing

Add the following to your `config/routes.php` to enable RESTful routing for your API:

```
Router::scope('/', function (RouteBuilder $routes) {
    $routes->extensions(['json']);
    $routes->resources('blogs');
});
```

## ApiBuilder Component

To get started you need to load the `ApiBuilder` Component:

```
class BlogsController extends AppController
{
    use Api\Controller\ApiControllerTrait;

    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Api.ApiBuilder', [
            'actions' => [
                'index' => 'Api.Index',
                'view' => 'Api.View',
                'add' => 'Api.Add'
                'edit' => 'Api.Edit',
                'delete' => 'Api.Delete',
            ]
        ]);
    }
}
```

> Note: Want to read more about the `ApiBuilder` Component? [Check out the possibilities here!](/api-builder.md)

## Transformers

Transformers are classes, which are responsible for taking one instance of the resource data and converting it to a basic array. Transformers are implemented by [Fractal](http://fractal.thephpleague.com/transformers/).

If you want a hand-on, we recommend to use this query first to build up your `blogs` table...:

```
CREATE TABLE `blogs` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(255) NULL DEFAULT NULL,
    `body` TEXT NULL,
    `author` VARCHAR(255) NULL DEFAULT NULL,
    `created` DATETIME NULL DEFAULT NULL,
    `modified` DATETIME NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
);
```

... And quickely build your model:

```
$ bin/cake bake model Blogs
```

You can easily create your first transformer with `cake bake`:

```
$ bin/cake bake transformer Blog
```

You can see that the `BlogTransformer` class has the following method:

```
namespace App\Transformer;

class BlogTransformer extends Transformer
{

    /**
     * Transformer
     *
     * @param Blog $entity Item.
     * @return array
     */
    public function transform(Blog $entity)
    {
        return[
            'id' => $entity->get('id'),
            'name' => $entity->get('name'),
            'body' => $entity->get('body'),
            'category_id' => $entity->get('category_id'),
            'created' => $entity->get('created'),
            'modified' => $entity->get('modified'),
        ];
    }

}
```

The good thing about Transformers is that they are the “barrier” between source data and output. That means that schema changes do not affect users.
You are free to choose the structure of the result of your `transform` method.

> Note: Want to read more about Transformers? [Check out the possibilities here!](http://fractal.thephpleague.com/transformers/)

## Serializers

Serializers are another great thing of [Fractal](http://fractal.thephpleague.com/transformers/). They structure your transformed data in certain ways.
There are 3 serializers available:

- `DataArraySerializer` - The default serializer. It adds a `data` namespace to the output:
```
// Item
[
    'data' => [
        'foo' => 'bar'
    ],
];

// Collection
[
    'data' => [
        [
            'foo' => 'bar'
        ]
    ],
];
```
- `ArraySerializer` - Sometimes people want to remove that 'data' namespace for items, which can be done by this serializer. 
This is mostly the same, other than that namespace for items. Collections keep the 'data' namespace to avoid confusing JSON when meta data is added.
```
// Item
[
    'foo' => 'bar'
];

// Collection
[
    'data' => [
        'foo' => 'bar'
    ]
];
```
- `JsonApiSerializer` - This is a representation of the [JSON-API standard (v1.0)](http://jsonapi.org/) which is very great. Output looks like:
```
// Item
[
    'data' => [
        'type' => 'books',
        'id' => 1,
        'attributes' => [
            'foo' => 'bar'
        ],
    ],
];

// Collection
[
    'data' => [
        [
            'type' => 'books',
            'id' => 1,
            'attributes' => [
                'foo' => 'bar'
            ],
        ]
    ],
];
```

You can set the serializer you want via the `ApiBuilder`:

```
$this->loadComponent('Api.ApiBuilder', [
    'serializer' => League\Fractal\Serializer\JsonApiSerializer::class
]);
```

> Note: Want to read more about Serializers? [Check out the possibilities here!](http://fractal.thephpleague.com/serializers/)

## Result

You've added the `ApiBuilder` component to the controller, you've created the transformer, and chose your serializer. You are ready to go!

