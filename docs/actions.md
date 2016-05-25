Actions
=======

The following actions are provided by the Api Plugin:

- `Api.Index`
- `Api.View`
- `Api.Add`
- `Api.Edit`
- `Api.Delete`

You can load those actions when loading the `ApiBuilder` Component:

```
    $this->loadComponent('Api.ApiBuilder', [
        'actions' => [
            'index' => 'Api.Index',
            'view' => 'Api.View',
            'add' => 'Api.Add'
            'edit' => 'Api.Edit',
            'delete' => 'Api.Delete',
        ]
    ]);
```

All Action classes extend on the `Api\Action\ApiAction` class.

## General methods

The `Action` classes heve some usefull methods.

To set a status code you can use the following example:

```
$this->statusCode(201);
```

> Note that all other methods like `_table()`, `_controller()`, `_request()`, and more are all available thanks to the Crud plugin.

## Fractal related methods

The `Manager` class of Fractal is accessible with the method:

```
$manager = $this->_fractal();
```

You can easily get the related Transformer instance like:

```
$transformer = $this->_transformer();
```

This can be helpful when you want to transform an Item or a Collection:

```
$result = $this->collection($data, $this->_transformer());
```

As you see collections and items can be build with the following methods:

```
// Return a collection
$result = $this->collection($data, 'Blog');

// Return an item
$result = $this->item($data, 'Blog');
```

To 'create' the data to a returnable format you can use the `createData` method like:

```
$data = $this->createData($result)->toArray();
```

This result should be used as response data.

## Api.Index

This Action is used when calling e.g. `GET /blogs`.

The following events are triggered during this Action:

- `Crud.beforePaginate`
- `Crud.afterPaginate`
- `Crud.beforeRender`

## Api.View

This Action is used when calling e.g. `GET /blogs/1`.

The following events are triggered during this Action:

- `Crud.beforeRender`

## Api.Add

This Action is used when calling e.g. `POST /blogs`.

The following events are triggered during this Action:

- `Crud.beforeSave`

## Api.Edit

This Action is used when calling e.g. `POST /blogs/1`.

The following events are triggered during this Action:

- `Crud.beforeSave`

## Api.Delete

This Action is used when calling e.g. `DELETE /blogs/1`.

> Not implemented properly yet...

## Further Reading

Read more about actions [here](http://crud.readthedocs.io/en/latest/actions.html)