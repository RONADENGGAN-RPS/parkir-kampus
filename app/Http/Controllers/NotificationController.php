<?php

namespace App\Http\Controllers;

use App\Models\LoginHistory;
use App\Models\Parkir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function fetch(Request $request)
    {
        if (!$request->ajax() && !$request->expectsJson()) {
            return redirect()->route('dashboard');
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        $role = $user->role->slug ?? 'mahasiswa';
        $dbNotifications = collect();
        $customNotifications = collect();

        // 1. Notifikasi dari tabel `notifications` (database)
        try {
            $dbNotifications = $user->notifications()
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'         => $item->id,
                        'type'       => $item->data['type'] ?? 'info',
                        'icon'       => $item->data['icon'] ?? 'bi-bell',
                        'data'       => ['message' => $item->data['message'] ?? 'Notifikasi'],
                        'read_at'    => $item->read_at ? $item->read_at->toDateTimeString() : null,
                        'created_at' => $item->created_at->toDateTimeString(),
                    ];
                });
        } catch (\Exception $e) {
            // Tabel notifications mungkin belum ada
        }

        // 2. Notifikasi kustom (fallback) berdasarkan role
        try {
            switch ($role) {
                case 'superadmin':
                case 'admin':
                    $customNotifications = LoginHistory::where('action', 'login_failed')
                        ->with('user')
                        ->latest()
                        ->take(10)
                        ->get()
                        ->map(function ($item) {
                            return [
                                'id'         => 'login-' . $item->id,
                                'type'       => 'warning',
                                'icon'       => 'bi-exclamation-triangle',
                                'data'       => ['message' => 'Login gagal: ' . ($item->user->email ?? $item->description)],
                                'read_at'    => null,
                                'created_at' => $item->created_at->toDateTimeString(),
                            ];
                        });
                    break;

                case 'petugas':
                    $customNotifications = Parkir::with('kendaraan')
                        ->latest()
                        ->take(10)
                        ->get()
                        ->map(function ($p) {
                            $icon = $p->status == 'active' ? 'bi-box-arrow-in-down'
                                : ($p->status == 'completed' ? 'bi-check-circle' : 'bi-exclamation-triangle');
                            $type = $p->status == 'active' ? 'info'
                                : ($p->status == 'completed' ? 'success' : 'danger');
                            return [
                                'id'         => 'parkir-' . $p->id,
                                'type'       => $type,
                                'icon'       => $icon,
                                'data'       => ['message' => ($p->status == 'active' ? 'Check-in: ' : ($p->status == 'completed' ? 'Check-out: ' : 'Pelanggaran: ')) . ($p->kendaraan->plat_nomor ?? '-')],
                                'read_at'    => null,
                                'created_at' => $p->check_in->toDateTimeString(),
                            ];
                        });
                    break;

                case 'mahasiswa':
                    $customNotifications = Parkir::with('kendaraan')
                        ->where('user_id', $user->id)
                        ->latest()
                        ->take(10)
                        ->get()
                        ->map(function ($p) {
                            $icon = $p->status == 'active' ? 'bi-box-arrow-in-down'
                                : ($p->status == 'completed' ? 'bi-check-circle' : 'bi-exclamation-triangle');
                            $type = $p->status == 'active' ? 'info'
                                : ($p->status == 'completed' ? 'success' : 'danger');
                            return [
                                'id'         => 'parkir-' . $p->id,
                                'type'       => $type,
                                'icon'       => $icon,
                                'data'       => ['message' => ($p->status == 'active' ? 'Sedang parkir: ' : ($p->status == 'completed' ? 'Parkir selesai: ' : 'Pelanggaran: ')) . ($p->kendaraan->plat_nomor ?? '-')],
                                'read_at'    => null,
                                'created_at' => $p->check_in->toDateTimeString(),
                            ];
                        });
                    break;
            }
        } catch (\Exception $e) {
            $customNotifications = collect();
        }

        // Gabungkan: custom di depan, database di belakang
        $allNotifications = $customNotifications->concat($dbNotifications);

        // Hitung unread: semua custom dianggap unread + database yang null read_at
        $unreadCount = $customNotifications->count()
            + $dbNotifications->where('read_at', null)->count();

        return response()->json([
            'notifications' => $allNotifications->values(),
            'unread_count'  => $unreadCount,
        ]);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        // Coba cari di database notifications
        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification && !$notification->read_at) {
            $notification->markAsRead();
        }
        // Untuk notifikasi custom, selalu kembalikan sukses
        return response()->json(['success' => true]);
    }
}
