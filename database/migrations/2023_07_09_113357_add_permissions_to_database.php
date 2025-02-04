<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = [
            [
                'name' => 'admin.root',
                'descriptions' => 'Full access to the admin panel'
            ],
        ];
    
        foreach ($permissions as $permission) {
            \DB::table('permissions')->updateOrInsert(['name' => $permission['name']], $permission);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
};
