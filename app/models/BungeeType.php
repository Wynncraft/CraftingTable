<?php

class BungeeType extends PluginHolder {

    protected $connection = 'mongodb';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'bungeetypes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];

    public static function boot() {
        parent::boot();

        BungeeType::deleting(function($bungeetype) {
            foreach(Network::all() as $network) {
                foreach ($network->bungeetypes()->where('bungee_type_id', '=', $bungeetype->id)->get() as $bungeeType) {
                    $bungeeType->delete();
                }
                foreach($network->bungees() as $bungee) {
                    $bungee->delete();
                }
            }

            return true;
        });
    }

}