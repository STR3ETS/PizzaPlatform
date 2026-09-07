<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_topics', function (Blueprint $table) {
            $table->id();
            $table->string('topic');
            $table->string('cluster');
            $table->string('intent')->default('informationeel'); // informationeel | commercieel
            $table->unsignedTinyInteger('priority')->default(5); // 1 = hoogste
            $table->string('status')->default('planned'); // planned | drafting | drafted | published
            $table->foreignId('post_id')->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 400)->nullable();
            $table->string('og_title')->nullable();
            $table->string('og_description', 400)->nullable();
            $table->string('target_keyword')->nullable();
            $table->string('cluster')->nullable();
            $table->string('cover')->nullable();
            $table->string('generation_status')->default('needs_review'); // needs_review | published
            $table->timestamp('published_at')->nullable(); // null = concept
            $table->json('briefing')->nullable();
            $table->json('outline')->nullable();
            $table->json('sections')->nullable();
            $table->json('schema_json')->nullable();
            $table->json('validator_metrics')->nullable();
            $table->json('ai_review')->nullable(); // { verdict: pass|fail, reasons: [] }
            $table->timestamp('last_refreshed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('content_topics');
    }
};
