ApiBuilder Component
====================

The `ApiBuilder` Component is the core of the plugin. This is where it all begins...

## Loading the Component

To get started with your API, you need to load the `ApiBuilder` Component:

```
class BlogsController extends AppController
{
    use \Api\Controller\ApiControllerTrait;

    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('Api.ApiBuilder', [
            'actions' => [
                'index' => 'Api.Index',
                'view' => 'Api.View',
                'add' => 'Api.Add',
                'edit' => 'Api.Edit',
                'delete' => 'Api.Delete',
            ]
        ]);
    }
}
```

Now you are able to call the component in your Controller like:

```
$this->ApiBuilder;
```

When you're working in an Action, you can access the `ApiBuilder` like:

```
$this->_api();
```

## Configurations

The following configurations can be passed through the component:

- `actions` - Array of actions with its mappings. So when the `index` action is requested, the `IndexAction` of the API plugin is used.
- `serializer` - Namespace of the serializer to use. Default `League\Fractal\Serializer\DataArraySerializer`.
- `parser` - Namespace of the parser to use. Default `Api\Parser\DataArrayParser`.
- `paginator` - Paginator to use (for Fractal). Default `Api\Pagination\CakePaginatorAdapter`.
- `listeners` - Array of listeners to implement.
- `eventPrefix` - The prefix of events send by Crud. Default `Crud`.
- `recursionLimit`- Limit of recursions by Fractal (`GET /categories?include=blogs.author` has 2 recursions). Default `10`.
- `baseUrl` - The BaseURL of the API. Default gotten from Cake's `Router` class.
