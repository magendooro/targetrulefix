<?php


class Magendoo_TargetRuleFix_Model_Observer extends Enterprise_TargetRule_Model_Resource_Index {

    //called in Mage_Index_Model_Process::reindexAll
    // Mage::dispatchEvent('after_reindex_process_' . $this->getIndexerCode());


    public function reindexAll() {

        //clean
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
            echo 'index '.$rule->getId(),"\n";
            $rule->save(); //dirty
        }


    }

}