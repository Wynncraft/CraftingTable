<?php

class PluginController extends BaseController {

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
                'directory'=>Input::get('directory')),
            array('name'=>'required|max:100|unique:plugins',
                'description'=>'max:255',
                'directory'=>'required|max:255')
        );

        if ($validator->fails()) {
            return Redirect::to('/plugins')->with('errorAdd', $validator->messages());
        } else {
            $plugin->description = Input::get('description');
            $plugin->directory = Input::get('directory');
            $plugin->save();
            return Redirect::to('/plugins')->with('success', 'Created plugin '.$plugin->name);
        }
    }

    public function postVersion(Plugin $plugin = null) {
        if ($plugin == null) {
            return Redirect::to('/plugins')->with('error', 'Unknown plugin Id');
        }
        if (Auth::user()->can('update_plugin') == false) {
            Redirect::to('/plugins')->with('error', 'You do not have permission to update plugins');
        }

        $pluginVersion = PluginVersion::firstOrNew(array('plugin_id' => $plugin->id, 'version'=> Input::get('version')));

        $validator = Validator::make(
            array('version'=>$pluginVersion->version,
                'description'=>Input::get('description')),
            array('version'=>'required|max:100|unique:plugin_versions,plugin_id,'.$plugin->id,
                'description'=>'max:255')
        );

        $messages = $validator->messages();

        if ($validator->fails()) {
            return Redirect::to('/plugins')->with('error' . $plugin->id, 'errorVersion')->with('errorVersion' . $plugin->id, $messages);
        } else {
            $pluginVersion->description = Input::get('description');
            $pluginVersion->plugin_id = $plugin->id;
            $pluginVersion->save();

            return Redirect::to('/plugins')->with('success', 'Added version '.$pluginVersion->version.' to plugin '.$plugin->name);
        }
    }

    public function deleteVersion(Plugin $plugin = null, PluginVersion $pluginVersion = null) {
        if ($plugin == null) {
            return Redirect::to('/plugins')->with('error', 'Unknown plugin Id');
        }
        if ($pluginVersion == null) {
            return Redirect::to('/plugins')->with('error', 'Unknown plugin version Id');
        }
        if (Auth::user()->can('update_plugin') == false) {
            Redirect::to('/plugins')->with('error', 'You do not have permission to update plugins');
        }

        $pluginVersion->delete();

        return Redirect::to('/plugins')->with('success', 'Deleted plugin version '.$pluginVersion->version.' for plugin '.$plugin->name);

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
            array('name'=>'required|max:100|unique:plugins,id,'.$plugin->id,
                'description'=>'max:255',
                'directory'=>'required|max:255')
        );

        $messages = $validator->messages();

        if ($validator->fails()) {
            return Redirect::to('/plugins')->with('error'.$plugin->id, 'errorEdit')->with('errorEdit'.$plugin->id, $messages);
        } else {
            $plugin->name = Input::get('name');
            $plugin->description = Input::get('description');
            $plugin->directory = Input::get('directory');
            $plugin->save();
            return Redirect::to('/plugins')->with('success', 'Saved plugin '.$plugin->name);
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