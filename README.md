BiplaneEnumBundle [![Build Status](https://secure.travis-ci.org/yethee/BiplaneEnumBundle.png?branch=master)](http://travis-ci.org/yethee/BiplaneEnumBundle)
=================

This bundle provides a typed enumeration for your Symfony2 project.

**Note:** This branch is compatible with Symfony 2.1. Use the 1.0.x branch when
using 2.0.x versions of Symfony.

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
        git=http://github.com/yethee/BiplaneEnumBundle.git
        target=bundles/Biplane/EnumBundle

Run the vendors script:

```bash
$ php bin/vendors install
```

**Using Git submodule**

```bash
$ git submodule add http://github.com/yethee/BiplaneEnumBundle.git vendor/bundles/Biplane/EnumBundle
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

In order to create a typed enumeration, it's enough to extend the base class `Biplane\EnumBundle\Enumeration\Enum`
- define constants and implement `getPossibleValues()` and `getReadables()` methods. The first method
should return an array of possible values of your enumeration, the second method returns a hash list
of possible values and their human representations.

Below you can see a simple implementation of enumeration for user roles:

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

You can create a new instance of the enumeration via the `create()` factory method,
which provides the base class:

    $role = UserRoles::create(UserRoles::ADMIN);

If the argument contains an invalid value, an exception of
`Biplane\EnumBundle\Exception\InvalidEnumArgumentException` type will be thrown.

The following code example shows how to get the raw value or the human representation of
the enumeration value from the object:

    $role->getValue(); // returns string 'ROLE_ADMIN'
    $role->getReadable(); // returns string 'Admin'

You can also convert the object to a string to obtain the human representation of the enumeration value:

    (string)$role;

### Bit flags support

You can extend `Biplane\EnumBundle\Enumeration\FlaggedEnum` for an enumeration, if a bitwise operation
is to be performed on a numeric value. In this case you should override three methods: `getPossibleValues()`,
`getReadables()` and `getBitmask()`. The last method should return an integer value (bitmask)
of the possible flags for an enumeration.

In this case define enumeration constants in powers of two, that is, 1, 2, 4, 8, and so on.
This means the individual flags in combined enumeration constants do not overlap. Also you can create
an enumerated constant for commonly used flag combinations, but the values of these constants **must not be**
returned by `getPossibleValues()` method.

*Note:* to facilitate the implementation of `getBitmask()` you can use the `getMaskOfPossibleValues()` helper
method. The result of this method should be cached for better performance.

Below you can see the implementation of flags enumeration for permissions list:

```php
<?php

use Biplane\EnumBundle\Enumeration\FlaggedEnum;

class Permissions extends FlaggedEnum
{
    const READ   = 1;
    const WRITE  = 2;
    const REMOVE = 4;
    const ALL    = 7;

    static public function getPossibleValues()
    {
        return array(static::READ, static::WRITE, static::REMOVE);
    }

    static public function getReadables()
    {
        return array(
            static::READ => 'Read',
            static::WRITE => 'Write',
            static::REMOVE => 'Remove',
            static::ALL => 'All permissions',
        );
    }

    static protected function getBitmask()
    {
        return static::ALL;
    }
}
```

You can use a bitwise operation on constants for creating a new instance of the enumeration:

    $permissions = Permissions::create(Permissions::READ | Permissions::WRITE);

This type of enumeration provides some additional methods:

 - `getFlags()` returns an array of bit flags of enumeration value. For the previous example
 this method returns `array(1, 2)`.

 - `hasFlag()` returns `true` if specified flag is set in an numeric value.

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

class RoleType extends StringType
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

JMSSerializerBundle support
---------------------------

By default, registers a custom handler for the enumeration when JMSSerializerBundle is enabled.
You can disable this handler:

```yaml
# app/config/config.yml
jms_serializer:
    handlers:
        biplane_enum: false
```