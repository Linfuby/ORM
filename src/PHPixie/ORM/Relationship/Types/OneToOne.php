<?php

namespace PHPixie\ORM\Relationships\Types;

class OneToMany extends PHPixie\ORM\Relationship\Type
{
    public function config($config)
    {
        return new OneToMany\Side\Config($config);
    }

    public function side($type, $config)
    {
        return new OneToMany\Side($this, $type, $config);
    }

    public function buildHandler()
    {
        return new OneToMany\Handler();
    }

    protected function sideTypes($config)
    {
        return array('owner', 'items');
    }

}
