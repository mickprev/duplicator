Use library with Symfony
========================

Register the bundle (with Symfony 4)
------------------------------------
```php
// config/bundles.php

return [
    ...
    MickPrev\Duplicator\Bridge\Symfony\Bundle\MickPrevDuplicatorBundle::class => ['all' => true],
];
```

Configure the bundle (with Symfony 4)
------------------------------

The bundle allows you to configure your own groups annotation, it just has to contain the getGroups method.
For instance, if you use the Serialization groups:

```yaml
# config/packages/mick_prev_duplicator.yaml

mick_prev_duplicator: 
    groups_annotation: Symfony\Component\Serializer\Annotation\Groups
```

Use Duplicator in (by example) a controller
------------------------------

```php
<?php
    
// src/Action/DuplicateController.php

namespace App\Controller;

use App\Repository\ProductRepository;
use MickPrev\Duplicator\DuplicatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DuplicateController
{
    /** @var DuplicatorInterface */
    private $duplicator;
    /** @var ProductRepository */
    private $productRepository;
    
    public function __construct(DuplicatorInterface $duplicator, ProductRepository $productRepository)
    {
        $this->duplicator = $duplicator;
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/duplicate/product/{productId}", name="app_duplicate_product")
     */
    public function duplicateProduct(string $productId)
    {
        $originalProduct = $this->productRepository->find($productId);
        
        if (!$originalProduct) {
            // Throw exception if product doesn't exists.
        }
        
        $newProduct = $this->duplicator->duplicate($originalProduct, ['groups' => ['duplicate_product', 'duplicate_advice']]);
                
        // Save new product.
        // Return new product.
    }
}
```

Configure a new duplicator for business logic
---------------------------------------------

If service autowiring and autoconfiguration are enabled (it's the case by default), you are done!

Otherwise, you need to register the corresponding service and add the mick_prev.duplicator tag to it.
 
```yaml
# config/services.yaml

services:
    # ...
    App\Duplicator\AdviceDuplicator: ~
        # Uncomment only if autoconfiguration is disabled
        #tags: [ 'mick_prev.duplicator' ]
```

