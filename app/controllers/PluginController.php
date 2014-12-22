<?php

class PluginController extends BaseController {

    public function getPluginsJson() {
        /*if (Auth::user()->can('read_plugin') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to view the plugins page');
        }*/
        return Plugin::all();
    }

    public function getPluginJson(Plugin $plugin = null) {
        if ($plugin == null) {
            return Response::json(array(), 404);
        }
        /*if (Auth::user()->can('read_plugin') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to view the plugins page');
        }*/

        return $plugin;
    }

    public function getPluginVersionsJson(Plugin $plugin = null) {
        if ($plugin == null) {
            return Response::json(array(), 404);
        }
        /*if (Auth::user()->can('read_plugin') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to view the plugins page');
        }*/

        return $plugin->versions()->get();
    }

    public function getPluginConfigsJson(Plugin $plugin = null) {
        if ($plugin == null) {
            return Response::json(array(), 404);
        }
        /*if (Auth::user()->can('read_plugin') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to view the plugins page');
        }*/

        return $plugin->configs()->get();
    }

    public function getPlugins() {
        if (Auth::user()->can('read_plugin') == false) {
            Redirect::to('/')->with('error', 'You do not have permission to view the plugins page');
        }

        return View::make('plugins');
    }

    public function postPlugin() {
        if (Auth::user()->can('create_plugin') == false) {
            Redirect::to('/plugins')->with('error', 'You do not have permission to create plugins');
        }

        $plugin = Plugin::firstOrNew(array('name'=> Input::get('name')));

        $validator = Validator::make(
            array('name'=>$plugin->name,
                'description'=>Input::get('description'),
                'type'=>Input::get('type'),
                'directory'=>Input::get('directory')),
            array('name'=>'required|min:3|max:100|unique:plugins',
                'description'=>'max:255',
                'type'=>'required',
                'directory'=>'required|max:255')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        if ($validator->fails()) {
            return Redirect::to('/plugins')->with('errorAdd', $validator->messages());
        } else {
            $plugin->description = Input::get('description');
            $plugin->type = Input::get('type');
            $plugin->directory = Input::get('directory');
            $plugin->save();
            return Redirect::to('/plugins')->with('open'.$plugin->id, 'successAdd')->with('success', 'Created plugin '.$plugin->name);
        }
    }

    public function postVersion(Plugin $plugin = null) {
        if ($plugin == null) {
            return Redirect::to('/plugins')->with('error', 'Unknown plugin Id');
        }
        if (Auth::user()->can('update_plugin') == false) {
            Redirect::to('/plugins')->with('error', 'You do not have permission to update plugins');
        }

        $pluginVersion = new PluginVersion(array('version'=> Input::get('version')));

        Validator::extend('uniqueVersion', function ($attribute, $value, $params) use ($plugin) {
            if ($plugin->versions()->where('version', '=', $value)->first() != null) {
                return false;
            }
            return true;
        }, "The version has already been taken.");

        $validator = Validator::make(
            array('version'=>$pluginVersion->version,
                'description'=>Input::get('description')),
            array('version'=>'required|min:3|max:100|uniqueVersion',
                'description'=>'max:255')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        $messages = $validator->messages();

        if ($validator->fails()) {
            return Redirect::to('/plugins')->with('open'.$plugin->id, 'errorVersion')->with('errorVersion' . $plugin->id, $messages);
        } else {
            $pluginVersion->description = Input::get('description');
            $pluginVersion->plugin_id = $plugin->id;
            //$pluginVersion->save();
            $plugin->versions()->save($pluginVersion);

            return Redirect::to('/plugins')->with('open'.$plugin->id, 'successVersionAdd')->with('success', 'Added version '.$pluginVersion->version.' to plugin '.$plugin->name);
        }
    }

    public function deleteVersion(Plugin $plugin = null, $pluginVersion = null) {
        if ($plugin == null) {
            return Redirect::to('/plugins')->with('error', 'Unknown plugin Id');
        }
        $pluginVersion = $plugin->versions()->where("_id", "=", $pluginVersion)->first();
        if ($pluginVersion == null) {
            return Redirect::to('/plugins')->with('error', 'Unknown plugin version Id');
        }
        if (Auth::user()->can('update_plugin') == false) {
            Redirect::to('/plugins')->with('error', 'You do not have permission to update plugins');
        }

        $pluginVersion->delete();

        return Redirect::to('/plugins')->with('open'.$plugin->id, 'successVersionDelete')->with('success', 'Deleted plugin version '.$pluginVersion->version.' from plugin '.$plugin->name);

    }

    public function postConfig(Plugin $plugin = null) {
        if ($plugin == null) {
            return Redirect::to('/plugins')->with('error', 'Unknown plugin Id');
        }
        if (Auth::user()->can('update_plugin') == false) {
            Redirect::to('/plugins')->with('error', 'You do not have permission to update plugins');
        }

        $pluginConfig = new PluginConfig(array('name'=> Input::get('name')));

        Validator::extend('uniqueConfig', function ($attribute, $value, $params) use ($plugin) {
            if ($plugin->configs()->where('name', '=', $value)->first() != null) {
                return false;
            }
            return true;
        }, "The config has already been taken.");

        $validator = Validator::make(
            array('name'=>$pluginConfig->name,
                'description'=>Input::get('description'),
                'directory'=>Input::get('directory')),
            array('name'=>'required|min:3|max:100|uniqueConfig',
                'description'=>'max:255',
                'directory'=>'required|max:255')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        $messages = $validator->messages();

        if ($validator->fails()) {
            return Redirect::to('/plugins')->with('open'.$plugin->id, 'errorConfig')->with('errorConfig' . $plugin->id, $messages);
        } else {
            $pluginConfig->description = Input::get('description');
            $pluginConfig->directory = Input::get('directory');
            $pluginConfig->plugin_id = $plugin->id;
            //$pluginConfig->save();
            $plugin->configs()->save($pluginConfig);

            return Redirect::to('/plugins')->with('open'.$plugin->id, 'successConfigAdd')->with('success', 'Added config '.$pluginConfig->name.' to plugin '.$plugin->name);
        }
    }

    public function deleteConfig(Plugin $plugin = null, $pluginConfig = null) {
        if ($plugin == null) {
            return Redirect::to('/plugins')->with('error', 'Unknown plugin Id');
        }
        $pluginConfig = $plugin->configs()->where("_id", "=", $pluginConfig)->first();
        if ($pluginConfig == null) {
            return Redirect::to('/plugins')->with('error', 'Unknown plugin config Id');
        }
        if (Auth::user()->can('update_plugin') == false) {
            Redirect::to('/plugins')->with('error', 'You do not have permission to update plugins');
        }

        $pluginConfig->delete();

        return Redirect::to('/plugins')->with('open'.$plugin->id, 'successConfigDelete')->with('success', 'Deleted plugin config '.$pluginConfig->name.' from plugin '.$plugin->name);

    }

    public function putPlugin(Plugin $plugin = null) {
        if ($plugin == null) {
            return Redirect::to('/plugins')->with('error', 'Unknown plugin Id');
        }
        if (Auth::user()->can('update_plugin') == false) {
            Redirect::to('/plugins')->with('error', 'You do not have permission to update plugins');
        }

        $validator = Validator::make(
            array('name'=>Input::get('name'),
                'description'=>Input::get('description'),
                'directory'=>Input::get('directory')),
            array('name'=>'required|min:3|max:100|unique:plugins,name,'.$plugin->id,
                'description'=>'max:255',
                'directory'=>'required|max:255')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        $messages = $validator->messages();

        if ($validator->fails()) {
            return Redirect::to('/plugins')->with('open'.$plugin->id, 'errorEdit')->with('errorEdit'.$plugin->id, $messages);
        } else {
            $plugin->name = Input::get('name');
            $plugin->description = Input::get('description');
            $plugin->directory = Input::get('directory');
            $plugin->save();
            return Redirect::to('/plugins')->with('open'.$plugin->id, 'successEdit')->with('success', 'Saved plugin '.$plugin->name);
        }
    }

    public function deletePlugin(Plugin $plugin = null) {
        if ($plugin == null) {
            return Redirect::to('/plugins')->with('error', 'Unknown plugin Id');
        }
        if (Auth::user()->can('delete_plugin') == false) {
            Redirect::to('/plugins')->with('error', 'You do not have permission to delete plugins');
        }

        $plugin->delete();

        return Redirect::to('/plugins')->with('success', 'Deleted plugin '.$plugin->name);
    }

}