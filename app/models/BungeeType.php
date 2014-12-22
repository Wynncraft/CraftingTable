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
                foreach ($network->nodes()->all() as $networkNode) {
                    if ($networkNode->bungeetype() != null) {
                        if ($networkNode->bungeetype()->id == $bungeetype->id) {
                            $networkNode->bungee_type_id = null;
                            $networkNode->node_public_address_id = null;
                            $networkNode->save();
                        }
                    }
                }
                foreach($network->bungees() as $bungee) {
                    $bungee->delete();
                }
            }

            return true;
        });
    }

}