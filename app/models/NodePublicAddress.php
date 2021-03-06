<?php

class NodePublicAddress extends Moloquent
{

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['publicAddress'];

    public static function boot() {
        parent::boot();

        NodePublicAddress::deleting(function ($publicAddress) {
            foreach (Network::all() as $network) {
                Log::info("Loop network " . $network->name);
                foreach ($network->bungeetypes()->all() as $bungeeType) {
                    foreach ($bungeeType->addresses()->where('node_public_address_id', '=', $publicAddress->id)->get() as $address) {
                        $address->delete();
                        $amount = $bungeeType->amount - 1;
                        $bungeeType->amount = $amount."";
                        $bungeeType->save();
                        $network->save();
                    }
                }
                foreach ($network->bungees() as $bungee) {
                    if ($bungee->publicAddress()->id == $publicAddress->id) {
                        $bungee->delete();
                    }
                }
            }

            return true;
        });
    }

}