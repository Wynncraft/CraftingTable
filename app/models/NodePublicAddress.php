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

    public static function boot()
    {
        parent::boot();

        NodePublicAddress::deleting(function ($publicAddress) {
            foreach (Network::all() as $network) {
                Log::info("Loop network " . $network->name);
                foreach ($network->nodes()->all() as $networkNode) {
                    Log::info('Loop node ' . $networkNode->node()->name);
                    if ($networkNode->publicAddress() != null) {
                        if ($networkNode->publicAddress()->id == $publicAddress->id) {

                            $nodePublicAddress = null;

                            $usedAddressIds = array();
                            foreach (Network::all() as $testNetwork) {
                                $testNetworkNode = $testNetwork->nodes()->where("node_id", "=", $networkNode->node()->id)->first();
                                if ($testNetworkNode != null) {
                                    if ($testNetworkNode->node_public_address_id != null) {
                                        $usedAddressIds[] = $testNetworkNode->node_public_address_id;
                                    }
                                }
                            }
                            foreach ($networkNode->node()->publicaddresses()->all() as $address) {
                                if (in_array($address->id, $usedAddressIds) == false) {
                                    $nodePublicAddress = $address;
                                    break;
                                }
                            }
                            Log::info("remove");
                            if ($nodePublicAddress != null) {
                                $networkNode->node_public_address_id = $nodePublicAddress->id;
                                $networkNode->save();
                            } else {
                                $networkNode->bungee_type_id = null;
                                $networkNode->node_public_address_id = null;
                                $networkNode->save();
                            }
                        }
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