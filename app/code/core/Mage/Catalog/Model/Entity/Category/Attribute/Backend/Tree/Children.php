<?php
/**
 * Catalog category tree_children attribute backend model
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Entity_Category_Attribute_Backend_Tree_Children extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    public function afterSave($object)
    {
        parent::afterSave($object);
        $tree = $object->getTreeModel()->getTree()
            ->load();
        $arrChildNodes = $tree->getNodeById($object->getId())
            ->getAllChildNodes();
        
        $nodeIds = array($object->getId());
        
        foreach ($arrChildNodes as $node) {
        	$nodeIds[] = $node->getId();
        }
        
        $object->setData($this->getAttribute()->getAttributeCode(), implode(',', $nodeIds));
        $this->getAttribute()->getEntity()
            ->saveAttribute($object, $this->getAttribute()->getAttributeCode());
        
        return $this;
    }
}
