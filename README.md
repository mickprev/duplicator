Duplicator
==========

Library for duplicate an object (or an array of objects) using PHP annotations.

Getting started
------------

```bash
composer require mickprev/duplicator
```

```php
use MickPrev\Duplicator\Annotation\Groups;

class Product
{
    // ...
    /**
      * @Groups({"my_group"})
      */
     private $title;
    // ...
}

$duplicator = new ChainDuplicator(
    [
        new IterableDuplicator(),
        new ObjectDuplicator(),
        new DefaultDuplicator(),
    ]
);

$newProduct = $duplicator->duplicate($originalProduct, ['groups' => ['my_group']]);
```

What the duplicator does not do yet
-----------------------------------

  * Duplicate object with arguments in the constructor that have no default value.
  * 'Iterable' duplicator returns a value with the same type that the value to duplicate instead of always return an array.

Extras
------

  * [Use case](doc/USE_CASE.md)
  * [Add a business logic](doc/BUSINESS_LOGIC.md)
  * [Use library with Symfony](doc/SYMFONY_BRIDGE.md)
