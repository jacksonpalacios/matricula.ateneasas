<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWordExpressionsIdForeignToScaleEvaluationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scale_evaluations', function (Blueprint $table) {
            $table->unsignedInteger('words_expressions_id');
            $table->foreign('words_expressions_id')
                ->references('id')->on('words_expressions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scale_evaluations', function (Blueprint $table) {
            $table->dropForeign(['words_expressions_id']);
            $table->dropColumn(['words_expressions_id']);
        });
    }
}
