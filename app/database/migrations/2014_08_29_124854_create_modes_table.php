<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModesTable extends Migration {

	public function up()
	{
		Schema::create('modes', function(Blueprint $table) {
			$table->increments('id');
			$table->softDeletes();
			$table->string('name', 255);
			$table->string('code', 7);
		});
	}

	public function down()
	{
		Schema::drop('modes');
	}
}