<?php

class PluginHolder extends Moloquent  {

    protected $connection = 'mongodb';

    /**
     * Plugins
     *
     * @return object
     */
    public function plugins()
    {
        return $this->embedsMany('PluginHolderPlugin');
    }

}