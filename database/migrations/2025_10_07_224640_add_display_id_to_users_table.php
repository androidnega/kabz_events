<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('display_id', 20)->nullable()->unique()->after('id');
        });

        // Generate display IDs for existing users
        $this->generateDisplayIds();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('display_id');
        });
    }

    /**
     * Generate display IDs for existing users based on their roles
     */
    private function generateDisplayIds(): void
    {
        $users = DB::table('users')->get();
        
        foreach ($users as $user) {
            // Get user's role
            $role = DB::table('model_has_roles')
                ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
                ->where('model_has_roles.model_id', $user->id)
                ->where('model_has_roles.model_type', 'App\\Models\\User')
                ->value('roles.name');
            
            // Determine prefix based on role
            $prefix = match($role) {
                'super_admin' => 'SA',
                'admin' => 'ADM',
                'vendor' => 'VND',
                'client' => 'CLT',
                default => 'USR',
            };
            
            // Generate display ID: PREFIX-XXXXXX (6 digits)
            $displayId = $prefix . '-' . str_pad($user->id, 6, '0', STR_PAD_LEFT);
            
            DB::table('users')
                ->where('id', $user->id)
                ->update(['display_id' => $displayId]);
        }
    }
};
