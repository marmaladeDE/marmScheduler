<?php
/**
 * Created by PhpStorm.
 * User: robin
 * Date: 14.10.14
 * Time: 23:12
 */

class marm_events {

    public static function onActivate()
    {
        oxDb::getDb()->execute('CREATE TABLE IF NOT EXISTS marmSchedulerTasks
                                (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                 active TINYINT(1) NOT NULL,
                                 path VARCHAR(255) NOT NULL,
                                 class VARCHAR(255) NOT NULL,
                                 description VARCHAR(255) NOT NULL,
                                 starttime INT NOT NULL,
                                 timeinterval INT NOT NULL,
                                 lastrun INT NOT NULL);');
        oxDb::getDb()->execute('CREATE TABLE IF NOT EXISTS marmSchedulerLog
                                (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                 taskid INT NOT NULL,
                                 class VARCHAR(255) NOT NULL,
                                 status TINYINT(1) NOT NULL,
                                 message VARCHAR(255) NOT NULL,
                                 time INT NOT NULL,
                                 runtime INT NOT NULL);');
    }
} 