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

        Validator::extend('checkVersion', function($attribute, $value, $parameters) {
            $world = World::find(Input::get('world'));

            if ($world == null) {
                return false;
            }

            $worldVersion = $world->versions()->where('id', '=', $value)->first();

            if ($worldVersion == null) {
                return false;
            }

            return true;
        }, 'Please select a valid world version');

        $validator = Validator::make(
            array('world'=>Input::get('world'),
                'worldVersion'=>Input::get('worldVersion')),
            array('world'=>'required|checkWorld',
                'worldVersion'=>'required|checkVersion')
        );


        if ($validator->fails()) {
            return Redirect::to('/servertypes')->with('open'.$serverType->id, 'errorAddWorldn')->with('errorAddWorld'.$serverType->id, $validator->messages());
        } else {
            $world = World::find(Input::get('world'));
            $defaultWorld = null;
            $worldVersion = $world->versions()->where('id', '=', Input::get('worldVersion'))->first();

            $serverTypeWorld = ServerTypeWorld::firstOrNew(array('servertype_id'=>$serverType->id, 'world_id'=>$world->id));

            Validator::extend('worldExists', function($attribute, $value, $parameters) {

                if ($value->exists == true) {
                    return false;
                }

                return true;
            }, 'The world '.$world->name.' is already added');

            if ($serverType->defaultWorld() != null) {
                $defaultWorld = $serverType->defaultWorld()->world()->name;
            }

            Validator::extend('worldDefault', function($attribute, $value, $parameters) {

                if (Input::has('default') == false) {
                    return true;
                }

                if ($value->defaultWorld() != null) {
                    return false;
                }

                return true;
            }, 'There is already a default world '.$defaultWorld);

            $validator = Validator::make(
                array('serverTypeWorld'=>$serverTypeWorld,
                    'serverTypeDefaultWorld'=>$serverType),
                array('serverTypeWorld'=>'worldExists',
                    'serverTypeDefaultWorld'=>'worldDefault'));

            if ($validator->fails()) {
                return Redirect::to('/servertypes')->with('open'.$serverType->id, 'errorAddWorld')->with('errorAddWorld'.$serverType->id, $validator->messages());
            }

            $serverTypeWorld->worldversion_id = $worldVersion->id;

            if (Input::has('default') == true) {
                $serverTypeWorld->default = true;
            }

            $serverTypeWorld->save();

            return Redirect::to('/servertypes')->with('open'.$serverType->id, 'successWorldAdd')->with('success', 'Added the world '.$world->name.' to the server type '.$serverType->name);
        }
    }

    public function deleteServerTypeWorld(ServerType $serverType = null, ServerTypeWorld $serverTypeWorld = null) {
        if ($serverType == null) {
            return Redirect::to('/servertypes')->with('error', 'Unknown server type Id');
        }

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