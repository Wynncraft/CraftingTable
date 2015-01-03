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
            array('name'=>'required|min:3|max:100|unique:servertypes',
                'description'=>'max:255',
                'players'=>'required|Integer|Min:1',
                'memory'=>'required|Integer|Min:1024')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

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
            array('name'=>'required|min:3|max:100|unique:servertypes,name,'.$serverType->id,
                'description'=>'max:255',
                'players'=>'required|Integer|Min:1',
                'memory'=>'required|Integer|Min:1024')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

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

        Validator::extend('checkType', function($attribute, $value, $parameters) {
            $plugin = Plugin::find($value);

            if ($plugin == null) {
                return false;
            }

            if ($plugin->type != 'SERVER') {
                return false;
            }

            return true;
        }, 'Please select a plugin for servers.');

        $validator = Validator::make(
            array('plugin'=>Input::get('plugin')),
            array('plugin'=>'required|checkPlugin|checkType')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");


        if ($validator->fails()) {
            return Redirect::to('/servertypes')->with('open'.$serverType->id, 'errorAddPlugin')->with('errorAddPlugin'.$serverType->id, $validator->messages());
        } else {
            $plugin = Plugin::find(Input::get('plugin'));

            $serverTypePlugin = new PluginHolderPlugin(array('plugin_id'=>$plugin->id));

            Validator::extend('pluginExists', function($attribute, $value, $parameters) use ($serverType) {

                if ($serverType->plugins()->where('plugin_id', '=', $value->plugin_id)->first() != null) {
                    return false;
                }

                return true;
            }, 'The plugin '.$plugin->name.' is already added');

            $validator = Validator::make(
                array('serverTypePlugin'=>$serverTypePlugin),
                array('serverTypePlugin'=>'pluginExists')
            );
            Validator::getPresenceVerifier()->setConnection("mongodb");

            if ($validator->fails()) {
                return Redirect::to('/servertypes')->with('open'.$serverType->id, 'errorAddPlugin')->with('errorAddPlugin'.$serverType->id, $validator->messages());
            }

            //$serverTypePlugin->save();
            $serverType->plugins()->save($serverTypePlugin);

            return Redirect::to('/servertypes')->with('open'.$serverType->id, 'successPluginAdd')->with('success', 'Added the plugin '.$plugin->name.' to the server type '.$serverType->name);
        }
    }

    public function putServerTypePlugin(ServerType $serverType = null) {
        if ($serverType == null) {
            return Redirect::to('/servertypes')->with('error', 'Unknown server type Id');
        }

        if (Auth::user()->can('update_servertype') == false) {
            Redirect::to('/servertypes')->with('error', 'You do not have permission to update server types');
        }

        foreach ($serverType->plugins()->get() as $plugin) {
            $versionId = Input::get($serverType->id.'pluginVersion'.$plugin->id);
            $configId = Input::get($serverType->id.'pluginConfig'.$plugin->id);

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
                return Redirect::to('/servertypes')->with('open'.$serverType->id, 'errorSaveWorld')->with('errorSavePlugin'.$serverType->id, $validator->messages());
            }

            $plugin->pluginversion_id = $versionId;
            if ($configId != -1) {
                $plugin->pluginconfig_id = $configId;
            }

            $plugin->save();
        }

        return Redirect::to('/servertypes')->with('open'.$serverType->id, 'successWorldAdd')->with('success', 'Saved the plugins for the server type '.$serverType->name);
    }

    public function deleteServerTypePlugin(ServerType $serverType = null, $serverTypePlugin = null) {
        if ($serverType == null) {
            return Redirect::to('/servertypes')->with('error', 'Unknown server type Id');
        }

        $serverTypePlugin = $serverType->plugins()->where("_id", "=", $serverTypePlugin)->first();
        if ($serverTypePlugin == null) {
            return Redirect::to('/servertypes')->with('error', 'Unknown server plugin Id');
        }

        if (Auth::user()->can('update_servertype') == false) {
            Redirect::to('/servertypes')->with('error', 'You do not have permission to update server types');
        }

        $serverTypePlugin->delete();

        return Redirect::to('/servertypes')->with('open'.$serverType->id, 'successPluginDelete')->with('success', 'Deleted plugin '.$serverTypePlugin->plugin()->name.' from '.$serverType->name);
    }

    public function postServerTypeWorld(ServerType $serverType = null) {
        if ($serverType == null) {
            return Redirect::to('/servertypes')->with('error', 'Unknown server type Id');
        }

        if (Auth::user()->can('update_servertype') == false) {
            Redirect::to('/servertypes')->with('error', 'You do not have permission to update server types');
        }

        Validator::extend('checkWorld', function($attribute, $value, $parameters) {
            $world = World::find($value);

            if ($world == null) {
                return false;
            }

            return true;
        }, 'Please select a valid world');

        $validator = Validator::make(
            array('world'=>Input::get('world')),
            array('world'=>'required|checkWorld')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");


        if ($validator->fails()) {
            return Redirect::to('/servertypes')->with('open'.$serverType->id, 'errorAddWorldn')->with('errorAddWorld'.$serverType->id, $validator->messages());
        } else {
            $world = World::find(Input::get('world'));
            $defaultWorld = null;

            $serverTypeWorld = new ServerTypeWorld(array('world_id'=>$world->id));

            Validator::extend('worldExists', function($attribute, $value, $parameters) use ($serverType) {

                if ($serverType->worlds()->where('world_id', '=', $value->world_id)->first() != null) {
                    return false;
                }

                return true;
            }, 'The world '.$world->name.' is already added');

            $validator = Validator::make(
                array('serverTypeWorld'=>$serverTypeWorld),
                array('serverTypeWorld'=>'worldExists')
            );
            Validator::getPresenceVerifier()->setConnection("mongodb");

            if ($validator->fails()) {
                return Redirect::to('/servertypes')->with('open'.$serverType->id, 'errorAddWorld')->with('errorAddWorld'.$serverType->id, $validator->messages());
            }

            $serverTypeWorld->defaultWorld = false;
            $serverType->worlds()->save($serverTypeWorld);

            return Redirect::to('/servertypes')->with('open'.$serverType->id, 'successWorldAdd')->with('success', 'Added the world '.$world->name.' to the server type '.$serverType->name);
        }
    }

    public function putServerTypeWorld(ServerType $serverType = null) {
        if ($serverType == null) {
            return Redirect::to('/servertypes')->with('error', 'Unknown server type Id');
        }

        if (Auth::user()->can('update_servertype') == false) {
            Redirect::to('/servertypes')->with('error', 'You do not have permission to update server types');
        }

        foreach ($serverType->worlds()->get() as $world) {
            $versionId = Input::get($serverType->id.'worldVersion'.$world->id);
            $default = false;
            if (Input::has($serverType->id . 'default' . $world->id)) {
                $default = true;
            }


            Validator::extend('versionExists', function($attribute, $value, $parameters) use($world) {
                foreach ($world->world()->versions()->get() as $version) {
                    if ($version->id == $value) {
                        return true;
                    }
                }

                return false;
            }, 'Please select a valid version for world '.$world->world()->name);

            Validator::extend('multiDefault', function($attribute, $value, $parameters) use($world, $serverType) {
                $isDefault = 0;

                foreach ($serverType->worlds()->get() as $testWorld) {
                    if (Input::has($serverType->id . 'default' . $testWorld->id)) {
                        $isDefault += 1;
                    }
                }

                if ($isDefault > 1) {
                    return false;
                }

                return true;
            }, 'Please select only one default world.');

            $validator = Validator::make(
                array('worldVersion'=>$versionId,
                    'default'=>$default),
                array('worldVersion'=>'versionExists',
                    'default'=>'multiDefault')
            );

            if ($validator->fails()) {
                return Redirect::to('/servertypes')->with('open'.$serverType->id, 'errorSaveWorld')->with('errorSaveWorld'.$serverType->id, $validator->messages());
            }

            $world->defaultWorld = $default;
            $world->worldversion_id = $versionId;

            $world->save();
        }

        return Redirect::to('/servertypes')->with('open'.$serverType->id, 'successWorldAdd')->with('success', 'Saved the worlds for the server type '.$serverType->name);
    }

    public function deleteServerTypeWorld(ServerType $serverType = null, $serverTypeWorld = null) {
        if ($serverType == null) {
            return Redirect::to('/servertypes')->with('error', 'Unknown server type Id');
        }

        $serverTypeWorld = $serverType->worlds()->where("_id", "=", $serverTypeWorld)->first();
        if ($serverTypeWorld == null) {
            return Redirect::to('/servertypes')->with('error', 'Unknown server world Id');
        }

        if (Auth::user()->can('update_servertype') == false) {
            Redirect::to('/servertypes')->with('error', 'You do not have permission to update server types');
        }

        $serverTypeWorld->delete();

        return Redirect::to('/servertypes')->with('open'.$serverType->id, 'successWorldDelete')->with('success', 'Deleted world '.$serverTypeWorld->world()->name.' from '.$serverType->name);
    }

}