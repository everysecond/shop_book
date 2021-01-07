<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCrmImagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('crm_images', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('related_table', 50)->default('')->comment('归属业务表');
			$table->integer('resource_id')->default(0)->comment('资源id');
			$table->string('relative_path')->default('')->comment('相对路径');
			$table->string('absolute_path')->default('')->comment('绝对路径');
			$table->integer('file_size')->nullable()->comment('文件大小');
			$table->string('file_name')->nullable()->comment('文件上传前名称');
			$table->string('mime_type', 50)->nullable()->comment('文件类型');
			$table->string('ext_type', 50)->nullable()->comment('扩展类型');
			$table->integer('created_by')->unsigned()->comment('操作人');
			$table->bigInteger('created_at')->unsigned()->nullable();
			$table->bigInteger('updated_at')->unsigned()->nullable();
		});
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `crm_images` comment '上传图片地址表'");//表注释
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('crm_images');
	}

}
