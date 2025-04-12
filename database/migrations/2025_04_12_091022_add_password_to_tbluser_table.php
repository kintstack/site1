<?php
// filepath: c:\Users\Tech\Documents\api\Lumen\database\migrations\xxxx_xx_xx_xxxxxx_add_password_to_tbluser_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tbluser', function (Blueprint $table) {
            $table->string('password')->after('email'); // Add the password column
        });
    }

    public function down()
    {
        Schema::table('tbluser', function (Blueprint $table) {
            $table->dropColumn('password'); // Remove the password column
        });
    }
};