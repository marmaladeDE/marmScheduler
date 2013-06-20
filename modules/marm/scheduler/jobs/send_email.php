<?php

/**
 * Class to demonstrate the scheduler
 */
class send_email {
    
    /**
     * You'll always need a public function run,
     * as this is the method we will call.
     */
    public function run()
    {
        $oEmail = oxNew('oxEmail');
        $oEmail->sendEmail('support@marmalade.de','Scheduler Demo','Hey Guys, i tried your scheduler! Great job.');
        
        $ret['success'] = 1;
        $ret['message'] = 'Everything went fine! Mail was sent.';
        
        return $ret;
    }
}
