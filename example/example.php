<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Ulrack\Services\Factory\ServiceFactory;
use GrizzIt\Storage\Component\ObjectStorage;
use GrizzIt\ObjectFactory\Factory\ObjectFactory;
use Ulrack\JsonSchema\Factory\SchemaValidatorFactory;
use Ulrack\Services\Component\Compiler\ServiceCompiler;
use Ulrack\Services\Component\Registry\ServiceRegistry;
use GrizzIt\ObjectFactory\Component\Analyser\ClassAnalyser;
use GrizzIt\ObjectFactory\Component\Reflector\MethodReflector;

// Create the registry.
$serviceRegistry = new ServiceRegistry();

// Create the storage for the compiled services.
$serviceStorage = new ObjectStorage();

// Create a storage for the reflection results.
$analysisStorage = new ObjectStorage();

// Create the method reflector.
$methodReflector = new MethodReflector(
    $analysisStorage
);

// Create the class analyser so we know which parameters are expected.
$classAnalyser = new ClassAnalyser(
    $analysisStorage,
    $methodReflector
);

// Create an instance of the ObjectFactory so new objects can be constructed.
$objectFactory = new ObjectFactory($classAnalyser);

// Create the service compiler.
$serviceCompiler = new ServiceCompiler(
    $serviceRegistry,
    $serviceStorage,
    $objectFactory
);

$extensions = [];
$extensionDir = array_diff(
    scandir(__DIR__ . '/../configuration/service-compiler-extensions'),
    ['.', '..']
);

// Create a list of all compiler extensions.
foreach ($extensionDir as $extension) {
    $extensions[] = json_decode(
        file_get_contents(
            __DIR__ . '/../configuration/service-compiler-extensions/' . $extension
        ),
        true
    );
}

// Create a schema validator factory from ulrack/json-schema to quickly validate the schema's.
$schemaValidatorFactory = new SchemaValidatorFactory();

$validator = $schemaValidatorFactory->createFromLocalFile(
    __DIR__ . '/../configuration/schema/service.compiler.extension.schema.json'
);

// Add all compiler extensions
foreach ($extensions as $extension) {
    $serviceCompiler->addExtension(
        $extension['key'],
        $extension['class'],
        $extension['sortOrder'],
        $schemaValidatorFactory->create(
            json_decode(
                json_encode(
                    $extension['schema']
                )
            )
        ),
        $extension['parameters'] ?? []
    );
}

// Retrieve all registrations for compilation.
$services = json_decode(
    file_get_contents(
        __DIR__ . '/services/services.json'
    ),
    true
);

// Add all services to the registry.
foreach ($services as $extensionKey => $definition) {
    if (is_array($definition)) {
        foreach ($definition as $definitionKey => $defined) {
            $serviceRegistry->add($extensionKey, $definitionKey, $defined);
        }
    }
}

// Create the service factory.
$serviceFactory = new ServiceFactory(
    $serviceCompiler,
    $objectFactory,
    $classAnalyser,
    $methodReflector
);

$extensions = [];
$extensionDir = array_diff(
    scandir(__DIR__ . '/../configuration/service-factory-extensions'),
    ['.', '..']
);

// Create a list of all factory extensions.
foreach ($extensionDir as $extension) {
    $extensions[] = json_decode(
        file_get_contents(
            __DIR__ . '/../configuration/service-factory-extensions/' . $extension
        ),
        true
    );
}

// Add all extensions to the factory.
foreach ($extensions as $extension) {
    $serviceFactory->addExtension(
        $extension['key'],
        $extension['class'],
        $extension['parameters'] ?? []
    );
}

// Retrieve a defined service.
var_dump($serviceFactory->create('services.not-validator'));
