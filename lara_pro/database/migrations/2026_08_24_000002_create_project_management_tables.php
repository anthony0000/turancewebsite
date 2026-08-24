<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 80)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['name', 'company']);
        });

        Schema::table('projects', function (Blueprint $table): void {
            if (! Schema::hasColumn('projects', 'client_id')) {
                $table->foreignId('client_id')->nullable()->after('project_number')->constrained('clients')->nullOnDelete();
            }

            if (! Schema::hasColumn('projects', 'project_brief')) {
                $table->longText('project_brief')->nullable();
            }

            if (! Schema::hasColumn('projects', 'project_manager_id')) {
                $table->foreignId('project_manager_id')->nullable()->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('projects', 'priority')) {
                $table->string('priority', 20)->default('medium')->index();
            }

            if (! Schema::hasColumn('projects', 'budget')) {
                $table->decimal('budget', 14, 2)->nullable();
            }

            if (! Schema::hasColumn('projects', 'estimated_hours')) {
                $table->decimal('estimated_hours', 8, 2)->nullable();
            }

            if (! Schema::hasColumn('projects', 'progress_mode')) {
                $table->string('progress_mode', 20)->default('tasks');
            }

            if (! Schema::hasColumn('projects', 'progress_override')) {
                $table->unsignedTinyInteger('progress_override')->nullable();
            }

            if (! Schema::hasColumn('projects', 'archived_at')) {
                $table->timestamp('archived_at')->nullable()->index();
            }

            if (! Schema::hasColumn('projects', 'completed_at')) {
                $table->timestamp('completed_at')->nullable();
            }
        });

        Schema::create('project_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role', 30)->default('member');
            $table->timestamps();
            $table->unique(['project_id', 'user_id']);
            $table->index(['user_id', 'project_id']);
        });

        Schema::create('board_columns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 20)->default('#b8860b');
            $table->unsignedInteger('position')->default(0);
            $table->boolean('is_done')->default(false);
            $table->timestamps();
            $table->unique(['project_id', 'name']);
            $table->index(['project_id', 'position']);
        });

        Schema::create('labels', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('color', 20)->default('#b8860b');
            $table->timestamps();
            $table->unique(['project_id', 'name']);
        });

        Schema::create('milestones', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('due_on')->nullable();
            $table->string('status', 20)->default('open')->index();
            $table->timestamps();
            $table->index(['project_id', 'due_on']);
        });

        Schema::create('sprints', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('goal')->nullable();
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->string('status', 20)->default('future')->index();
            $table->timestamps();
            $table->index(['project_id', 'status']);
        });

        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('board_column_id')->nullable()->constrained('board_columns')->nullOnDelete();
            $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('milestone_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sprint_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_task_id')->nullable()->constrained('tasks')->nullOnDelete();
            $table->unsignedInteger('task_number');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('type', 20)->default('task');
            $table->string('status', 30)->default('backlog')->index();
            $table->string('priority', 20)->default('medium')->index();
            $table->date('starts_on')->nullable();
            $table->date('due_on')->nullable()->index();
            $table->unsignedInteger('position')->default(0);
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('story_points', 8, 2)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->unique(['project_id', 'task_number']);
            $table->index(['project_id', 'board_column_id', 'position']);
            $table->index(['project_id', 'assignee_id', 'due_on']);
        });

        Schema::create('task_collaborators', function (Blueprint $table): void {
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['task_id', 'user_id']);
        });

        Schema::create('task_watchers', function (Blueprint $table): void {
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['task_id', 'user_id']);
        });

        Schema::create('task_labels', function (Blueprint $table): void {
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('label_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['task_id', 'label_id']);
        });

        Schema::create('checklists', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->string('title')->default('Checklist');
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('checklist_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('checklist_id')->constrained()->cascadeOnDelete();
            $table->string('content');
            $table->boolean('is_complete')->default(false);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
            $table->index(['checklist_id', 'position']);
        });

        Schema::create('task_dependencies', function (Blueprint $table): void {
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('depends_on_task_id')->constrained('tasks')->cascadeOnDelete();
            $table->string('type', 20)->default('blocks');
            $table->timestamps();
            $table->primary(['task_id', 'depends_on_task_id']);
        });

        Schema::create('comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->text('body');
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
            $table->index(['task_id', 'created_at']);
        });

        Schema::create('project_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type', 127)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();
            $table->index(['project_id', 'task_id', 'created_at']);
        });

        Schema::create('time_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->unsignedInteger('minutes')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['task_id', 'user_id', 'started_at']);
        });

        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('task_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 80);
            $table->string('entity_type', 80)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['project_id', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
        });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('saved_filters', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('filters');
            $table->timestamps();
            $table->unique(['user_id', 'project_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_filters');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('time_entries');
        Schema::dropIfExists('project_attachments');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('task_dependencies');
        Schema::dropIfExists('checklist_items');
        Schema::dropIfExists('checklists');
        Schema::dropIfExists('task_labels');
        Schema::dropIfExists('task_watchers');
        Schema::dropIfExists('task_collaborators');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('sprints');
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('labels');
        Schema::dropIfExists('board_columns');
        Schema::dropIfExists('project_members');

        Schema::table('projects', function (Blueprint $table): void {
            foreach ([
                'client_id',
                'project_brief',
                'project_manager_id',
                'priority',
                'budget',
                'estimated_hours',
                'progress_mode',
                'progress_override',
                'archived_at',
                'completed_at',
            ] as $column) {
                if (Schema::hasColumn('projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::dropIfExists('clients');
    }
};
