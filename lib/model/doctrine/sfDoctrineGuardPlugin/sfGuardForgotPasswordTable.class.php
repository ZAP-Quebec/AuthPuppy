<?php


class sfGuardForgotPasswordTable extends PluginsfGuardForgotPasswordTable
{
    
    public static function getInstance()
    {
        return Doctrine_Core::getTable('sfGuardForgotPassword');
    }
}