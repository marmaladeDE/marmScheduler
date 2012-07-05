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
        $config = oxConfig::getInstance()->getShopConfVar(self::CONFIG_ENTRY_NAME);
        if ($config['locked']){
            echo 'scheduler locked';
            return false;
        }
        
        //lock scheduler
        $config['locked'] = 1;
        oxConfig::getInstance()->saveShopConfVar('aarr',self::CONFIG_ENTRY_NAME,  $config);
        
        $tasks = $this->_getTasks();
        foreach ($tasks as $task) {
            try {
                if($task['path']){
                    if(file_exists(getShopBasePath() . $task['path'])){
                        include getShopBasePath() . $task['path'];
                    }
                }
                $class = oxNew($task['class']);
                if (method_exists($class, 'run')){
                    $ret = $class->run();
                    $this->_logTask($task['id'], $task['class'], $ret);
                } else {
                    $message = 'function run does not exist';
                    $this->_logError($task['id'], $task['class'], $message);
                    $this->_deactivateTask($task['id']);
                }
            }catch(Exception $e){
                $message= 'exception: '.$e->getMessage();
                $this->_logError($task['id'], $task['class'], $message);
                $this->_deactivateTask($task['id']);
            }

        }
        
        //unlock scheduler
        $config['locked'] = 0;
        oxConfig::getInstance()->saveShopConfVar('aarr',self::CONFIG_ENTRY_NAME,  $config);

    }
    
    private function _getTasks(){
        $now = time();
        $sQuery = 'SELECT * FROM marmSchedulerTasks WHERE active = 1 AND (lastrun + timeinterval) <= \''.$now.'\'';
        //$sQuery = 'SELECT * FROM marmSchedulerTasks';
        $this->_oDb = oxDb::getDb();
        $oRes = $this->_oDb->Execute($sQuery);
        $tasks = array();
        if ($oRes != false && $oRes->recordCount() > 0){
            while (!$oRes->EOF){
                $task = array();
                $task['id'] = $oRes->fields[0];
                $task['class'] = $oRes->fields[3];
                $task['path'] = $oRes->fields[2];
                $tasks[] = $task;
                $oRes->moveNext();
            }
        }
        return $tasks;
    }
    
    private function _logTask($id, $class, $array){
        $now =time();
        $sQuery = 'INSERT INTO marmSchedulerLog (taskid,class,success,message,time,runtime) VALUES ('.$id
                    .',\''.$class
                    .'\','.$array['success']
                    .',\''.$array['message']
                    .'\','.$array['time']
                    .','.$array['runtime']
                    .')';
        $this->_oDb->Execute($sQuery);
        $sQuery = 'UPDATE marmSchedulerTasks SET lastrun ='.$now.' WHERE id ='.$id;
        $this->_oDb->Execute($sQuery);
    }
    
    private function _logError($id, $class, $message){
        $now =time();
        $sQuery = 'INSERT INTO marmSchedulerLog (taskid,class,success,message,time,runtime) VALUES ('.$id
                    .',\''.$class
                    .'\',0,\''.$message
                    .'\','.$now
                    .',0)';
        $this->_oDb->Execute($sQuery);
    }
    
    private function _deactivateTask($id){
        $sQuery = 'UPDATE marmSchedulerTasks SET active = 0 WHERE id ='.$id;
        $this->_oDb->Execute($sQuery);
    }

}
$scheduler = Scheduler::getInstance();
$scheduler->run();
?>


