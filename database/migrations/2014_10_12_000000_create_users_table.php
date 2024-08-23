<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->date('dob')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('phone_number')->nullable();
             $table->string('country', 100)->nullable();
            $table->enum('user_status', ['Active','Verified','Unverified','Pending','Block'])->default('Pending');
            $table->enum('role', ['Admin','User','Employee'])->default('User');
            $table->enum('register_from', ['Web', 'Facebook', 'Gmail', 'Apple', 'IOS', 'Android'])->default('Web');
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('profile_completion')->default(0);
            $table->longText('address')->nullable();
            $table->longText('description')->nullable();
            $table->string('personal_identity')->nullable();
            $table->string('email_verification_code', 10)->nullable();
            $table->enum('theme_mode', ['Light', 'Dark'])->default('Light');
            $table->double('time_spent')->default(0);
            $table->timestamp('last_seen')->nullable();
            $table->longText('verification_token')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            // $table->string('full_name', 100)->nullable();

            // $table->enum('user_type', ['super-club', 'chef-at-home', 'both', 'none'])->default('none');

            // $table->string('location', 100)->nullable();
            // $table->string('country', 100)->nullable();
            // $table->string('city', 100)->nullable();
            // $table->string('state', 100)->nullable();
            // $table->string('latitude', 100)->nullable();
            // $table->string('longitude', 100)->nullable();

            // $table->tinyInteger('user_status')->comment('1=Active, 2=Block')->default(1);


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
