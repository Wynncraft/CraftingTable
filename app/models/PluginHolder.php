<?php

class PluginHolder extends Eloquent {

    /**
     * Plugins
     *
     * @return object
     */
    public function plugins()
    {
        return $this->morphMany('PluginHolderPlugin', 'pluginholder');
    }

}