<?php

use Illuminate\Database\Migrations\Migration;

class MainDatabase extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		Schema::connection('mongodb')->create('worlds', function($collection) {
			$collection->unique('name');
		});

		Schema::connection('mongodb')->create('plugins', function($collection) {
			$collection->unique('name');
		});

		Schema::connection('mongodb')->create('servertypes', function($collection) {
			$collection->unique('name');
		});

		Schema::connection('mongodb')->create('bungeetypes', function($collection) {
			$collection->unique('name');
		});

		Schema::connection('mongodb')->create('nodes', function($collection) {
			$collection->unique('name');
			$collection->unique('privateAddress');
		});

		Schema::connection('mongodb')->create('networks', function($collection) {
			$collection->unique('name');
		});

		Schema::connection('mongodb')->create('servers', function($collection) {
			$collection->index('network_id');
			$collection->index('server_type_id');
			$collection->index('node_id');
			$collection->index('number');
		});

		Schema::connection('mongodb')->create('bungees', function($collection) {
			$collection->index('network_id');
			$collection->index('bungee_type_id');
			$collection->index('node_id');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('mongodb')->drop('servers');
		Schema::connection('mongodb')->drop('bungees');
		Schema::connection('mongodb')->drop('networks');
		Schema::connection('mongodb')->drop('nodes');
		Schema::connection('mongodb')->drop('bungeetypes');
		Schema::connection('mongodb')->drop('servertypes');
		Schema::connection('mongodb')->drop('plugins');
		Schema::connection('mongodb')->drop('worlds');

	}

}
