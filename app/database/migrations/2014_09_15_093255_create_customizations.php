<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomizations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customizations', function(Blueprint $table) {
			$table->string('lon');
			$table->string('lat');
			$table->string('territory');
			$table->string('prefix');
			$table->string('cq');
			$table->string('itu');
			$table->string('continent');
			$table->string('tz');
			$table->integer('qso_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('customizations');
	}

}
