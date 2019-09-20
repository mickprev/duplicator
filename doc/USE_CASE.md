USE CASE
========

Imagine that you have an E-commerce website and to contribute more quickly, you want to be able to duplicate a product.

We consider that you own the following classes:

A product class
---------------
```php
<?php

// src/Entity/Product

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use MickPrev\Duplicator\Annotation\Groups as DuplicatorGroups;

/**
 * @ORM\Entity
 */
class Product
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="guid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column
     * @DuplicatorGroups({"duplicate_product"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @DuplicatorGroups({"duplicate_product"})
     */
    private $description;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity=Category::class))
     * @DuplicatorGroups({"duplicate_product"})
     */
    private $category;

    /**
     * @var Collection|Advice[]
     *
     * @ORM\OneToMany(targetEntity=Advice::class, mappedBy="product", cascade={"persist"})
     * @DuplicatorGroups({"duplicate_product"})
     */
    private $advices;

    /**
     * @var Person
     *
     * @ORM\ManyToOne(targetEntity=Person::class))
     */
    private $author;

    public function __construct()
    {
        $this->advantages = new ArrayCollection();
        $this->advices = new ArrayCollection();
        $this->technicalInformations = new ArrayCollection();
    }
    
    // Getters
    // Setters
    // Adders
    // Removers
    // ...
}
```

A category class
----------------
```php
<?php

// src/Entity/Category

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Category
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="guid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column
     */
    private $descrption;

    // Getters
    // Setters
    // ...
}
```

An advice class
---------------
```php
<?php

// src/Entity/Advice.php 

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use MickPrev\Duplicator\Annotation\Groups as DuplicatorGroups;

/**
 * @ORM\Entity
 */
class Advice
{
    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="guid", unique=true)
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column
     * @DuplicatorGroups({"duplicate_advice"})
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column
     * @DuplicatorGroups({"duplicate_advice"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @DuplicatorGroups({"duplicate_advice"})
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @DuplicatorGroups({"duplicate_advice"})
     */
    private $description;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", name="is_common")
     */
    private $common = true;
    
    // Getters
    // Setters
    // ...
}
```

Duplicate a product
-------------------
```php
<?php

    use App\Entity\Advice;
    use App\Entity\Category;
    use App\Entity\Person;
    use App\Entity\Product;
    use MickPrev\Duplicator\ChainDuplicator;
    use MickPrev\Duplicator\DefaultDuplicator;
    use MickPrev\Duplicator\DuplicatorInterface;
    use MickPrev\Duplicator\IterableDuplicator;
    use MickPrev\Duplicator\ObjectDuplicator;

    $duplicator = new ChainDuplicator(
        [
            new IterableDuplicator(),
            new ObjectDuplicator(),
            new DefaultDuplicator(),
        ]
    );

    $originalAdvice = new Advice();
    $originalAuthor = new Person();
    $originalCategory = new Category();

    $originalProduct = new Product();
    $originalProduct->setTitle('Title');
    $originalProduct->addAdvice($originalAdvice);
    $originalProduct->setAuthor($originalAuthor);
    $originalProduct->setCategory($originalCategory);

    // What I want:
    // - Duplicate the original product and original advice.
    // - Keep the original category.
    // - Do not keep the author.
    $newProduct = $duplicator->duplicate($originalProduct, ['groups' => ['duplicate_product', 'duplicate_advice']]);
```
