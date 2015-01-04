<?php

class NetworkBungeeType extends Moloquent
{

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['bungee_type_id'];

    public function bungeetype() {
        return $this->hasOne('BungeeType', '_id', 'bungee_type_id')->first();
    }

    public function addresses() {
        return $this->embedsMany('NetworkBungeeTypeAddress');
    }

    public function nodes() {
        $nodes = array();

        foreach ($this->addresses()->get() as $addressInfo) {
            if (in_array($addressInfo->node()->id, $nodes)) {
                continue;
            }
            $nodes[] = $addressInfo->node()->id;
        }

        return $nodes;
    }

}