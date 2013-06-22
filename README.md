Fix some bugs on Magento EE 1.12 in TargetRule Module

http://magento.stackexchange.com/questions/1143/target-rule-upsells


- On product save, all matched products are deleted (except current product)
- Indexer does not work (shell/indexer.php --reindex targetrule do nothing)

