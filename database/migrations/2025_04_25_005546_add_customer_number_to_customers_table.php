<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('customer_number')->nullable()->unique()->after('id');
        });

        // Populate existing customers with a generated customer number
        if (DB::connection()->getDriverName() === 'sqlite') {
            // SQLite version
            DB::table('customers')->orderBy('id')->get()->each(function ($customer) {
                DB::table('customers')
                    ->where('id', $customer->id)
                    ->update(['customer_number' => 'CUST' . str_pad($customer->id, 6, '0', STR_PAD_LEFT)]);
            });
        } else {
            // MySQL version
            DB::statement("UPDATE customers SET customer_number = CONCAT('CUST', LPAD(id, 6, '0'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('customer_number');
        });
    }
};
