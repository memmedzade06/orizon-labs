<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends AdminController
{
    public function index(Request $request): View
    {
        $messages = ContactMessage::query()
            ->when(array_key_exists((string) $request->input('type'), ContactMessage::TYPES), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->input('status') === 'unread', fn ($q) => $q->whereNull('read_at'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.messages.index', compact('messages'));
    }

    /** Açılanda avtomatik "oxundu" olur */
    public function show(ContactMessage $message): View
    {
        if (! $message->isRead()) {
            $message->forceFill(['read_at' => now()])->save();
        }

        return view('admin.messages.show', ['item' => $message]);
    }

    public function destroy(ContactMessage $message): RedirectResponse
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Müraciət silindi.');
    }
}
