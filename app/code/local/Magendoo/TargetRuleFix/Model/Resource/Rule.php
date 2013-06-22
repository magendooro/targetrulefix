<?php
class Magendoo_TargetRuleFix_Model_Resource_Rule extends Enterprise_TargetRule_Model_Resource_Rule
{

    //copied from magento EE 1.13, add 4th parameters


    /**
     * Bind specified rules to entities  (add 4th parameter, like in EE 1.13, @carco)
     *
     * @param array|int|string $ruleIds
     * @param array|int|string $entityIds
     * @param string $entityType
     * @param bool $deleteOldResults
     *
     * @return Mage_Rule_Model_Resource_Abstract
     */
    public function bindRuleToEntity($ruleIds, $entityIds, $entityType, $deleteOldResults = true)
    {
        $version = Mage::getVersionInfo();
        $applyHack = !$deleteOldResults || ($version['major']==1 && $version['minor']<=12);


        if(!$applyHack) {
            return parent::bindRuleToEntity($ruleIds,$entityIds,$entityType,$deleteOldResults);
        }

        if (empty($ruleIds) || empty($entityIds)) {
            return $this;
        }
        $adapter    = $this->_getWriteAdapter();
        $entityInfo = $this->_getAssociatedEntityInfo($entityType);

        if (!is_array($ruleIds)) {
            $ruleIds = array((int) $ruleIds);
        }
        if (!is_array($entityIds)) {
            $entityIds = array((int) $entityIds);
        }

        $data  = array();
        $count = 0;

        $adapter->beginTransaction();

        try {
            foreach ($ruleIds as $ruleId) {
                foreach ($entityIds as $entityId) {
                    $data[] = array(
                        $entityInfo['entity_id_field'] => $entityId,
                        $entityInfo['rule_id_field'] => $ruleId
                    );
                    $count++;
                    if (($count % 1000) == 0) {
                        $adapter->insertOnDuplicate(
                            $this->getTable($entityInfo['associations_table']),
                            $data,
                            array($entityInfo['rule_id_field'])
                        );
                        $data = array();
                    }
                }
            }
            if (!empty($data)) {
                $adapter->insertOnDuplicate(
                    $this->getTable($entityInfo['associations_table']),
                    $data,
                    array($entityInfo['rule_id_field'])
                );
            }
           if($deleteOldResults) { //GET from EE 1.13, fix problem with targetrule which remove other rules when save product
                $adapter->delete($this->getTable($entityInfo['associations_table']),
                    $adapter->quoteInto($entityInfo['rule_id_field']   . ' IN (?) AND ', $ruleIds) .
                    $adapter->quoteInto($entityInfo['entity_id_field'] . ' NOT IN (?)',  $entityIds)
                );
            }
        } catch (Exception $e) {
            $adapter->rollback();
            throw $e;

        }

        $adapter->commit();

        return $this;
    }



}
