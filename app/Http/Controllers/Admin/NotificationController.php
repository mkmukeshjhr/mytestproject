<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendNotificationJob;
use App\Models\Notification;
use App\services\FCMService;
use App\services\OneSignalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Exception;

define('LOGINPATH', '/admin/login');

class NotificationController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;
    public function addNotification()
    {
        return view('pages.notification-list');
    }

    public function addNotificationApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                Notification::create([
                    'title' => $req->title,
                    'description' => $req->did,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);
                return redirect()->route('notifications');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    //Get Skill Api

    public function getNotification(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $notifications = Notification::query();
                $notifications->orderBy("id", "DESC");
                $notificationCount = $notifications->count();
                $notifications->skip($paginationStart);
                $notifications->take($this->limit);
                $notifications = $notifications->get();
                $totalPages = ceil($notificationCount / $this->limit);
                $totalRecords = $notificationCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                $users = DB::Table('users')
                ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                    ->where('isDelete', '=', false)
                    ->where('isActive', '=', true)
                // ->where('user_roles.roleId', '=', 3)
                    ->select('users.*','user_roles.roleId')
                    ->get();

                //  filters by wallet never recharged
                $wallet_empty = DB::table('users')
                ->leftJoin('user_wallets', 'users.id', '=', 'user_wallets.userId')
                ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                ->whereNull('user_wallets.userId') // Users who don't have any wallet records
                ->where('users.isDelete', '=', false)
                ->where('users.isActive', '=', true)
                ->where('user_roles.roleId', '=', 3)
                ->select('users.*')
                ->get();

                $usersNotUsedFree = DB::table('users')
                ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('chatrequest')
                        ->whereColumn('chatrequest.userId', 'users.id')
                        ->where('chatrequest.chatStatus', '=', 'Completed');
                })
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('callrequest')
                        ->whereColumn('callrequest.userId', 'users.id')
                        ->where('callrequest.callStatus', '=', 'Completed');
                })
                ->where('users.isDelete', '=', false)
                ->where('users.isActive', '=', true)
                ->where('user_roles.roleId', '=', 3)
                ->select('users.*')
                ->get();

                return view('pages.notification-list', compact('notifications', 'users', 'totalPages', 'totalRecords', 'start', 'end', 'page','wallet_empty','usersNotUsedFree'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function editNotification()
    {
        return view('pages.notification-list');
    }

    public function editNotificationApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $notification = Notification::find($req->filed_id);
                if ($notification) {
                    $notification->title = $req->title;
                    $notification->description = $req->did;
                    $notification->update();
                }
                return redirect()->route('notifications');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function notifcationStatus(Request $request)
    {
        return view('pages.notification-list');
    }

    public function notifcationStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {

                $notification = Notification::find($request->status_id);
                if ($notification) {
                    $notification->isActive = !$notification->isActive;
                    $notification->update();
                }
                return redirect()->route('notifications');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }



    public function sendNotification(Request $req)
    {
        try {
            $notification = Notification::find($req->notification_id);
            $req->userIds = ($req->userIds === ['all']) ? [] : $req->userIds;
            $authUserId = 1;
            // ✅ If auto_send_time is provided → save in scheduler table instead of sending now
            if (!empty($req->auto_send_time)) {
                $this->saveScheduledNotification($req, $notification, $authUserId);
                return response()->json([
                    'success' => ['user_notifications_scheduler created successfully'],
                ]);
            }
    
            if ($req->userIds && count(json_decode(json_encode($req->userIds))) > 0) {
                // Dispatch jobs for specific users
                foreach (json_decode(json_encode($req->userIds)) as $user) {
                    SendNotificationJob::dispatch($notification, $user, $authUserId);
                }
            } elseif ($req->role && $req->role == 'User') {
                // Dispatch jobs for all users
                $this->dispatchNotificationsForRole($notification, 3, $authUserId);
            } elseif ($req->role && $req->role == 'Astrologer') {
                // Dispatch jobs for all astrologers
                $this->dispatchNotificationsForRole($notification, 2, $authUserId);
            } elseif ($req->role == 'User Never Recharged') {
                // Dispatch jobs for users who never recharged
                $this->dispatchNotificationsForNeverRecharged($notification, $authUserId);
            } elseif ($req->role == 'User Not Used Free Chat/Call') {
                // Dispatch jobs for users who haven't used free chat/call
                $this->dispatchNotificationsForNotUsedFreeChatOrCall($notification, $authUserId);
            } else {
                // Dispatch jobs for all users by default
                $this->dispatchNotificationsForAll($notification, $authUserId);
            }
    
            return response()->json([
                'success' => ['Notifications are being processed in the background'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => [$e->getMessage()]]);
        }
    }
    
    private function dispatchNotificationsForRole($notification, $roleId, $authUserId)
    {
        $userIds = DB::table('user_device_details')
            ->join('user_roles', 'user_roles.userId', '=', 'user_device_details.userId')
            ->where('user_roles.roleId', '=', $roleId)
            ->where('isActive', 1)
            ->where('isDelete', 0)
            ->pluck('user_device_details.userId');
    
        $this->dispatchNotificationsForUsers($notification, $userIds, $authUserId);
    }
    
    private function dispatchNotificationsForNeverRecharged($notification, $authUserId)
    {
        $userIds = DB::table('user_device_details')
            ->join('user_roles', 'user_roles.userId', '=', 'user_device_details.userId')
            ->where('user_roles.roleId', '=', 3)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_wallets')
                    ->whereRaw('user_device_details.userId = user_wallets.userId');
            })
            ->pluck('user_device_details.userId');
    
        $this->dispatchNotificationsForUsers($notification, $userIds, $authUserId);
    }
    
    private function dispatchNotificationsForNotUsedFreeChatOrCall($notification, $authUserId)
    {
        $userIds = DB::table('user_device_details')
            ->join('user_roles', 'user_roles.userId', '=', 'user_device_details.userId')
            ->where('user_roles.roleId', '=', 3)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('chatrequest')
                    ->whereRaw('user_device_details.userId = chatrequest.userId')
                    ->orWhereExists(function ($subquery) {
                        $subquery->select(DB::raw(1))
                            ->from('callrequest')
                            ->whereRaw('user_device_details.userId = callrequest.userId');
                    });
            })
            ->pluck('user_device_details.userId');
    
        $this->dispatchNotificationsForUsers($notification, $userIds, $authUserId);
    }
    
    private function dispatchNotificationsForAll($notification, $authUserId)
    {
        $userIds = DB::table('user_device_details')->pluck('userId');
        
        $this->dispatchNotificationsForUsers($notification, $userIds, $authUserId);
        
    }
    
    private function dispatchNotificationsForUsers($notification, $userIds, $authUserId)
    {
        foreach ($userIds as $userId) {
            SendNotificationJob::dispatch($notification, $userId, $authUserId);
        }
       
    }
    private function saveScheduledNotification($req, $notification, $authUserId)
    {
    $userIds = [];

    // If users are selected, use them
    if ($req->userIds && count(json_decode(json_encode($req->userIds))) > 0) {
        $userIds = json_decode(json_encode($req->userIds));
    }
    // If role is selected, fetch users by role
    elseif ($req->role && $req->role == 'User') {
        $userIds = DB::table('user_roles')->where('roleId', 3)->pluck('userId');
    } elseif ($req->role && $req->role == 'Astrologer') {
        $userIds = DB::table('user_roles')->where('roleId', 2)->pluck('userId');
    } elseif ($req->role && $req->role == 'User Never Recharged') {
        $userIds = DB::table('user_roles')
            ->where('roleId', 3)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('user_wallets')
                    ->whereRaw('user_roles.userId = user_wallets.userId');
            })
            ->pluck('userId');
    } elseif ($req->role && $req->role == 'User Not Used Free Chat/Call') {
        $userIds = DB::table('user_roles')
            ->where('roleId', 3)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('chatrequest')
                    ->whereRaw('user_roles.userId = chatrequest.userId')
                    ->orWhereExists(function ($subquery) {
                        $subquery->select(DB::raw(1))
                            ->from('callrequest')
                            ->whereRaw('user_roles.userId = callrequest.userId');
                    });
            })
            ->pluck('userId');
    } else {
        // All users
        $userIds = DB::table('user_device_details')->pluck('userId');
    }

    foreach ($userIds as $userId) {
        DB::table('user_notifications_scheduler')->insert([
            'userId'            => $userId,
            'title'             => $notification->title ?? null,
            'description'       => $notification->description ?? null,
            'notificationId'    => $notification->id,
            'chatRequestId'     => null,
            'callRequestId'     => null,
            'isActive'          => 1,
            'isDelete'          => 0,
            'notification_type' => 'scheduled',
            'is_read'           => 0,
            'created_at'        => now(),
            'updated_at'        => now(),
            'createdBy'         => $authUserId,
            'modifiedBy'        => $authUserId,
            'status'            => 'pending',
            'auto_send_time'    => $req->auto_send_time, // store scheduled time
        ]);
    }
    }


}
