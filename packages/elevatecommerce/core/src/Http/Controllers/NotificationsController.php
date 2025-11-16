<?php

namespace ElevateCommerce\Core\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class NotificationsController extends Controller
{
    /**
     * Display notifications index
     */
    public function index()
    {
        return view('core::admin.notifications.index');
    }

    /**
     * Show a specific notification
     */
    public function show($id)
    {
        $notification = auth('admin')->user()->notifications()->findOrFail($id);
        
        // Mark as read
        $notification->markAsRead();

        // Redirect based on notification data
        if (isset($notification->data['data']['action_url'])) {
            return redirect($notification->data['data']['action_url']);
        } elseif (isset($notification->data['data']['url'])) {
            return redirect($notification->data['data']['url']);
        }

        return redirect()->route('admin.notifications.index');
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $notification = auth('admin')->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth('admin')->user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read');
    }

    /**
     * Delete a notification
     */
    public function delete($id)
    {
        $notification = auth('admin')->user()->notifications()->findOrFail($id);
        $notification->delete();

        return redirect()->back()->with('success', 'Notification deleted');
    }
}
