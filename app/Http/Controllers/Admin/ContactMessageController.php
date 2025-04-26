<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of the contact messages.
     */
    public function index(Request $request)
    {
        $contactMessages = ContactMessage::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($request->filter, function ($query, $filter) {
                if ($filter === 'unread') {
                    $query->where('is_read', false);
                } elseif ($filter === 'read') {
                    $query->where('is_read', true);
                }
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $unreadCount = ContactMessage::where('is_read', false)->count();

        return Inertia::render('Admin/ContactMessages', [
            'contactMessages' => $contactMessages,
            'unreadCount' => $unreadCount,
            'filters' => $request->only(['search', 'filter']),
        ]);
    }

    /**
     * Display the specified contact message.
     */
    public function show(ContactMessage $contactMessage)
    {
        // Mark as read when viewed
        if (!$contactMessage->is_read) {
            $contactMessage->update(['is_read' => true]);
        }

        return Inertia::render('Admin/ContactMessageShow', [
            'contactMessage' => $contactMessage->load('user'),
        ]);
    }

    /**
     * Mark the specified contact message as read.
     */
    public function markAsRead(ContactMessage $contactMessage)
    {
        $contactMessage->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Message marked as read.');
    }

    /**
     * Mark multiple contact messages as read.
     */
    public function markMultipleAsRead(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:contact_messages,id',
        ]);

        ContactMessage::whereIn('id', $validated['ids'])->update(['is_read' => true]);

        return redirect()->back()->with('success', count($validated['ids']) . ' messages marked as read.');
    }

    /**
     * Remove the specified contact message from storage.
     */
    public function destroy(ContactMessage $contactMessage)
    {
        $contactMessage->delete();

        return redirect()->route('admin.contact-messages.index')->with('success', 'Message deleted successfully.');
    }
}
