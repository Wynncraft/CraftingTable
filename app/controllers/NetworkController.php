<?php

class NetworkController extends BaseController {

    public function postNetwork() {

        if (Auth::user()->can('create_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to create networks');
        }

        $network = Network::firstOrNew(array('name'=> Input::get('name')));

        $validator = Validator::make(
            array('name'=>$network->name,
                'description'=>Input::get('description')),
            array('name'=>'required|max:100|unique:networks',
                'description'=>'max:255')
        );

        if ($validator->fails()) {
            return Redirect::to('/')->with('errorAdd', $validator->messages());
        } else {
            $network->description = Input::get('description');
            $network->save();
            return Redirect::to('/')->with('open'.$network->id, 'successAdd')->with('success', 'Created network '.$network->name);
        }

    }

    public function putNetwork(Network $network = null) {

        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('update_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to edit networks');
        }

        $validator = Validator::make(
            array('name'=>Input::get('name'),
                'description'=>Input::get('description')),
            array('name'=>'required|max:100|unique:networks,name,'.$network->id,
                'description'=>'max:255')
        );

        $messages = $validator->messages();

        if ($validator->fails()) {
            return Redirect::to('/')->with('open'.$network->id, 'errorEdit')->with('errorEdit'.$network->id, $messages);
        } else {
            $network->name = Input::get('name');
            $network->description = Input::get('description');
            $network->save();
            return Redirect::to('/')->with('open'.$network->id, 'successEdit')->with('success', 'Saved network '.$network->name);
        }
    }

    public function deleteNetwork(Network $network = null) {

        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('delete_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to delete networks');
        }

        $network->delete();

        return Redirect::to('/')->with('success', 'Deleted network '.$network->name);
    }

    public function postServerType(Network $network = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if (Auth::user()->can('update_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to edit networks');
        }

        Validator::extend('checkType', function($attribute, $value, $parameters) {
            $servertype = ServerType::find($value);

            if ($servertype == null) {
                return false;
            }

            return true;
        }, 'Please select a valid server type');

        $validator = Validator::make(
            array('servertype'=>Input::get('servertype'),
                'amount'=>Input::get('amount')),
            array('servertype'=>'required|checkType',
                'amount'=>'required|Integer|Min:1')
        );

        if ($validator->fails()) {
            return Redirect::to('/')->with('open'.$network->id, 'errorAddServerType')->with('errorAddServerType'.$network->id, $validator->messages());
        } else {
            $servertype = ServerType::find(Input::get('servertype'));
            $defaultServerType = null;
            $networkServerType = NetworkServerType::firstOrNew(array('network_id'=>$network->id, 'server_type_id'=>$servertype->id));

            Validator::extend('servertypeExists', function($attribute, $value, $parameters) {

                if ($value->exists == true) {
                    return false;
                }

                return true;
            }, 'The server type '.$servertype->name.' is already added');

            if ($network->defaultServerType() != null) {
                $defaultServerType = $network->defaultServerType()->servertype()->name;
            }

            Validator::extend('typeDefault', function($attribute, $value, $parameters) {

                if (Input::has('default') == false) {
                    return true;
                }

                if ($value->defaultServerType() != null) {
                    return false;
                }

                return true;
            }, 'There is already a default server type '.$defaultServerType);

            $validator = Validator::make(
                array('serverType'=>$networkServerType,
                    'networkDefaultServerType'=>$network),
                array('serverType'=>'servertypeExists',
                    'networkDefaultServerType'=>'typeDefault'));

            if ($validator->fails()) {
                return Redirect::to('/')->with('open'.$network->id, 'errorAddServerType')->with('errorAddServerType'.$network->id, $validator->messages());
            }

            $networkServerType->amount = Input::get('amount');

            if (Input::has('default') == true) {
                $networkServerType->default = true;
            }

            $networkServerType->save();

            return Redirect::to('/')->with('open'.$network->id, 'successServerTypeAdd')->with('success', 'Added the server type '.$servertype->name.' to the network '.$network->name);
        }
    }

    public function deleteServerType(Network $network = null, NetworkServerType $networkServerType = null) {
        if ($network == null) {
            return Redirect::to('/')->with('error', 'Unknown network Id');
        }

        if ($networkServerType == null) {
            return Redirect::to('/')->with('error', 'Unknown server type Id');
        }

        if (Auth::user()->can('update_network') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to update networks');
        }

        $networkServerType->delete();

        return Redirect::to('/')->with('open'.$network->id, 'successServerTypeDelete')->with('success', 'Deleted server type '.$networkServerType->servertype()->name.' from '.$network->name);
    }

}