Serializers
===========

Serializers are another great thing of [Fractal](http://fractal.thephpleague.com). They structure your transformed data in certain ways. There are 3 serializers available:

- `DataArraySerializer`
- `ArraySerializer`
- `JsonApiSerializer`

You can set your favorite Serializer when loading the `ApiBuilder` Component:

```
    $this->loadComponent('Api.ApiBuilder', [
        'serializer` => League\Fractal\Serializer\JsonApiSerializer::class
    ]);
```

## DataArraySerializer

> For now; read [http://fractal.thephpleague.com/serializers/](http://fractal.thephpleague.com/serializers/)

## ArraySerializer

> For now; read [http://fractal.thephpleague.com/serializers/](http://fractal.thephpleague.com/serializers/)

## JsonApiSerializer

> For now; read [http://fractal.thephpleague.com/serializers/](http://fractal.thephpleague.com/serializers/)

## Creating a custom Serializer

> For now; read [http://fractal.thephpleague.com/serializers/](http://fractal.thephpleague.com/serializers/)

## Read more

You can read more about Serializers at [the docs of Fractal](http://fractal.thephpleague.com/serializers/).
