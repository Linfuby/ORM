<?php

namespace PHPixie\ORM\Data\Types;

class Map extends \PHPixie\ORM\Data\Type\Implementation implements \PHPixie\ORM\Data\Type\Diffable
{
    protected $dataBuilder;
    protected $data;
    protected $originalData;
    
    public function __construct($dataBuilder, $data)
    {
        $this->dataBuilder = $dataBuilder;
        $this->data = (array) $data;
        $this->originalData = $data;
    }
    
    public function get($key)
    {
        return $this->data[$key];
    }
    
    protected function setValue($key, $value)
    {
        $this->data[$key] = $value;
    }
    
    public function data()
    {
        return (object) $this->data;
    }
    
    public function originalData()
    {
        return $this->originalData;
    }
    
    public function setCurrentAsOriginal()
    {
        $this->originalData = $this->data();
    }
    
    public function diff()
    {
        $currentData = $this->data;
        $originalData = (array) $this->originalData;
        $unset = array_diff(array_keys($originalData), array_keys($currentData));
        $data = array_fill_keys($unset, null);

        foreach ($currentData as $key => $value) {
            if (!array_key_exists($key, $originalData) || $value !== $originalData[$key])
                $data[$key] = $value;
        }

        return $this->dataBuilder->diff((object) $data);
    }
}