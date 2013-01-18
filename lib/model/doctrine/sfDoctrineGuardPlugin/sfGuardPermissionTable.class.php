<?php


class sfGuardPermissionTable extends PluginsfGuardPermissionTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('sfGuardPermission');
    }
}