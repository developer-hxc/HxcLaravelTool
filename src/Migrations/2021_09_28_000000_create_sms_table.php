<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->index()->comment('手机号');
            $table->string('content')->comment('验证码内容');
            $table->string('ip')->index()->comment('ip地址');
            $table->string('scene')->index()->comment('使用场景');
            $table->string('code')->index()->nullable()->comment('验证码');
            $table->string('status')->default('1')->comment('状态，1：待验证，2：已验证');
            $table->timestamp('end_time')->index()->comment('失效时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms');
    }
}
