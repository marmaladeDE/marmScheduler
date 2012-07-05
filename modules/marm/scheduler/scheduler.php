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
        //var_dump($tasks);
        foreach ($tasks as $task) {
            try {
                if($task['path']){
                    if(file_exists(getShopBasePath() . $task['path'])){
                        include getShopBasePath() . $task['path'];
                    }
                }
                $class = oxNew($task['class']);
                if (method_exists($class, 'run')){
                    $this->_logTask($task, null, true);
                    $start = time();
                    $ret = $class->run();
                    $ret['runtime'] = time()-$start;
                    $this->_logTask($task, $ret);
                } else {
                    $message = 'function run does not exist';
                    $this->_logError($task, $message);
                    $this->_deactivateTask($task['id']);
                }
            }catch(Exception $e){
                $message= $e->getMessage();
                $this->_logError($task, $message);
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
    
    private function _logTask($task, $array= null, $blStart = false){
        $now =time();
        if($blStart){
            $sQuery = 'INSERT INTO marmSchedulerLog (taskid,class,status,message,time)'
                        .' VALUES (\''.$task['id']
                                .'\',\''.$task['class']
                                .'\',\'2'
                                .'\',\'starting'
                                .'\',\''.$now
                                .'\')';
        } else {
            $sQuery = 'INSERT INTO marmSchedulerLog (taskid,class,status,message,time,runtime)'
                    .' VALUES (\''.$task['id']
                        .'\',\''.$task['class']
                        .'\',\''.$array['success']
                        .'\',\''.$array['message']
                        .'\',\''.$now
                        .'\',\''.$array['runtime']
                        .'\')';
        }
        //var_dump($sQuery);
        $this->_oDb->Execute($sQuery);
        if(!$blStart){
            $sQuery = 'UPDATE marmSchedulerTasks SET lastrun ='.$now.' WHERE id ='.$id;
            $this->_oDb->Execute($sQuery);
        }
    }
    
    private function _logError($task, $message){
        $now =time();
        $sQuery = 'INSERT INTO marmSchedulerLog (taskid,class,status,message,time)'
                    .' VALUES (\''.$task['id']
                            .'\',\''.$task['class']
                            .'\',\'0\',\''.$message
                            .'\',\''.$now
                            .'\')';
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


