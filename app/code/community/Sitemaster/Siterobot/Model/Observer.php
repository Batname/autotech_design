<?php

class Sitemaster_Siterobot_Model_Observer
{

    public function startRobot()
    {
        $model = Mage::getModel('sitemaster_siterobot/parser');
        $model->getContent();

        return $this;
    }

}
