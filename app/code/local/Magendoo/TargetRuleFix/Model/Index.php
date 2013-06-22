<?php

class  Magendoo_TargetRuleFix_Model_Index extends Enterprise_TargetRule_Model_Index
{
    protected $_isVisible = true; //make this visible in Index Management (magento hides because not working)


    protected function _construct()
    {
        $this->_init('targetrulefix/index');
    }


    public function getDescription()
    {
        return Mage::helper('enterprise_targetrule')->__('Rule-Based Product Relations');
    }

    //add reindexAll - regenerate all matched IDS for all rules

    public function reindexAll() {

        //clean all tables
        $adapter = $this->_getResource()->reindexAll();


    }

}
