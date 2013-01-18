<?php


class sfGuardUserPermissionTable extends PluginsfGuardUserPermissionTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('sfGuardUserPermission');
    }
}