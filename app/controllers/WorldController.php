<?php

class WorldController extends BaseController
{

    /*public function getWorldsJson() {
        return World::all();
    }

    public function getWorldJson(World $world = null) {
        if ($world == null) {
            return Response::json(array(), 404);
        }

        return $world;
    }

    public function getWorldVersionsJson(World $world = null) {
        if ($world == null) {
            return Response::json(array(), 404);
        }

        return $world->versions()->get();
    }*/

    public function getWorlds()
    {
        return View::make('worlds');
    }

    public function postWorld() {
        if (Auth::user()->can('create_world') == false) {
            Redirect::to('/worlds')->with('error', 'You do not have permission to create worlds');
        }

        $world = World::firstOrNew(array('name'=> Input::get('name')));

        $validator = Validator::make(
            array('name'=>$world->name,
                'description'=>Input::get('description'),
                'directory'=>Input::get('directory')),
            array('name'=>'required|min:3|max:100|unique:worlds',
                'description'=>'max:255',
                'directory'=>'required|max:255')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        if ($validator->fails()) {
            return Redirect::to('/worlds')->with('errorAdd', $validator->messages());
        } else {
            $world->description = Input::get('description');
            $world->directory = Input::get('directory');
            $world->save();
            return Redirect::to('/worlds')->with('open'.$world->id, 'successAdd')->with('success', 'Created world '.$world->name);
        }
    }

    public function postVersion(World $world = null) {
        if ($world == null) {
            return Redirect::to('/worlds')->with('error', 'Unknown world Id');
        }
        if (Auth::user()->can('update_world') == false) {
            Redirect::to('/worlds')->with('error', 'You do not have permission to update worlds');
        }

        //$worldVersion = WorldVersion::firstOrNew(array('world_id' => $world->id, 'version'=> Input::get('version')));

        $worldVersion = new WorldVersion(array('version'=> Input::get('version')));

        Validator::extend('uniqueVersion', function($attribute, $value, $params) use($world) {
            if ($world->versions()->where('version', '=', $value)->first() != null) {
                return false;
            }
            return true;
        }, "The version has already been taken.");

        $validator = Validator::make(
            array('version'=>$worldVersion->version,
                'description'=>Input::get('description')),
            array('version'=>'required|min:3|max:100|uniqueVersion',
                'description'=>'max:255')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        $messages = $validator->messages();

        if ($validator->fails()) {
            return Redirect::to('/worlds')->with('open'.$world->id, 'errorVersion')->with('errorVersion' . $world->id, $messages);
        } else {
            $worldVersion->description = Input::get('description');
            $worldVersion->world_id = $world->id;
            //$worldVersion->save();
            $world->versions()->save($worldVersion);

            return Redirect::to('/worlds')->with('open'.$world->id, 'successVersionAdd')->with('success', 'Added version '.$worldVersion->version.' to world '.$world->name);
        }
    }

    public function deleteVersion(World $world = null, $worldVersion) {
        if ($world == null) {
            return Redirect::to('/worlds')->with('error', 'Unknown world Id');
        }
        $worldVersion = $world->versions()->where("_id", "=", $worldVersion)->first();
        if ($worldVersion == null) {
            return Redirect::to('/worlds')->with('error', 'Unknown world version Id');
        }
        if (Auth::user()->can('update_world') == false) {
            Redirect::to('/worlds')->with('error', 'You do not have permission to update worlds');
        }

        $worldVersion->delete();

        return Redirect::to('/worlds')->with('open'.$world->id, 'successVersionDelete')->with('success', 'Deleted world version '.$worldVersion->version.' for world '.$world->name);

    }

    public function putWorld(World $world = null) {
        if ($world == null) {
            return Redirect::to('/worlds')->with('error', 'Unknown world Id');
        }
        if (Auth::user()->can('update_world') == false) {
            Redirect::to('/worlds')->with('error', 'You do not have permission to update worlds');
        }

        $validator = Validator::make(
            array('name'=>Input::get('name'),
                'description'=>Input::get('description'),
                'directory'=>Input::get('directory')),
            array('name'=>'required|min:3|max:100|unique:worlds,name,'.$world->id,
                'description'=>'max:255',
                'directory'=>'required|max:255')
        );
        Validator::getPresenceVerifier()->setConnection("mongodb");

        $messages = $validator->messages();

        if ($validator->fails()) {
            return Redirect::to('/worlds')->with('open'.$world->id, 'errorEdit')->with('errorEdit'.$world->id, $messages);
        } else {
            $world->name = Input::get('name');
            $world->description = Input::get('description');
            $world->directory = Input::get('directory');
            $world->save();
            return Redirect::to('/worlds')->with('open'.$world->id, 'successEdit')->with('success', 'Saved world '.$world->name);
        }
    }

    public function deleteWorld(World $world = null) {
        if ($world == null) {
            return Redirect::to('/worlds')->with('error', 'Unknown world Id');
        }
        if (Auth::user()->can('delete_world') == false) {
            Redirect::to('/worlds')->with('error', 'You do not have permission to delete worlds');
        }

        $world->delete();

        return Redirect::to('/worlds')->with('success', 'Deleted world '.$world->name);
    }

}