<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bugs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('status')->comment('ステータス');
            $table->string('summary', 1024)->comment('内容');
            $table->unsignedBigInteger('reported_by')->comment('報告者ID');
            $table->dateTime('reported_at')->comment('報告日時');

            // 共通項目
            $table->unsignedBigInteger('created_by')->comment('登録者ID');
            $table->dateTime('created_at')->comment('登録日時');
            $table->unsignedBigInteger('updated_by')->comment('更新者ID');
            $table->dateTime('updated_at')->comment('更新日時');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('削除者ID');
            $table->dateTime('deleted_at')->nullable()->comment('削除日時');

            // foreign key
            $table->foreign('reported_by')->references('id')->on('users');

            // index
            $table->index('reported_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bugs');
    }
};
