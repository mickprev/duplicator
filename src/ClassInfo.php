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

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use MickPrev\Duplicator\Annotation\Groups;
use MickPrev\Duplicator\Exception\ClassNotInstantiableException;
use MickPrev\Duplicator\Exception\MissingGroupsContextException;
use MickPrev\Duplicator\Exception\RuntimeException;

/**
 * @author Mick Prev <support@mickprev.fr>
 */
class ClassInfo implements ClassInfoInterface
{
    /** @var array */
    private $properties = [];
    /** @var Reader|null */
    private $reader;
    /** @var string */
    private $groupsAnnotation = Groups::class;

    public function __construct(?Reader $reader = null)
    {
        $this->reader = $reader;
    }

    public function getReader(): Reader
    {
        if ($this->reader) {
            return $this->reader;
        }

        $this->reader = new AnnotationReader();
        AnnotationRegistry::registerLoader('class_exists');

        return $this->reader;
    }

    public function setGroupsAnnotation(string $class): void
    {
        if (!\class_exists($class)) {
            throw new RuntimeException("Class $class does not exist.");
        }

        if (!\method_exists($class, 'getGroups')) {
            throw new RuntimeException("Class $class must implement the accessible getGroups method.");
        }

        $this->groupsAnnotation = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function newInstance($classOrObject): object
    {
        $reflectionClass = new \ReflectionClass($classOrObject);

        // @todo Support arguments in the constructor.
        if (!$this->isInstantiable($reflectionClass)) {
            throw new ClassNotInstantiableException($reflectionClass->getName());
        }

        return $reflectionClass->newInstance();
    }

    /**
     * {@inheritdoc}
     */
    public function getProperties($classOrObject, array $context, bool $cache = true): array
    {
        $groupsToSearch = !empty($context['groups']) ? (array) $context['groups'] : [];
        if (!$groupsToSearch) {
            throw new MissingGroupsContextException();
        }

        $reflectionClass = new \ReflectionClass($classOrObject);
        $cacheKey = $this->getCacheKey($reflectionClass->getName(), $groupsToSearch);

        if ($cache && isset($this->properties[$cacheKey])) {
            return $this->properties[$cacheKey];
        }

        $reader = $this->getReader();
        $this->properties[$cacheKey] = [];

        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            /** @var Groups|null $groups */
            $groups = $reader->getPropertyAnnotation($reflectionProperty, $this->groupsAnnotation);
            if ($groups && \array_intersect($groupsToSearch, $groups->getGroups())) {
                $this->properties[$cacheKey][] = $reflectionProperty;
            }
        }

        return $this->properties[$cacheKey];
    }

    private function isInstantiable(\ReflectionClass $reflectionClass): bool
    {
        if ($constructor = $reflectionClass->getConstructor()) {
            foreach ($constructor->getParameters() as $parameter) {
                if (!$parameter->isDefaultValueAvailable()) {
                    return false;
                }
            }
        }

        return true;
    }

    private function getCacheKey(string $class, array $groupsToSearch): string
    {
        return md5(\serialize([$class, $groupsToSearch]));
    }
}
