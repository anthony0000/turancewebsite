<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposal_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('category')->nullable();
            $table->string('theme_key', 50)->default('gold');
            $table->text('description')->nullable();
            $table->json('palette')->nullable();
            $table->json('settings')->nullable();
            $table->json('preview')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('proposals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('proposal_template_id')->nullable()->constrained('proposal_templates')->nullOnDelete();
            $table->string('proposal_number')->unique();
            $table->string('public_token', 80)->unique();
            $table->string('status', 30)->default('draft')->index();
            $table->string('theme_key', 50)->default('gold');
            $table->string('title');
            $table->string('client_name')->nullable();
            $table->string('client_company')->nullable();
            $table->string('prepared_by')->nullable();
            $table->string('company_name');
            $table->string('company_slogan')->nullable();
            $table->date('proposal_date')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('website')->nullable();
            $table->text('business_address')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('background_image_path')->nullable();
            $table->string('currency', 10)->default('USD');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_total', 12, 2)->default(0);
            $table->decimal('tax_total', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('proposal_sections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->string('type', 80);
            $table->string('title');
            $table->string('eyebrow')->nullable();
            $table->longText('body')->nullable();
            $table->json('payload')->nullable();
            $table->string('layout_style', 80)->default('editorial');
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('proposal_assets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->string('asset_type', 50);
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('proposal_pricing_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->string('package', 40)->default('Custom');
            $table->string('service_name');
            $table->text('description')->nullable();
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax_rate', 6, 2)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('proposal_timelines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->string('phase_title');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('duration')->nullable();
            $table->text('deliverables')->nullable();
            $table->string('status', 40)->default('Planned');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('proposal_team_members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('role')->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_image_path')->nullable();
            $table->string('email')->nullable();
            $table->string('social_link')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('proposal_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('primary_color', 20)->default('#111111');
            $table->string('secondary_color', 20)->default('#f3f4f0');
            $table->string('accent_color', 20)->default('#e8b51f');
            $table->string('font_family')->default('Aptos');
            $table->string('header_style')->default('Editorial split');
            $table->string('footer_style')->default('Gold folio');
            $table->boolean('page_numbering')->default(true);
            $table->string('watermark')->nullable();
            $table->json('options')->nullable();
            $table->timestamps();
        });

        Schema::create('proposal_exports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('proposal_id')->constrained()->cascadeOnDelete();
            $table->string('format', 30);
            $table->string('file_name')->nullable();
            $table->string('exported_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposal_exports');
        Schema::dropIfExists('proposal_settings');
        Schema::dropIfExists('proposal_team_members');
        Schema::dropIfExists('proposal_timelines');
        Schema::dropIfExists('proposal_pricing_items');
        Schema::dropIfExists('proposal_assets');
        Schema::dropIfExists('proposal_sections');
        Schema::dropIfExists('proposals');
        Schema::dropIfExists('proposal_templates');
    }
};
