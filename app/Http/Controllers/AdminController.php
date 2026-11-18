<?php

namespace App\Http\Controllers;

use App\Models\ApplicationSetting;
use App\Models\AuditLog;
use App\Models\TransactionRecord;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        $databaseAvailable = true;
        try {
            DB::select('SELECT 1');
        } catch (\Throwable) {
            $databaseAvailable = false;
        }

        return view('admin.dashboard', [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('status', 'active')->count(),
            'suspendedUsers' => User::where('status', 'suspended')->count(),
            'transactionCount' => TransactionRecord::count(),
            'databaseAvailable' => $databaseAvailable,
            'applicationAvailable' => true,
            'chainId' => 11155111,
            'networkName' => 'Ethereum Sepolia',
            'settings' => ApplicationSetting::whereIn('setting_key', [
                'maintenance_notice',
                'support_status',
            ])->get()->keyBy('setting_key'),
        ]);
    }

    public function users()
    {
        return view('admin.users', [
            'users' => User::latest()->paginate(20),
        ]);
    }

    public function updateStatus(Request $request, User $user)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['active', 'suspended'])],
        ]);

        abort_unless($user->id !== $request->user()->id, 422, 'Administrators cannot suspend their own account.');

        $user->forceFill(['status' => $validated['status']])->save();

        AuditLog::create([
            'actor_id' => $request->user()->id,
            'event_type' => 'account_status_changed',
            'outcome' => 'success',
            'context' => [
                'target_user_id' => $user->id,
                'status' => $validated['status'],
            ],
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        return redirect()->route('admin.users')->with('status', 'Account status updated.');
    }

    public function logs()
    {
        return view('admin.logs', [
            'logs' => AuditLog::latest()->paginate(30),
        ]);
    }

    public function settings(Request $request)
    {
        $allowedKeys = ['maintenance_notice', 'support_status'];

        $validated = $request->validate([
            'maintenance_notice' => ['nullable', 'string', 'max:255'],
            'support_status' => ['nullable', Rule::in(['operational', 'degraded', 'maintenance'])],
        ]);

        foreach ($allowedKeys as $key) {
            if (! array_key_exists($key, $validated) || $validated[$key] === null) {
                continue;
            }

            ApplicationSetting::updateOrCreate(
                ['setting_key' => $key],
                [
                    'setting_value' => $validated[$key],
                    'updated_by' => $request->user()->id,
                ],
            );
        }

        AuditLog::create([
            'actor_id' => $request->user()->id,
            'event_type' => 'application_settings_changed',
            'outcome' => 'success',
            'context' => ['setting_keys' => array_keys($validated)],
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        return redirect()->route('admin.dashboard')->with('status', 'Non-secret settings updated.');
    }
}
