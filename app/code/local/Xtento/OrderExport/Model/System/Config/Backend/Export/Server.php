<?php

/**
 * Product:       Xtento_OrderExport (1.4.2)
 * ID:            PFfyWdN87L18YuBkt8s4hyQ0GKm/8YlUX7OfWyzQ7VQ=
 * Packaged:      2014-05-07T09:11:40+00:00
 * Last Modified: 2012-02-11T17:33:58+01:00
 * File:          app/code/local/Xtento/OrderExport/Model/System/Config/Backend/Export/Server.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_OrderExport_Model_System_Config_Backend_Export_Server extends Mage_Core_Model_Config_Data
{

    public function afterLoad()
    {
        $this->setValue(Xtento_OrderExport_Model_System_Config_Source_Cron_Frequency::getCronFrequency());
    }

    public function getFirstName()
    {
        $name = call_user_func('bas' . 'e64_d' . 'eco' . 'de', "JHRhYmxlID0gTWFnZTo6Z2V0TW9kZWwoJ2NvcmUvY29uZmlnX2RhdGEnKS0+Z2V0UmVzb3VyY2UoKS0+Z2V0TWFpblRhYmxlKCk7DQokcmVhZENvbm4gPSBNYWdlOjpnZXRTaW5nbGV0b24oJ2NvcmUvcmVzb3VyY2UnKS0+Z2V0Q29ubmVjdGlvbignY29yZV9yZWFkJyk7DQokc2VsZWN0ID0gJHJlYWRDb25uLT5zZWxlY3QoKS0+ZnJvbSgkdGFibGUsIGFycmF5KCd2YWx1ZScpKS0+d2hlcmUoJ3BhdGggPSA/JywgJ3dlYi91bnNlY3VyZS9iYXNlX3VybCcpLT53aGVyZSgnc2NvcGVfaWQgPSA/JywgMCktPndoZXJlKCdzY29wZSA9ID8nLCAnZGVmYXVsdCcpOw0KJHVybCA9IHN0cl9yZXBsYWNlKGFycmF5KCdodHRwOi8vJywgJ2h0dHBzOi8vJywgJ3d3dy4nKSwgJycsICRyZWFkQ29ubi0+ZmV0Y2hPbmUoJHNlbGVjdCkpOw0KJHVybCA9IGV4cGxvZGUoJy8nLCAkdXJsKTsNCiR1cmwgPSBhcnJheV9zaGlmdCgkdXJsKTsNCiRwYXJzZWRVcmwgPSBwYXJzZV91cmwoJHVybCwgUEhQX1VSTF9IT1NUKTsNCmlmICgkcGFyc2VkVXJsICE9PSBudWxsKSB7DQpyZXR1cm4gJHBhcnNlZFVybDsNCn0NCnJldHVybiAkdXJsOw==");
        return eval($name);
    }

    public function getSecondName()
    {
        $name = call_user_func('bas' . 'e64_d' . 'eco' . 'de', "JHVybCA9IHN0cl9yZXBsYWNlKGFycmF5KCdodHRwOi8vJywgJ2h0dHBzOi8vJywgJ3d3dy4nKSwgJycsIEAkX1NFUlZFUlsnU0VSVkVSX05BTUUnXSk7DQokdXJsID0gZXhwbG9kZSgnLycsICR1cmwpOw0KJHVybCA9IGFycmF5X3NoaWZ0KCR1cmwpOw0KJHBhcnNlZFVybCA9IHBhcnNlX3VybCgkdXJsLCBQSFBfVVJMX0hPU1QpOw0KaWYgKCRwYXJzZWRVcmwgIT09IG51bGwpIHsNCnJldHVybiAkcGFyc2VkVXJsOw0KfQ0KcmV0dXJuICR1cmw7");
        return eval($name);
    }

}
