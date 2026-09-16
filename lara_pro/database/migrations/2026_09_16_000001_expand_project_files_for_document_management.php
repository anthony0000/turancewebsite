                                                             c <?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_files', function (Blueprint $table): void {
            $table->dropForeign(['project_id']);
        });

        Schema::table('project_files', function (Blueprint $table): void {
            $table->unsignedBigInteger('project_id')->nullable()->change();
            $table->string('document_scope', 20)->default('project')->after('project_id')->index();
            $table->string('folder', 100)->nullable()->after('document_scope')->index();
            $table->foreign('project_id')->references('id')->on('projects')->nullOnDelete();
            $table->index(['document_scope', 'created_at']);
        });
    }

    public function down(): void
    {
        DB::table('project_files')->whereNull('project_id')->delete();

        Schema::table('project_files', function (Blueprint $table): void {
            $table->dropForeign(['project_id']);
            $table->dropIndex(['document_scope', 'created_at']);
            $table->dropIndex(['document_scope']);
            $table->dropIndex(['folder']);
            $table->dropColumn(['document_scope', 'folder']);
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
        });
    }
};
