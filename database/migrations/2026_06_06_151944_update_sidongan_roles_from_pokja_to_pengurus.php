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
        // Mapping role lama ke role baru
        $roleMapping = [
            'pokja1' => 'pengurus_1',
            'pokja2' => 'pengurus_2',
            'pokja3' => 'pengurus_3',
            'pokja4' => 'pengurus_4',
        ];
        
        foreach ($roleMapping as $oldRole => $newRole) {
            DB::table('users')
                ->where('sidongan_role', $oldRole)
                ->update(['sidongan_role' => $newRole]);
        }
        
        // Update disposisi_data di dokumen (JSON field)
        $documents = DB::table('sidongan_documents')
            ->whereNotNull('disposisi_data')
            ->get();
        
        foreach ($documents as $doc) {
            $dispo = json_decode($doc->disposisi_data, true);
            if (isset($dispo['target_roles']) && is_array($dispo['target_roles'])) {
                $updated = false;
                foreach ($dispo['target_roles'] as $key => $role) {
                    if (isset($roleMapping[$role])) {
                        $dispo['target_roles'][$key] = $roleMapping[$role];
                        $updated = true;
                    }
                }
                if ($updated) {
                    DB::table('sidongan_documents')
                        ->where('id', $doc->id)
                        ->update(['disposisi_data' => json_encode($dispo)]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $roleMapping = [
            'pengurus_1' => 'pokja1',
            'pengurus_2' => 'pokja2',
            'pengurus_3' => 'pokja3',
            'pengurus_4' => 'pokja4',
        ];
        
        foreach ($roleMapping as $newRole => $oldRole) {
            DB::table('users')
                ->where('sidongan_role', $newRole)
                ->update(['sidongan_role' => $oldRole]);
        }
    }
};