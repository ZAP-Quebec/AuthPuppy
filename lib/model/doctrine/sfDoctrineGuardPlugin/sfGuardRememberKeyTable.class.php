<?php


class sfGuardRememberKeyTable extends PluginsfGuardRememberKeyTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('sfGuardRememberKey');
    }
}