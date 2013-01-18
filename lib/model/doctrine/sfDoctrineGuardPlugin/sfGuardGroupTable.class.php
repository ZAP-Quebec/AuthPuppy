<?php


class sfGuardGroupTable extends PluginsfGuardGroupTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('sfGuardGroup');
    }
}