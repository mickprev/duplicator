Add business logic
====================

In some case, you want to duplicate or not a value according to a business logic, so you will have to create your duplicator.
If I keep the use case, I wish duplicate the advice only if the advice is a common advice (common = true).

```php
<?php

// src/Duplicator/AdviceDuplicator.php

declare(strict_types=1);

namespace App\Duplicator;

use App\Entity\Advice;
use MickPrev\Duplicator\DuplicatorAwareInterface;
use MickPrev\Duplicator\DuplicatorAwareTrait;
use MickPrev\Duplicator\DuplicatorInterface;

final class AdviceDuplicator implements DuplicatorInterface, DuplicatorAwareInterface
{
    private const ALREADY_CALLED = 'advice_duplicator_already_called';

    use DuplicatorAwareTrait;

    /**
     * @param Advice $value
     */
    public function duplicate($value, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;

        return $value->common ? $this->duplicator->duplicate($value, $context) : null;
    }

    public function supports($value, array $context = []): bool
    {
        return \is_object($value) && $value instanceof Advice && !isset($context[self::ALREADY_CALLED]);
    }
}
```
