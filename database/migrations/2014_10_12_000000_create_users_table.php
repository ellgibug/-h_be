<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->engine = "InnoDB";
            $table->bigIncrements('id')->unique();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('is_email_verified')->nullable();
            $table->string('email_verification_code')->nullable();
            $table->timestamp('email_verification_code_expired_at')->nullable();
            $table->string('password');
            $table->string('password_reset_code')->nullable();
            $table->string('password_reset_token')->nullable();
            $table->timestamp('password_reset_code_expired_at')->nullable();
            $table->rememberToken();
            $table->bigInteger('organization_id')->nullable()->unsigned()->index();
            $table->bigInteger('role_id')->unsigned()->index();
            $table->boolean('is_confirmed_in_organization')->default(false);
            $table->timestamps();

            $table->foreign('organization_id')
                ->references('id')->on('organizations')
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')->on('roles')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
