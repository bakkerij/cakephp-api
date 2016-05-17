# Installation

In this section we will explain how to install this plugin.

##  Requirements

- CakePHP 3.2+
- PHP 5.5.9+

## Using Composer

The recommended installation method for this plugin is by using composer.

```
$ composer require cakeplugins/api
```

## Loading the plugin

Add the following to your /config/bootstrap.php

```
Plugin::load('Api');
```

Or execute the following cake command:

```
$ bin/cake plugin install Api
```

## Configuring the controller

The Api Plugin provides a trait wich should be used in your controller. It's recommended to add this code in your `AppController`:

```
namespace App\Controller;

class AppController extends Controller
{
    use Api\Controller\ApiControllerTrait;

}
```

> To have the API just scaffold a single controller you can just add the `ApiControllerTrait` to that specific controller.

Adding the `ApiControllerTrait` itself do not enable the API Builder, but simply installs the code to handle the `\Cake\Error\MissingActionException`
exception which is called if the action method (like `index`) does not exist.

Now you have to load the `ApiBuilder` component in the controller. Example:

```
class AppController extends \Cake\Controller\Controller
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


