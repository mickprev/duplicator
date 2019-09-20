Duplicator
==========

Library for duplicate a value (array, object, scalar, ...) using PHP annotations.

Getting started
------------

```bash
composer require mickprev/duplicator
```

```php
$duplicator = new ChainDuplicator(
    [
        new IterableDuplicator(),
        new ObjectDuplicator(),
        new DefaultDuplicator(),
    ]
);
$newValue = $duplicator->duplicate($originalValue, ['groups' => stringOrArray]);
```

What the duplicator does not do yet
-----------------------------------

  * Duplicate object with arguments in the constructor that have no default value.
  * 'Iterable' duplicator returns the same type as the argument instead of always return an array.

Extras
------

  * [Use case](doc/USE_CASE.md)
  * [Add a business logic](doc/BUSINESS_LOGIC.md)
  * [Use library with Symfony](doc/SYMFONY_BRIDGE.md)
