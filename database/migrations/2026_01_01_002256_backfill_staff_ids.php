<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Str;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $users = User::whereNull('staff_id')->orWhere('staff_id', '')->get();

        foreach ($users as $user) {
            $user->staff_id = $this->generateStaffId();
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse action needed as we don't want to remove IDs
    }

    private function generateStaffId()
    {
        do {
            $id = 'S' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (User::where('staff_id', $id)->exists());

        return $id;
    }
};
