Parsers
=======

Because Fractal is only for *output* data, this plugin introduces Parsers to parse incoming request data into entities. There are 3 Parsers available:

- `DataArrayParser`
- `ArrayParser`
- `JsonApiParser`

You can set your favorite Parser when loading the `ApiBuilder` Component:

```
    $this->loadComponent('Api.ApiBuilder', [
        'parser` => Api\Parser\JsonApiParser::class
    ]);
```

> Note: When you choose a specific Serializer, it is adviced to use the same named Parser. This way the output structure strokes with the input structure.


