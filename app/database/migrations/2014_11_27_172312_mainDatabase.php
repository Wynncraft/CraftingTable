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
			$collection->index('name');
			$collection->unique('name');
		});

		Schema::connection('mongodb')->create('plugins', function($collection) {
			$collection->index('name');
			$collection->unique('name');
		});

		Schema::connection('mongodb')->create('servertypes', function($collection) {
			$collection->index('name');
			$collection->unique('name');
		});

		Schema::connection('mongodb')->create('bungeetypes', function($collection) {
			$collection->index('name');
			$collection->unique('name');
		});

		Schema::connection('mongodb')->create('nodes', function($collection) {
			$collection->index('name');
			$collection->unique('name');
			$collection->index('privateAddress');
			$collection->unique('privateAddress');
		});

		Schema::connection('mongodb')->create('networks', function($collection) {
			$collection->index('name');
			$collection->unique('name');
		});

	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('mongodb')->drop('networks');
		Schema::connection('mongodb')->drop('nodes');
		Schema::connection('mongodb')->drop('bungeetypes');
		Schema::connection('mongodb')->drop('servertypes');
		Schema::connection('mongodb')->drop('plugins');
		Schema::connection('mongodb')->drop('worlds');

	}

}
