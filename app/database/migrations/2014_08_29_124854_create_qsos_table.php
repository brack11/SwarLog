<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateQsosTable extends Migration {

	public function up()
	{
		Schema::create('qsos', function(Blueprint $table) {
			$table->increments('id');
			$table->string('call');
			$table->timestamp('date');
			$table->text('message');
			$table->string('rst', 7);
			$table->enum('qsl_via', array('B', 'D', 'E', 'M'));
			$table->enum('qsl_rcvd', array('Y', 'N', 'R', 'I'));
			$table->string('comment');
			$table->softDeletes();
			$table->string('address');
			$table->string('frequency');
			$table->integer('user_id');
			$table->integer('band_id');
			$table->integer('mode_id');
		});
	}

	public function down()
	{
		Schema::drop('qsos');
	}
}