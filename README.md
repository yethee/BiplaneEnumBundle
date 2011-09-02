BiplaneEnumBundle
=================

This bundle provides a typed enumeration for your Symfony2 project.

### Features

- Provides base implementation for enum type.
- Provides base implementation for flags enum type (treated as a bit field, that is a set of flags).
- Uses enum types with Symfony2's Form Component.
- Contains normalizer for Symfony2's Serializer Component.
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

Usage
-----

In order to create a typed enumeration, enough extend a base class `Biplane\EnumBundle\Enumeration\EnumBundle`
- define constants and implement `getPossibleValues()` and `getReadables()` methods. The first method
should return an array of possible values of your enumeration, the second method returns a hash list
of possible values and their human representations.

Below you can see simple implementation of enumeration for user roles:

```php
<?php

use Biplane\EnumBundle\Enumeration\Enum;

class UserRoles extends Enum
{
    const MEMBER = 'ROLE_MEMBER';
    const ADMIN  = 'ROLE_ADMIN';

    static public function getPossibleValues()
    {
        return array(static::MEMBER, static::ADMIN);
    }

    static public function getReadables()
    {
        return array(static::MEMBER => 'Member', static::ADMIN => 'Admin');
    }
}
```

### Using with Doctrine ORM

You can store a raw value of the enum in the entity, and use casting type in getter and setter:

```php
<?php

use Doctrine\ORM\Mapping as ORM;

class User
{
    /**
     * @ORM\Column(type="string")
     */
    private $role;

    public function getRole()
    {
        return UserRoles::create($this->role);
    }

    public function setRole(UserRoles $role)
    {
        $this->role = $role->getValue();
    }
}
```

Or you can create a custom type of DBAL for move the logic of casting type from the entity:

```php
<?php

use Doctrine\DBAL\Types\StringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class RoleType extend StringType
{
    public function getName()
    {
        return 'role_enum';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->getValue();
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        return UserRoles::create($value);
    }
}
```

After that you should register your type, this can be done through config:

```yaml
# app/config/config.yml
doctrine:
    dbal:
        types:
            role_enum: RoleType
```

Set your type in the mapping of the entity:

```php
<?php

use Doctrine\ORM\Mapping as ORM;

class User
{
    /**
     * @ORM\Column(type="role_enum")
     */
    private $role;
}
```