<?php
/**
 * Created by PhpStorm.
 * User: Robin Lehrmann
 * Email: robin@snow-hamster.de
 * Date: 14.10.14
 * Time: 22:49
 */
interface job_interface
{
    /**
     * You'll always need a public function run,
     * as this is the method we will call.
     *
     * @return array
     */
    public function run();
}