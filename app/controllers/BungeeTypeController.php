<?php

class BungeeTypeController extends BaseController {

    public function getBungeeTypes() {
        if (Auth::user()->can('read_bungeetype') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to view the bungee types page');
        }

        return View::make('bungeetypes');
    }

    public function postBungeeType() {

        if (Auth::user()->can('create_bungeetype') == false) {
            Redirect::to('/bungeetypes')->with('error', 'You do not have permission to create bungee types');
        }

        $bungeeType = BungeeType::firstOrNew(array('name'=> Input::get('name')));

        $validator = Validator::make(
            array('name'=>$bungeeType->name,
                'description'=>Input::get('description'),
                'memory'=>Input::get('ram')),
            array('name'=>'required|min:3|max:100|unique:bungeetypes',
                'description'=>'max:255',
                'memory'=>'required|Integer|Min:1024')
        );

        if ($validator->fails()) {
            return Redirect::to('/bungeetypes')->with('errorAdd', $validator->messages());
        } else {

            $bungeeType->description = Input::get('description');
            $bungeeType->ram = Input::get('ram');

            $bungeeType->save();

            return Redirect::to('/bungeetypes')->with('open'.$bungeeType->id, 'successCreate')->with('success', 'Created Bungee Type '.$bungeeType->name);
        }

    }

    public function putBungeeType(BungeeType $bungeeType = null) {
        if ($bungeeType == null) {
            return Redirect::to('/bungeetypes')->with('error', 'Unknown bungee type Id');
        }

        if (Auth::user()->can('update_bungeetype') == false) {
            Redirect::to('/bungeetypes')->with('error', 'You do not have permission to create bungee types');
        }

        $validator = Validator::make(
            array('name'=>Input::get('name'),
                'description'=>Input::get('description'),
                'memory'=>Input::get('ram')),
            array('name'=>'required|min:3|max:100|unique:bungeetypes,name,'.$bungeeType->id,
                'description'=>'max:255',
                'memory'=>'required|Integer|Min:1024')
        );

        if ($validator->fails()) {
            return Redirect::to('/bungeetypes')->with('open'.$bungeeType->id, 'errorEdit')->with('errorEdit'.$bungeeType->id, $validator->messages());
        } else {
            $bungeeType->name = Input::get('name');
            $bungeeType->description = Input::get('description');
            $bungeeType->ram = Input::get('ram');

            $bungeeType->save();

            return Redirect::to('/bungeetypes')->with('open'.$bungeeType->id, 'successEdit')->with('success', 'Updated Bungee Type '.$bungeeType->name);
        }
    }

    public function deleteBungeeType(BungeeType $bungeeType = null) {
        if ($bungeeType == null) {
            return Redirect::to('/bungeetypes')->with('error', 'Unknown bungee type Id');
        }

        if (Auth::user()->can('delete_bungeetype') == false) {
            Redirect::to('/bungeetypes')->with('error', 'You do not have permission to delete bungee types');
        }

        $bungeeType->delete();

        return Redirect::to('/bungeetypes')->with('success', 'Deleted Bungee Type '.$bungeeType->name);
    }

    public function postBungeeTypePlugin(BungeeType $bungeeType = null) {
        if ($bungeeType == null) {
            return Redirect::to('/bungeetypes')->with('error', 'Unknown bungee type Id');
        }

        if (Auth::user()->can('update_bungee') == false) {
            Redirect::to('/bungeetypes')->with('error', 'You do not have permission to update bungee types');
        }

        Validator::extend('checkPlugin', function($attribute, $value, $parameters) {
            $plugin = Plugin::find($value);

            if ($plugin == null) {
                return false;
            }

            return true;
        }, 'Please select a valid plugin');

        Validator::extend('checkType', function($attribute, $value, $parameters) {
            $plugin = Plugin::find($value);

            if ($plugin == null) {
                return false;
            }

            if ($plugin->type != 'BUNGEE') {
                return false;
            }

            return true;
        }, 'Please select a plugin for bungees.');

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
            array('plugin'=>'required|checkPlugin|checkType',
                'pluginVersion'=>'required|checkVersion')
        );


        if ($validator->fails()) {
            return Redirect::to('/bungeetypes')->with('open'.$bungeeType->id, 'errorAddPlugin')->with('errorAddPlugin'.$bungeeType->id, $validator->messages());
        } else {
            $plugin = Plugin::find(Input::get('plugin'));
            $pluginVersion = $plugin->versions()->where('id', '=', Input::get('pluginVersion'))->first();

            $bungeeTypePlugin = BungeeTypePlugin::firstOrNew(array('bungeetype_id'=>$bungeeType->id, 'plugin_id'=>$plugin->id));

            Validator::extend('pluginExists', function($attribute, $value, $parameters) {

                if ($value->exists == true) {
                    return false;
                }

                return true;
            }, 'The plugin '.$plugin->name.' is already added');

            $validator = Validator::make(
                array('bungeeTypePlugin'=>$bungeeTypePlugin),
                array('bungeeTypePlugin'=>'pluginExists'));

            if ($validator->fails()) {
                return Redirect::to('/bungeetypes')->with('open'.$bungeeType->id, 'errorAddPlugin')->with('errorAddPlugin'.$bungeeType->id, $validator->messages());
            }

            $bungeeTypePlugin->pluginversion_id = $pluginVersion->id;

            $bungeeTypePlugin->save();

            return Redirect::to('/bungeetypes')->with('open'.$bungeeType->id, 'successPluginAdd')->with('success', 'Added the plugin '.$plugin->name.' to the bungee type '.$bungeeType->name);
        }
    }

    public function deleteBungeeTypePlugin(BungeeType $bungeeType = null, BungeeTypePlugin $bungeeTypePlugin = null) {
        if ($bungeeType == null) {
            return Redirect::to('/bungeetypes')->with('error', 'Unknown bungee type Id');
        }

        if ($bungeeTypePlugin == null) {
            return Redirect::to('/bungeetypes')->with('error', 'Unknown server plugin Id');
        }

        if (Auth::user()->can('update_bungeetypes') == false) {
            Redirect::to('/bungeetypes')->with('error', 'You do not have permission to update bungee types');
        }

        $bungeeTypePlugin->delete();

        return Redirect::to('/bungeetypes')->with('open'.$bungeeType->id, 'successPluginDelete')->with('success', 'Deleted plugin '.$bungeeTypePlugin->plugin()->name.' from '.$bungeeType->name);
    }

}