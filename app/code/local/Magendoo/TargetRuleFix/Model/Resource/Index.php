<?php

class Magendoo_TargetRuleFix_Model_Resource_Index extends Enterprise_TargetRule_Model_Resource_Index
{

   public function saveProductIndex($ruleId, $productId, $storeId)
    {
        /** @var $targetRule Enterprise_TargetRule_Model_Resource_Rule */
        $targetRule = Mage::getResourceSingleton('targetrulefix/rule');
        //$targetRule->bindRuleToEntity($ruleId, $productId, 'product');
        $targetRule->bindRuleToEntity($ruleId, $productId, 'product', false); //@carco: copied from EE 1.13


        return $this;
    }

    public function reindexAll() {

        $adapter = $this->_getWriteAdapter();

        $tables = array(
            'customersegment',
            'product',
            'index',
            'index_crosssell',
            'index_related',
            'index_upsell'
        );
        foreach($tables as $table)  {
            $adapter->truncate($this->getTable('enterprise_targetrule/'.$table));
        }

        $rules = Mage::getModel('enterprise_targetrule/rule')->getCollection();
        foreach($rules as $rule) {
            $rule->save(); //dirty
        }
    }


}
