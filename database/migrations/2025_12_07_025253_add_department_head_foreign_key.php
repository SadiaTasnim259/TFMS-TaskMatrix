<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepartmentHeadForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * PURPOSE: Add foreign key constraint for department head
     * 
     * This migration runs AFTER staff table is created to avoid circular dependency.
     * departments.head_id references staff.id
     *
     * @return void
     */
    public function up()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->foreign('head_id')
                ->references('id')
                ->on('staff')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['head_id']);
        });
    }
}
