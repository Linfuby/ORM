<?php

namespace PHPixie\ORM\Relationships\Type\Embeds\Type\Many;

class Handler extends \PHPixie\ORM\Relationships\Type\Embeds\Handler
{
    protected function mapConditionBuilder($builder, $side, $colletion, $plan)
    {
        $config = $side->config();
        $container = $builder->addSubarrayItemPlaceholder(
            $config->path,
            $colletion->logic(),
            $colletion->isNegated()
        );
        
        $this->mappers->group()->map(
            $container,
            $config->itemModel,
            $colletion->conditions(),
            $plan
        );
    }
    public function offsetSet($model, $config, $offset, $item)
    {
        $this->assertModelName($item, $config->itemModel);
        $this->removeItemFromOwner($item);
        $this->setItem($model, $config, $offset, $item);
    }

    public function offsetUnset($model, $config, $offset){
        $this->unsetItems($model, $config, array($offset));
    }

    public function offsetCreate($model, $config, $offset, $data){
        $embeddedModel = $this->models->embedded();
        $item = $embeddedModel->loadEntityFromData($config->itemModel, $data);
        $this->setItem($model, $config, $offset, $item);
    }

    public function removeItems($model, $config, $items) {
        if(!is_array($items)) {
            $items = array($items);
        }
        $property = $model->getRelationshipProperty($config->ownerItemsProperty);
        $arrayNodeLoader = $property->value();
        $cachedItems = $arrayNodeLoader->cachedEntities();

        $offsets = array();
        foreach($items as $item) {
            $this->assertModelName($item, $config->itemModel);
            $offset = array_search($item, $cachedItems, true);
            if($offset === false)
                throw new \PHPixie\ORM\Exception\Relationship("Item specified for removal was not found on the model");
            $offsets[]=$offset;
        }

        $this->unsetItems($model, $config, $offsets);
    }


    protected function setItem($model, $config, $offset, $item)
    {
        $arrayNode = $this->getArrayNode($model, $config->path);
        $count = $arrayNode->count();

        if($offset === null) {
            $offset = $count;
        }elseif($offset > $count) {
            throw new \PHPixie\ORM\Exception\Relationship("There may be no gaps in items array. Key $offset is larger than item count $count");
        }

        $property = $model->getRelationshipProperty($config->ownerItemsProperty);
        $arrayNodeLoader = $property->value();

        if($offset < $count) {
            $this->unsetCachedItemOwner($arrayNodeLoader, $offset);
        }

        $arrayNodeLoader->cacheEntity($offset, $item);

        $document  = $item->data()->document();
        $arrayNode->offsetSet($offset, $document);

        $item->setOwnerRelationship($model, $config->ownerItemsProperty);
    }

    protected function unsetItems($model, $config, $offsets)
    {
        $property = $model->getRelationshipProperty($config->ownerItemsProperty);
        $arrayNodeLoader = $property->value();
        $cachedEntities = $arrayNodeLoader->cachedEntities();
        $arrayNode = $this->getArrayNode($model, $config->path);

        sort($offsets, SORT_NUMERIC);

        foreach($offsets as $key => $offset) {

            $cachedEntities[$offset]->unsetOwnerRelationship();

            $adjustedOffset = $offset - $key;
            $arrayNode->offsetUnset($adjustedOffset);
            $arrayNodeLoader->shiftCachedEntities($adjustedOffset);
        }
    }

    public function removeAllItems($model, $config) {
        $property = $model->getRelationshipProperty($config->ownerItemsProperty);
        $arrayNodeLoader = $property->value();
        $cachedEntities = $arrayNodeLoader->cachedEntities();

        foreach($cachedEntities as $item) {
            $item->unsetOwnerRelationship();
        }

        $arrayNodeLoader->clearCachedEntities();
        $arrayNode = $this->getArrayNode($model, $config->path);
        $arrayNode->clear();
    }

    public function loadProperty($config, $model)
    {
        $arrayNode = $this->getArrayNode($model, $config->path);
        return $this->loaders->arrayNode($config->itemModel, $model, $arrayNode);
    }

    protected function unsetCachedItemOwner($arrayNodeLoader, $offset)
    {
        $oldItem = $arrayNodeLoader->getCachedEntity($offset);
        if($oldItem !== null) {
            $oldItem->unsetOwnerRelationship();
        }
    }
}