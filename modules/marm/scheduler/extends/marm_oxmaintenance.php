<?php
/**
 * Created by PhpStorm.
 * User: Robin Lehrmann
 * Email: robin@snow-hamster.de
 * Date: 14.10.14
 * Time: 22:50
 */

class marm_oxmaintenance extends marm_oxmaintenance_parent {

    /**
     * @return mixed
     */
    public function execute()
    {
        $scheduler = scheduler::getInstance();
        $scheduler->run();
        return parent::execute();
    }
} 