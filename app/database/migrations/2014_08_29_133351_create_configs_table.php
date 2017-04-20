<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConfigsTable extends Migration {

	public function up()
	{
		Schema::create('configs', function(Blueprint $table) {
			$table->increments('id');
			$table->string('name', 50);
			$table->text('value');
			$table->integer('user_id');
		});
	}

	public function down()
	{
		Schema::drop('configs');
	}
}