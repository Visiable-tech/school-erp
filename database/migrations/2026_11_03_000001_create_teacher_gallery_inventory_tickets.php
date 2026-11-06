<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up():void {
  Schema::create('teacher_gallery_images',function(Blueprint $t){$t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('uploaded_by')->index();$t->string('title');$t->string('image_path');$t->string('caption',500)->nullable();$t->timestamps();});
  Schema::create('teacher_inventory_items',function(Blueprint $t){$t->id();$t->unsignedBigInteger('school_id')->index();$t->string('name');$t->string('sku',80)->nullable();$t->unsignedInteger('quantity')->default(0);$t->string('unit',30)->default('pcs');$t->timestamps();});
  Schema::create('teacher_inventory_requests',function(Blueprint $t){$t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('item_id')->index();$t->unsignedBigInteger('requested_by')->index();$t->unsignedInteger('quantity');$t->string('status',15)->default('pending');$t->text('reason');$t->unsignedBigInteger('reviewed_by')->nullable();$t->timestamp('reviewed_at')->nullable();$t->timestamps();});
  Schema::create('teacher_support_tickets',function(Blueprint $t){$t->id();$t->unsignedBigInteger('school_id')->index();$t->unsignedBigInteger('created_by')->index();$t->unsignedBigInteger('assigned_to')->nullable()->index();$t->string('title');$t->text('description');$t->string('priority',10)->default('normal');$t->string('status',20)->default('open');$t->timestamps();});
 }
 public function down():void {foreach(['teacher_support_tickets','teacher_inventory_requests','teacher_inventory_items','teacher_gallery_images'] as $t)Schema::dropIfExists($t);}
};
