<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;



class UserDetailsCreate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //

        Schema::create('user_details', function (Blueprint $table) {

            $table->increments('id');
            $table->longText('permissions');
            $table->string('username');
            $table->string('email');
            $table->string('password');

        });


      $perm =   '[{"name":"dashboard","perm":true},{"name":"participants","perm":true},{"name":"speakers","perm":true},{"name":"agenda","perm":true},{"name":"users","perm":true},{"name":"attendance","perm":true},{"name":"QRList","perm":true}]';


        DB::table('user_details')->insert(
            ['email' => 'admin@admin.com', 'username' => 'Nilesh Jayanandana', 'password'=>'admin321','permissions'=>$perm]
        );

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
          Schema::drop('user_details');
    }
}
