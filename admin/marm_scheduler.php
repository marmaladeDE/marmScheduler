<?php
/**
 * Automically generated file.
 */
class marm_scheduler extends oxAdminView
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'marmScheduler.tpl';
        
    const CONFIG_ENTRY_NAME = 'marm_scheduler_config';
    
    public function getTasks(){
        
        $now = time();
        $sQuery = 'SELECT * FROM marmSchedulerTasks';

        $this->_oDb = oxDb::getDb();
        $oRes = $this->_oDb->Execute($sQuery);
        $tasks = array();

        if ($oRes != false && $oRes->recordCount() > 0){
            while (!$oRes->EOF){
                $task = array();
                $task['id'] = $oRes->fields[0];
                $task['active'] = $oRes->fields[1];
                $task['path'] = $oRes->fields[2];
                $task['class'] = $oRes->fields[3];
                $task['description'] = $oRes->fields[4];
                $task['starttime'] = $oRes->fields[5];
                $task['timeinterval'] = $oRes->fields[6];
                $task['lastrun'] = $oRes->fields[7];
                $tasks[] = $task;
                $oRes->moveNext();
            }
        }
        return $tasks;
    }
    
    /**
     * Saves the Tasks
     * @return void
     */
    public function save()
    {
        $oDb = oxDb::getDb();
        $aParams = oxConfig::getParameter( "editval" );
        foreach ($aParams as $key => $task){
            if(!is_int($key)){break;}
            $sQuery = 'UPDATE marmSchedulerTasks SET class =\''.$task['class']
                                                .'\',path=\''.$task['path']
                                                .'\',description=\''.$task['description']
                                                .'\',active=\''.$task['active']
                                                .'\',starttime=\''.$task['starttime']
                                                .'\',timeinterval=\''.$task['timeinterval']
                                                .'\' WHERE id='.$key;
            $oDb->Execute($sQuery);
        }
        if (!empty($aParams['new']['class'])){
            $task=$aParams['new'];
            
            $sQuery = 'INSERT INTO marmSchedulerTasks (class, path, description, active, starttime, timeinterval) VALUES (\''.$task['class']
                                                .'\',\''.$task['path']
                                                .'\',\''.$task['description']
                                                .'\',\''.$task['active']
                                                .'\',\''.$task['starttime']
                                                 .'\',\''.$task['timeinterval']
                                                .'\')';
            $oDb->Execute($sQuery);
        }

    }
    
    public function isSchedulerRunning(){
        $config = oxConfig::getInstance()->getShopConfVar(self::CONFIG_ENTRY_NAME);
        return !$config['locked'];
    }
    
    public function unlockScheduler(){
        $config = oxConfig::getInstance()->getShopConfVar(self::CONFIG_ENTRY_NAME);
        if($config['locked']==0){
            return true;
        }
        $id = $this->_getLastStartedTask();
        $this->_deactivateTask($id);
        $config['locked'] = 0;
        oxConfig::getInstance()->saveShopConfVar('aarr',self::CONFIG_ENTRY_NAME,  $config);
        return true;
    }
    
    private function _getLastStartedTask(){
        $oDb = oxDb::getDb();
        $sQuery = 'SELECT id FROM marmSchedulerLog WHERE status=\'2\' ORDER BY id DESC';
        $id = $oDb->getOne($sQuery);
        return $id;
    }
    private function _deactivateTask($id){
        $sQuery = 'UPDATE marmSchedulerTasks SET active = 0 WHERE id ='.$id;
        $this->_oDb->Execute($sQuery);
    }
}


