<?php

namespace PHPixie\ORM\Relationships\Types\OneTo\Type\One\Property\Query;

class Item extends \PHPixie\ORM\Relationships\Types\OneTo\Type\One\Property\Query
{

    public function set($item)
    {
        $this->processSet($this->model, $item);
    }
}