<?php

namespace PHPixieTests\ORM\Relationships\Relationship\Implementation\Preloader\Result;

/**
 * @coversDefaultClass \PHPixie\ORM\Relationships\Relationship\Implementation\Preloader\Result\Multiple
 */
abstract class MultipleTest extends \PHPixieTests\ORM\Relationships\Relationship\Implementation\Preloader\ResultTest
{
    protected function prepareMultiplePreloader($ids)
    {
        $loader = $this->getReusableResult();
        $this->method($this->loaders, 'multiplePreloader', $loader, array($this->preloader, $ids), 0);
        return $loader;
    }
}