<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotesFinalTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes_final', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('code_notes_final', 191)->unique();
            $table->float('value')->nullable();
            $table->float('overcoming')->nullable();

            $table->unsignedBigInteger('evaluation_periods_id');
            $table->foreign('evaluation_periods_id')
                ->references('id')->on('evaluation_periods');

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
        Schema::dropIfExists('notes_final');
    }
}
