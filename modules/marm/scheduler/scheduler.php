<?php

if (!function_exists('getShopBasePath')) {
    /**
     * Returns shop base path.
     *
     * @return string
     */
    function getShopBasePath()
    {
        return dirname(__FILE__).'/../../../';
    }
}

// START INCLUDE OXID FRAMEWORK BLOCK - DELETE IF NOT NEEDED

set_include_path(get_include_path() . PATH_SEPARATOR . getShopBasePath());

/**
 * Returns true.
 *
 * @return bool
 */
if ( !function_exists( 'isAdmin' )) {
    /**
     * @return bool
     */
    function isAdmin()
    {
        return false;
    }
}

error_reporting( E_ALL ^ E_NOTICE );

// custom functions file
include getShopBasePath() . 'modules/functions.php';
// Generic utility method file
require_once getShopBasePath() . 'core/oxfunctions.php';
// Including main ADODB include
require_once getShopBasePath() . 'core/adodblite/adodb.inc.php';

// END INCLUDE OXID FRAMEWORK BLOCK - DELETE IF NOT NEEDED

/**
 * Description of scheduler
 *
 * @author jens
 */
final class Scheduler {
    
    const CONFIG_ENTRY_NAME = 'marm_scheduler_config';
    
    private static $_instance = null;
    
    protected $_blLocked = 0;
    protected $_oDb = null;

    private function __construct() {}
    
    private function __clone() {}

    public static function getInstance(){
        if (null === self::$_instance){
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    public function run(){
        
        //check if scheduler is still running
        if ($this->_blLocked){
            return false;
        }
        
        //lock scheduler
        $this->_blLocked = 1;
     
        $tasks = $this->_getTasks();
        foreach ($tasks as $task) {
            if($task['path']){
                include $task['path'];
            }
            $class = oxNew($task['class']);
            $ret = $class->run();
            /*$ret= array();
            $ret['success']= 1;
            $ret['message']='debug';
            $ret['time']=time();
            $ret['runtime']=10;*/
            $this->_logTask($task['id'], $task['class'], $ret);
        }
        
        //unlock scheduler
        $this->_blLocked = 0;

    }
    
    private function _getTasks(){
        $now = time();
        $sQuery = 'SELECT * FROM marmSchedulerTasks WHERE active =\'1\' AND (lastrun + timeinterval) <= \''.$now.'\'';
        //$sQuery = 'SELECT * FROM marmSchedulerTasks';
        $this->_oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $oRes = $this->_oDb->Execute($sQuery);
        $tasks = array();
        if ($oRes != false && $oRes->recordCount() > 0){
            while (!$oRes->EOF){
                $task = array();
                $task['id'] = $oRes->fields["id"];
                $task['class'] = $oRes->fields["class"];
                $task['path'] = $oRes->fields["path"];
                $tasks[] = $task;
                $oRes->moveNext();
            }
        }
        return $tasks;
    }
    
    private function _logTask($id, $class, $array){
        var_dump('Test:logging');
        $sQuery = 'INSERT INTO marmSchedulerLog (taskid,class,success,message,time,runtime) VALUES ('.$id
                    .',\''.$class
                    .'\','.$array['success']
                    .',\''.$array['message']
                    .'\','.$array['time']
                    .','.$array['runtime']
                    .')';
            var_dump($sQuery);
        $this->_oDb->Execute($sQuery);
    }

}
$scheduler = Scheduler::getInstance();
$scheduler->run();
?>

