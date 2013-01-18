<?php


class sfGuardUserGroupTable extends PluginsfGuardUserGroupTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('sfGuardUserGroup');
    }
}