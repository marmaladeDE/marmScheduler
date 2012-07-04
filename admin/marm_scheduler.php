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
        
    public function getTasks(){
        $now = time();
        $sQuery = 'SELECT * FROM marmSchedulerTasks';
        $this->_oDb = oxDb::getDb(oxdb::FETCH_MODE_ASSOC);
        $oRes = $this->_oDb->Execute($sQuery);
        $tasks = array();
        if ($oRes != false && $oRes->recordCount() > 0){
            while (!$oRes->EOF){
                $task = array();
                $task['id'] = $oRes->fields["id"];
                $task['class'] = $oRes->fields["class"];
                $task['path'] = $oRes->fields["path"];
                $task['description'] = $oRes->fields["description"];
                $task['active'] = $oRes->fields["active"];
                $task['starttime'] = $oRes->fields["starttime"];
                $task['timeinterval'] = $oRes->fields["timeinterval"];
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
                                                .'\',active='.$task['active']
                                                .',starttime='.$task['starttime']
                                                .',timeinterval='.$task['timeinterval']
                                                .' WHERE id='.$key;
            $oDb->Execute($sQuery);
        }
        if (!empty($aParams['new']['class'])){
            $task=$aParams['new'];
            $sQuery = 'INSERT INTO marmSchedulerTasks (class, path, description, active, starttime, timeinterval) VALUES (\''.$task['class']
                                                .'\',\''.$task['path']
                                                .'\',\''.$task['description']
                                                .','.$task['active']
                                                .','.$task['starttime']
                                                .','.$task['timeinterval']
                                                .'\')';
                                        $oDb->Execute($sQuery);
        }

    }
}

