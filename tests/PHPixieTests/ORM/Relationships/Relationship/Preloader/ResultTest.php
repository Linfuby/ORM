<?php

namespace PHPixieTests\ORM\Relationships\Relationship\Preloader;

/**
 * @coversDefaultClass \PHPixie\ORM\Relationships\Relationship\Preloader\Result
 */
abstract class ResultTest extends \PHPixieTests\ORM\Relationships\Relationship\PreloaderTest
{
    protected $models = array();
    protected $preloadedModels = array();
    protected $loaders;
    protected $side;
    
    public function setUp()
    {
        $this->side = $this->side();
        parent::setUp();
    }
    
    /**
     * @covers ::getModel
     * @covers ::<protected>
     */
    public function testGetModel()
    {
        $this->prepareMap();
        $this->prepareLoader();
        $models = array_reverse($this->preloadedModels, true);
        $i = count($models) - 1;
        foreach($models as $id => $model) {
            $this->assertEquals($model, $this->preloader->getModel($id));
            $i--;
        }
    }
    
    protected function prepareLoader()
    {
        $modelOffsets = array_values($this->preloadedModels);
        $this->loader
                ->expects($this->any())
                ->method('getByOffset')
                ->will($this->returnCallback(function($offset) use ($modelOffsets) {
                    return $modelOffsets[$offset];
                }));
    }
    
    protected function mapConfig($config, $data)
    {
        $config
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function($key) use($data){
                return $data[$key];
            }));
        foreach($data as $key => $value)
            $config->$key = $value;
    }
    
    protected function side()
    {
        $config = $this->getConfig();
        $this->mapConfig($config, $this->configData);
        $side = $this->getSide();
        $this->method($side, 'config', $config, array());
        return $side;
    }
    
    protected function getReusableResult()
    {
        return $this->quickMock('\PHPixie\ORM\Steps\Step\Query\Result\Reusable');
    }
    
    protected function getDatabaseModel()
    {
        return $this->abstractMock('\PHPixie\ORM\Repositories\Type\Database\Model');
    }
    
    protected function getDatabaseRepository()
    {
        return $this->abstractMock('\PHPixie\ORM\Repositories\Type\Database');
    }
    
    abstract protected function getSide();
    abstract protected function getConfig();
    abstract protected function prepareMap();
}