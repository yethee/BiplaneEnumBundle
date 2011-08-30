BiplaneEnumBundle
=================

This bundle provides a typed enumeration for your Symfony2 project.

### Features

- Provides base implementation for enum type.
- Provides base implementation for flags enum type (treated as a bit field, that is a set of flags).
- Uses enum types with Symfony2's Form Component.
- Contains normalizer for Symfony2' Serializer Component.
- Contains the custom handler for JMSSerializerBundle.

Installation
------------

### Add this bundle to your project

**Using the vendors script**

Add the following lines in your deps file:

    [BiplaneEnumBundle]
        git=https://github.com/yethee/BiplaneEnumBundle.git
        target=bundles/Biplane/EnumBundle

Run the vendors script:

```bash
$ php bin/vendors install
```

**Using Git submodule**

```bash
$ git submodule add https://github.com/yethee/BiplaneEnumBundle.git vendor/bundles/Biplane/EnumBundle
```

### Add the Biplane namespace to your autoloader

```php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    'Biplane' => __DIR__.'/../vendor/bundles',
    // your other namespaces
));
```

### Add this bundle to your application kernel

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    return array(
        // ...
        new Biplane\EnumBundle\BiplaneEnumBundle(),
        // ...
    );
}
```