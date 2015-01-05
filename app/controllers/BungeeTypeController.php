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
        Validator::getPresenceVerifier()->setConnection("mongodb");

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
            array('name'=>'required|min:3|max:100|unique:bungeetypes,'.$bungeeType->id,
                'description'=>'max:255',
                'memory'=>'required|Integer|Min:1024')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

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

        $validator = Validator::make(
            array('plugin'=>Input::get('plugin')),
            array('plugin'=>'required|checkPlugin|checkType')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");


        if ($validator->fails()) {
            return Redirect::to('/bungeetypes')->with('open'.$bungeeType->id, 'errorAddPlugin')->with('errorAddPlugin'.$bungeeType->id, $validator->messages());
        } else {
            $plugin = Plugin::find(Input::get('plugin'));

            $bungeeTypePlugin = new PluginHolderPlugin(array('plugin_id'=>$plugin->id));

            Validator::extend('pluginExists', function($attribute, $value, $parameters) use($bungeeType) {

                if ($bungeeType->plugins()->where('plugin_id', '=', $value->plugin_id)->first() != null) {
                    return false;
                }

                return true;
            }, 'The plugin '.$plugin->name.' is already added');

            $validator = Validator::make(
                array('bungeeTypePlugin'=>$bungeeTypePlugin),
                array('bungeeTypePlugin'=>'pluginExists')
            );
            Validator::getPresenceVerifier()->setConnection("mongodb");

            if ($validator->fails()) {
                return Redirect::to('/bungeetypes')->with('open'.$bungeeType->id, 'errorAddPlugin')->with('errorAddPlugin'.$bungeeType->id, $validator->messages());
            }

            //$bungeeTypePlugin->save();
            $bungeeType->plugins()->save($bungeeTypePlugin);

            return Redirect::to('/bungeetypes')->with('open'.$bungeeType->id, 'successPluginAdd')->with('success', 'Added the plugin '.$plugin->name.' to the bungee type '.$bungeeType->name);
        }
    }

    public function putBungeeTypePlugin(BungeeType $bungeeType = null) {
        if ($bungeeType == null) {
            return Redirect::to('/bungeetypes')->with('error', 'Unknown bungee type Id');
        }

        if (Auth::user()->can('update_bungeetypes') == false) {
            Redirect::to('/bungeetypes')->with('error', 'You do not have permission to update bungee types');
        }

        foreach ($bungeeType->plugins()->get() as $plugin) {
            $versionId = Input::get($bungeeType->id.'pluginVersion'.$plugin->id);
            $configId = Input::get($bungeeType->id.'pluginConfig'.$plugin->id);

            Validator::extend('versionExists', function($attribute, $value, $parameters) use($plugin) {
                foreach ($plugin->plugin()->versions()->get() as $version) {
                    if ($version->id == $value) {
                        return true;
                    }
                }

                return false;
            }, 'Please select a valid version for plugin '.$plugin->plugin()->name);

            Validator::extend('configExists', function($attribute, $value, $parameters) use($plugin) {
                if ($value == -1) {
                    return true;
                }
                foreach ($plugin->plugin()->configs()->get() as $config) {
                    if ($config->id == $value) {
                        return true;
                    }
                }

                return false;
            }, 'Please select a valid config for plugin '.$plugin->plugin()->name);

            $validator = Validator::make(
                array('pluginVersion'=>$versionId,
                    'pluginConfig'=>$configId),
                array('pluginVersion'=>'versionExists',
                    'pluginConfig'=>'configExists')
            );

            if ($validator->fails()) {
                return Redirect::to('/bungeetypes')->with('open'.$bungeeType->id, 'errorSaveWorld')->with('errorSavePlugin'.$bungeeType->id, $validator->messages());
            }

            $plugin->pluginversion_id = $versionId;
            if ($configId != -1) {
                $plugin->pluginconfig_id = $configId;
            }

            $plugin->save();
        }

        return Redirect::to('/bungeetypes')->with('open'.$bungeeType->id, 'successWorldAdd')->with('success', 'Saved the plugins for the bungee type '.$bungeeType->name);
    }

    public function deleteBungeeTypePlugin(BungeeType $bungeeType = null, $bungeeTypePlugin = null) {
        if ($bungeeType == null) {
            return Redirect::to('/bungeetypes')->with('error', 'Unknown bungee type Id');
        }

        $bungeeTypePlugin = $bungeeType->plugins()->where("_id", "=", $bungeeTypePlugin)->first();
        if ($bungeeTypePlugin == null) {
            return Redirect::to('/bungeetypes')->with('error', 'Unknown bungee plugin Id');
        }

        if (Auth::user()->can('update_bungeetypes') == false) {
            Redirect::to('/bungeetypes')->with('error', 'You do not have permission to update bungee types');
        }

        $bungeeTypePlugin->delete();

        return Redirect::to('/bungeetypes')->with('open'.$bungeeType->id, 'successPluginDelete')->with('success', 'Deleted plugin '.$bungeeTypePlugin->plugin()->name.' from '.$bungeeType->name);
    }

}