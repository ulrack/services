[![Build Status](https://travis-ci.com/ulrack/services.svg?branch=master)](https://travis-ci.com/ulrack/services)

# Ulrack Services

This package contains a services implementation.
These services can be configured for a project to create a configured depency injection layer.
The services are ran through a compiler to create workable data for the factories.
The factories are used to retrieve assembled objects for the project.
Both the compiler and factory can be extended by creating compiler extensions and factory extensions.
On top of that, existing compilers and factories can be extended and altered with hooks.

## Installation

To install the package run the following command:

```
composer require ulrack/services
```

## Usage

### Registry

To start, the [ServiceRegistry](src/Component/Registry/ServiceRegistry.php) needs to be created.
The ServiceRegistry is the object on which all service definitions are registered,
before they are compiled. To create the ServiceRegistry use the following snippet:
```php
<?php

use Ulrack\Services\Component\Registry\ServiceRegistry;

$serviceRegistry = new ServiceRegistry();
```

The service definitions can then be registered with the `add` method.
See the files in [configuration/schema](configuration/schema) to see everything
that can be registered out of the box.
```php
<?php

use Ulrack\Validator\Component\Logical\AlwaysValidator;

$serviceRegistry->add(
    'services',
    'foo',
    [
        'class' => AlwaysValidator::class,
        'parameters' => [
            'alwaysBool' => true
        ]
    ]
);
```

### Compiler

Then to create the compiler use the following snippet:

```php
<?php

use Ulrack\ObjectFactory\Component\Analyser\ClassAnalyser;
use Ulrack\ObjectFactory\Factory\ObjectFactory;
use Ulrack\Services\Component\Compiler\ServiceCompiler;
use Ulrack\Storage\Component\ObjectStorage;

// The services storage in this example only exists during the execution.
// Use an alternative implementation of the StorageInterface to keep the compiled services.
$serviceStorage = new ObjectStorage();

$classAnalyser = new ClassAnalyser(
    new ObjectStorage()
);

$objectFactory = new ObjectFactory($classAnalyser);

$serviceCompiler = new ServiceCompiler(
    $serviceRegistry,
    $serviceStorage,
    $objectFactory
);
```

### Compiler extensions

The compiler is used to reformat all defined code to operate faster in the factories later on.
In order to add logic to the compiler, extensions need to be added.
To support services, the [ServicesCompiler](src/Component/Compiler/Extension/ServicesCompiler.php) can be used.
Extensions are required to implement the [AbstractServiceCompilerExtension](src/Common/AbstractServiceCompilerExtension.php).

To add the ServicesCompiler, use the following snippet:

```php
<?php

use Ulrack\Services\Component\Compiler\ServicesCompiler;

$serviceCompiler->addExtension(
    // This will be the key which is used in the service definitions for services.
    'services',
    ServicesCompiler::class,
    // This is the sort order of the extension, it is used to determine the order of execution.
    0,
    // Optionaly a validator implementing the ValidatorInterface can be provided here
    null,
    // Parameters can be provided to the extension here.
    []
);
```

### Compiler hooks

To add a hook to the compiler the following snippet can be used:

```php
<?php

$serviceCompiler->addHook(
    // Note that the key must the same as the the compiler which needs to be hooked into.
    'services',
    MyHook::class,
    // The sort order to determine the order of execution.
    0,
    // Optional parameters for the hook.
    []
);
```

The hooks are required to implement the [AbstractServiceCompilerHook](src/Common/Hook/AbstractServiceCompilerHook.php).


### Creating the factory

Once everything is configured for the compiler, the factory can be created in a similar way.

```php
<?php

use Ulrack\Services\Factory\ServiceFactory;

$serviceFactory = new ServiceFactory(
    // The previously configured service compiler.
    $serviceCompiler,
    $objectFactory,
    $classAnalyser
);

```

### Factory extensions

Factory extensions must implement the [AbstractServiceFactoryExtension](src/Common/AbstractServiceFactoryExtension.php).
To add the `services` factory use the following snippet:

```php
<?php

use Ulrack\Services\Factory\Extension\ServicesFactory;

$serviceFactory->addExtension(
    // The key on which services are registered.
    'services',
    ServicesFactory::class,
    // Optional parameters.
    []
);
```

### Factory hooks

Adding a factory hook is similar to factory extension.
It must implement the [AbstractServiceFactoryHook](src/Common/Hook/AbstractServiceFactoryHook.php).
Then to add the hook, use the following snippet:

```php
<?php

$serviceFactory->addHook(
    // The key on which services are registered.
    'services',
    MyHook::class,
    // The sort order, used for the order of execution.
    0,
    // A set of optional parameters for the hook.
    []
);
```

### Using the factory

After everything is configured the factory can be used to create an instance of the declared service,
or retrieve a certain configured value. To do so invoke the create method with the reference to the service declaration.

```php
<?php

// The key is a combination of the scope of the required service and the name of the service.
$serviceFactory->create('services.foo')
```

This will return whatever is registered on `foo`.

## Example

To see a full example, see the [example](example) directory.
Run the following commands in the root directory to be able to execute the example:
```bash
composer require ulrack/json-schema
php example/example.php
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE_OF_CONDUCT](CODE_OF_CONDUCT.md) for details.

## MIT License

Copyright (c) GrizzIT

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
