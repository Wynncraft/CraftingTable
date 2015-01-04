<?php

class Node extends Moloquent  {

    protected $connection = 'mongodb';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'nodes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'privateAddress', 'ram'];

    public static function boot() {
        parent::boot();

        Node::deleting(function($node) {
            foreach(Network::all() as $network) {
                foreach ($network->bungeetypes()->all() as $bungeeType) {
                    foreach ($bungeeType->addresses()->where('node_id', '=', $node->id)->get() as $address) {
                        $address->delete();
                        $bungeeType->amount -= 1;
                        $bungeeType->save();
                    }
                }
                foreach($network->nodes()->all() as $networkNode) {
                    if ($networkNode->node()->id == $node->id) {
                        $networkNode->delete();
                    }
                }
                foreach($network->servers()->get()->all() as $server) {
                    if ($server->node()->id == $node->id) {
                        $server->delete();
                    }
                }
                foreach($network->bungees()->get()->all() as $bungee) {
                    if ($bungee->node()->id == $node->id) {
                        $bungee->delete();
                    }
                }
            }

            return true;
        });
    }

    /**
     * Public Addresses
     *
     * @return object
     */
    public function publicaddresses()
    {
        return $this->embedsMany('NodePublicAddress');
    }
}