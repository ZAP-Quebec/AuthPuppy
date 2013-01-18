<?php


class sfGuardGroupPermissionTable extends PluginsfGuardGroupPermissionTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('sfGuardGroupPermission');
    }
}