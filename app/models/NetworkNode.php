<?php

class NetworkNode extends Moloquent
{

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['node_id', 'node_public_address_id', 'bungee_type_id'];

    public static function boot() {
        parent::boot();

        NetworkNode::deleting(function ($node) {
            foreach (Network::all() as $network) {
                Log::info("Loop network " . $network->name);
                foreach ($network->bungeetypes()->all() as $bungeeType) {
                    foreach ($bungeeType->addresses()->where('node_id', '=', $node->node()->id)->get() as $address) {
                        $address->delete();
                        $amount = $bungeeType->amount - 1;
                        $bungeeType->amount = $amount."";
                        $bungeeType->save();
                    }
                }
            }

            return true;
        });
    }

    public function publicaddress() {
        $publicAddress = $this->node()->publicaddresses()->where('id', '=', $this->node_public_address_id)->first();
        return $publicAddress;
    }

    /*public function bungeetype() {
        return $this->hasOne('BungeeType', '_id', 'bungee_type_id')->first();
    }*/

    public function node() {
        return $this->hasOne('Node', '_id', 'node_id')->first();
    }

}