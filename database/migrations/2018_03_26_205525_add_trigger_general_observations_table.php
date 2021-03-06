<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTriggerGeneralObservationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('general_observations', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::unprepared('
                DROP PROCEDURE IF EXISTS insert_code_general_observation;
                CREATE TRIGGER insert_code_general_observation BEFORE INSERT ON `general_observations` FOR EACH ROW
                BEGIN
                SET NEW.code = CONCAT(NEW.enrollment_id,NEW.period_working_day_id);
                END;
                
            ');
                \Illuminate\Support\Facades\DB::unprepared('
              DROP PROCEDURE IF EXISTS update_code_general_observation;
                CREATE TRIGGER update_code_general_observation BEFORE UPDATE ON `general_observations` FOR EACH ROW
                BEGIN
                SET NEW.code = CONCAT(NEW.enrollment_id,NEW.period_working_day_id);
                END;
                
            ');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('general_observations', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::unprepared('DROP TRIGGER `insert_code_general_observation`');
            \Illuminate\Support\Facades\DB::unprepared('DROP TRIGGER `update_code_general_observation`');
        });
    }
}
