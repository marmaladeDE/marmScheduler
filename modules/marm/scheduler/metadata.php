<?php
/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'            => 'scheduler',
    'title'         => 'marmalade :: Scheduler',
    'description'   => 'Schedule Scripts',
    'thumbnail'     => 'marmalade.jpg',
    'version'       => '0.2',
    'author'        => 'marmalade GmbH :: Jens Richter, Joscha Krug',
    'url'           => 'http://www.marmalade.de',
    'email'         => 'support@marmalade.de',
    'extend'        => array(
        'oxmaintenance'     => 'marm/scheduler/extends/marm_oxmaintenance'
    ),
    'files'         => array(
        'marm_events'       => 'marm/scheduler/core/marm_events.php',
        'marm_scheduler'    => 'marm/scheduler/controllers/admin/marm_scheduler.php',
        'job_interface'     => 'marm/scheduler/interfaces/job_interface.php',
        'scheduler'         => 'marm/scheduler/core/scheduler.php'
    ),
    'templates'     => array(
        'marmScheduler.tpl' => 'marm/scheduler/views/admin/marmScheduler.tpl'
    ),
    'events'       => array(
        'onActivate'   => 'marm_events::onActivate'
    )
);
