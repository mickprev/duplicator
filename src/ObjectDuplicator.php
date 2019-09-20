<?php

/*
 * This file is part of the duplicator project.
 *
 * (c) Mick Prev <support@mickprev.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MickPrev\Duplicator;

use MickPrev\Duplicator\Exception\RootObjectImmutableException;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
class ObjectDuplicator implements DuplicatorInterface, DuplicatorAwareInterface
{
    use DuplicatorAwareTrait;

    /** @var ClassInfoInterface */
    private $classInfo;
    /** @var PropertyAccessorInterface */
    private $propertyAccessor;

    public function __construct(?ClassInfoInterface $classInfo = null, ?PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->classInfo = $classInfo ?: new ClassInfo();
        $this->propertyAccessor = $propertyAccessor ?: new PropertyAccessor();
    }

    public function duplicate($object, array $context = [])
    {
        if (!($properties = $this->classInfo->getProperties($object, $context))) {
            if (0 === $context['depth']) {
                throw new RootObjectImmutableException();
            }

            return $object;
        }

        $context['currentObject'] = $object;
        $newObject = $this->classInfo->newInstance($object);

        foreach ($properties as $property) {
            $propertyPath = $property->getName();

            if (
                !$this->propertyAccessor->isReadable($object, $propertyPath)
                || !$this->propertyAccessor->isWritable($newObject, $propertyPath)
            ) {
                continue;
            }

            $context['propertyPath'] = $propertyPath;

            $this->propertyAccessor->setValue(
                $newObject,
                $propertyPath,
                $this->duplicator->duplicate($this->propertyAccessor->getValue($object, $propertyPath), $context)
            );
        }

        return $newObject;
    }

    public function supports($value, array $context = []): bool
    {
        return \is_object($value);
    }
}
