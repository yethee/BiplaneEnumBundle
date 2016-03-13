Changelog
=========

2.0.1
-----

* Support latest versions of Symfony (2.7.10, 2.8.3)

2.0.0
-----

 * Added support for Symfony 3.0
 * Use PSR-4 instead of PSR-0

1.2.0
-----

 * Support JMSSerializerBundle of version 0.12;
 * [BC break] For using the custom handler for the serializer you should manually register
   your types of enumerations in the config.

1.1.1
-----

 * Fixed support PHP of version 5.3.5 and old;
 * Enhancements in the implementation of FlaggedEnum
   * Fixed determine of bit flags. Now, the zero value is will not be determined as a flag;
   * Added a constant `NONE` for the zero value;
   * [BC break] Constant with the zero value should not be returned in result from the `getPossibleValues` method;
   * Now there is no need to override `getBitmask` method;
   * Method `getMaskOfPossibleValues` marked as deprecated;
   * Make configurable a separator of flags for `ReadableFor` and `Readable` methods. By defaults `; `.

1.1.0
-----

 * Added `addFlags` and `removeFlags` methods to the `FlaggedEnum`;
 * Added support the Symfony of version 2.1;
 * Refactored the data transformers.

1.0.0
-----

Initial release.
