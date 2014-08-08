zf1-empty
=========

A hack to prevent composer from including multiple Zend Frameworks. For example Magento already has ZF1 in it's library/ path. If you use composer to require a package that in turn requires ZF1, composer doens't know that ZF1 is already provided by Magento. In that case, just require this package in your project's composer.json to prevent any of your dependencies from requiring new ZF1s
