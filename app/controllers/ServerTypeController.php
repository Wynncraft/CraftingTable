<?php

class ServerTypeController extends BaseController {

    public function getServerTypes() {
        if (Auth::user()->can('read_servertype') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to view the server types page');
        }

        return View::make('servertypes');
    }

    public function postServerType() {

        if (Auth::user()->can('create_servertype') == false) {
            Redirect::to('/servertypes')->with('error', 'You do not have permission to create server types');
        }

        $serverType = ServerType::firstOrNew(array('name'=> Input::get('name')));

        $validator = Validator::make(
            array('name'=>$serverType->name,
                'description'=>Input::get('description'),
                'players'=>Input::get('players'),
                'memory'=>Input::get('ram')),
            array('name'=>'required|max:100|unique:servertypes',
                'description'=>'max:255',
                'players'=>'required|Integer|Min:1',
                'memory'=>'required|Integer|Min:1024')
        );

        if ($validator->fails()) {
            return Redirect::to('/servertypes')->with('errorAdd', $validator->messages());
        } else {

            $serverType->description = Input::get('description');
            $serverType->players = Input::get('players');
            $serverType->ram = Input::get('ram');

            $serverType->save();

            return Redirect::to('/servertypes')->with('open'.$serverType->id, 'successCreate')->with('success', 'Created Server Type '.$serverType->name);
        }

    }

    public function putServerType(ServerType $serverType = null) {
        if ($serverType == null) {
            return Redirect::to('/servertypes')->with('error', 'Unknown server type Id');
        }

        if (Auth::user()->can('update_servertype') == false) {
            Redirect::to('/servertypes')->with('error', 'You do not have permission to create server types');
        }

        $validator = Validator::make(
            array('name'=>Input::get('name'),
                'description'=>Input::get('description'),
                'players'=>Input::get('players'),
                'memory'=>Input::get('ram')),
            array('name'=>'required|max:100|unique:servertypes,name,'.$serverType->id,
                'description'=>'max:255',
                'players'=>'required|Integer|Min:1',
                'memory'=>'required|Integer|Min:1024')
        );

        if ($validator->fails()) {
            return Redirect::to('/servertypes')->with('open'.$serverType->id, 'errorEdit')->with('errorEdit'.$serverType->id, $validator->messages());
        } else {
            $serverType->name = Input::get('name');
            $serverType->description = Input::get('description');
            $serverType->players = Input::get('players');
            $serverType->ram = Input::get('ram');

            $serverType->save();

            return Redirect::to('/servertypes')->with('open'.$serverType->id, 'successEdit')->with('success', 'Updated Server Type '.$serverType->name);
        }
    }

    public function deleteServerType(ServerType $serverType = null) {
        if ($serverType == null) {
            return Redirect::to('/servertypes')->with('error', 'Unknown server type Id');
        }

        if (Auth::user()->can('delete_servertype') == false) {
            Redirect::to('/servertypes')->with('error', 'You do not have permission to delete server types');
        }

        $serverType->delete();

        return Redirect::to('/servertypes')->with('success', 'Deleted Server Type '.$serverType->name);
    }

    public function postServerTypePlugin(ServerType $serverType = null) {
        if ($serverType == null) {
            return Redirect::to('/servertypes')->with('error', 'Unknown server type Id');
        }

        if (Auth::user()->can('update_servertype') == false) {
            Redirect::to('/servertypes')->with('error', 'You do not have permission to update server types');
        }

        Validator::extend('checkPlugin', function($attribute, $value, $parameters) {
            $plugin = Plugin::find($value);

            if ($plugin == null) {
                return false;
            }

            return true;
        }, 'Please select a valid plugin');

        Validator::extend('checkVersion', function($attribute, $value, $parameters) {
            $plugin = Plugin::find(Input::get('plugin'));

            if ($plugin == null) {
                return false;
            }

            $pluginVersion = $plugin->versions()->where('id', '=', $value)->first();

            if ($pluginVersion == null) {
                return false;
            }

            return true;
        }, 'Please select a valid plugin version');

        $validator = Validator::make(
            array('plugin'=>Input::get('plugin'),
                'pluginVersion'=>Input::get('pluginVersion')),
            array('plugin'=>'required|checkPlugin',
                'pluginVersion'=>'required|checkVersion')
        );


        if ($validator->fails()) {
            return Redirect::to('/servertypes')->with('open'.$serverType->id, 'errorAddPlugin')->with('errorAddPlugin'.$serverType->id, $validator->messages());
        } else {
            $plugin = Plugin::find(Input::get('plugin'));
            $pluginVersion = $plugin->versions()->where('id', '=', Input::get('pluginVersion'))->first();

            $serverTypePlugin = ServerTypePlugin::firstOrNew(array('servertype_id'=>$serverType->id, 'plugin_id'=>$plugin->id));

            Validator::extend('pluginExists', function($attribute, $value, $parameters) {

                if ($value->exists == true) {
                    return false;
                }

                return true;
            }, 'The plugin '.$plugin->name.' is already added');

            $validator = Validator::make(
                array('serverTypePlugin'=>$serverTypePlugin),
                array('serverTypePlugin'=>'pluginExists'));

            if ($validator->fails()) {
                return Redirect::to('/servertypes')->with('open'.$serverType->id, 'errorAddPlugin')->with('errorAddPlugin'.$serverType->id, $validator->messages());
            }

            $serverTypePlugin->pluginversion_id = $pluginVersion->id;

            $serverTypePlugin->save();

            return Redirect::to('/servertypes')->with('open'.$serverType->id, 'successPluginAdd')->with('success', 'Added the plugin '.$plugin->name.' to the server type '.$serverType->name);
        }
    }

    public function deleteServerTypePlugin(ServerType $serverType = null, ServerTypePlugin $serverTypePlugin = null) {
        if ($serverType == null) {
            return Redirect::to('/servertypes')->with('error', 'Unknown server type Id');
        }

        if ($serverTypePlugin == null) {
            return Redirect::to('/servertypes')->with('error', 'Unknown server plugin Id');
        }

        if (Auth::user()->can('update_servertype') == false) {
            Redirect::to('/servertypes')->with('error', 'You do not have permission to update server types');
        }

        $serverTypePlugin->delete();

        return Redirect::to('/servertypes')->with('open'.$serverType->id, 'successPluginDelete')->with('success', 'Deleted plugin '.$serverTypePlugin->plugin()->name.' from '.$serverType->name);
    }

}