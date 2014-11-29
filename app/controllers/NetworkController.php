<?php

class NetworkController extends BaseController {

    public function postNetwork() {

        $network = Network::firstOrNew(array('name'=> Input::get('name')));

        $validator = Validator::make(
            array('name'=>$network->name,
                'description'=>Input::get('description')),
            array('name'=>'required|max:100|unique:networks',
                'description'=>'max:255')
        );

        if (Auth::user()->can('create_network') == false) {
            return Redirect::to('/')->with('error', 'You do not have permissions to create networks');
        }

        if ($validator->fails()) {
            return Redirect::to('/')->with('errorAdd', $validator->messages());
        } else {
            $network->description = Input::get('description');
            $network->save();
            return Redirect::to('/')->with('success', 'Created network '.$network->name);
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
            array('name'=>'required|max:100|unique:networks',
                'description'=>'max:255')
        );

        $messages = $validator->messages();

        if ($network->name != Input::get('name') && $validator->fails()) {
            return Redirect::to('/')->with('error'.$network->id, $messages);
        } else {
            $network->name = Input::get('name');
            $network->description = Input::get('description');
            $network->save();
            return Redirect::to('/')->with('success', 'Saved network '.$network->name);
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

}