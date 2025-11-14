<?php

namespace Elevate\CommerceCore\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Elevate\CommerceCore\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChannelController extends Controller
{
    /**
     * Display a listing of channels.
     */
    public function index()
    {
        $channels = Channel::orderBy('default', 'desc')
            ->orderBy('name')
            ->get();

        return view('commerce::admin.channels.index', compact('channels'));
    }

    /**
     * Show the form for creating a new channel.
     */
    public function create()
    {
        return view('commerce::admin.channels.create');
    }

    /**
     * Store a newly created channel.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'handle' => 'nullable|string|max:255|unique:channels,handle',
            'url' => 'nullable|url|max:255',
            'default' => 'boolean',
        ]);

        // Auto-generate handle if not provided
        if (empty($validated['handle'])) {
            $validated['handle'] = Str::slug($validated['name']);
        }

        $channel = Channel::create($validated);

        // If marked as default, update other channels
        if ($request->boolean('default')) {
            $channel->setAsDefault();
        }

        return redirect()
            ->route('admin.settings.channels.index')
            ->with('success', 'Channel created successfully.');
    }

    /**
     * Show the form for editing the specified channel.
     */
    public function edit(Channel $channel)
    {
        return view('commerce::admin.channels.edit', compact('channel'));
    }

    /**
     * Update the specified channel.
     */
    public function update(Request $request, Channel $channel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'handle' => 'nullable|string|max:255|unique:channels,handle,' . $channel->id,
            'url' => 'nullable|url|max:255',
            'default' => 'boolean',
        ]);

        $channel->update($validated);

        // If marked as default, update other channels
        if ($request->boolean('default')) {
            $channel->setAsDefault();
        }

        return redirect()
            ->route('admin.settings.channels.index')
            ->with('success', 'Channel updated successfully.');
    }

    /**
     * Remove the specified channel.
     */
    public function destroy(Channel $channel)
    {
        // Prevent deleting the default channel
        if ($channel->default) {
            return redirect()
                ->route('admin.settings.channels.index')
                ->with('error', 'Cannot delete the default channel.');
        }

        // Check if channel has orders
        if ($channel->orders()->exists()) {
            return redirect()
                ->route('admin.settings.channels.index')
                ->with('error', 'Cannot delete channel with existing orders.');
        }

        $channel->delete();

        return redirect()
            ->route('admin.settings.channels.index')
            ->with('success', 'Channel deleted successfully.');
    }

    /**
     * Set a channel as default.
     */
    public function setDefault(Channel $channel)
    {
        $channel->setAsDefault();

        return redirect()
            ->route('admin.settings.channels.index')
            ->with('success', "'{$channel->name}' is now the default channel.");
    }
}
