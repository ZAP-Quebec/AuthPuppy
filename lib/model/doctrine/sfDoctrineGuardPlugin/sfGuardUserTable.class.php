<?php


class sfGuardUserTable extends PluginsfGuardUserTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('sfGuardUser');
    }
}