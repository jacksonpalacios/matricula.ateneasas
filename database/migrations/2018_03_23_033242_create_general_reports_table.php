<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_reports', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Relacion con la matricula
            $table->unsignedBigInteger('enrollment_id');
            $table->foreign('enrollment_id')
                  ->references('id')->on('enrollment');

            // Relacion con el periodo
            $table->unsignedBigInteger('period_working_day_id');
            $table->foreign('period_working_day_id')
                  ->references('id')->on('working_day_periods');

            $table->longText('report');
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
        Schema::dropIfExists('general_reports');
    }
}
