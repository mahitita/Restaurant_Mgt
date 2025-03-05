<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Queue;
use Inertia\Inertia;
use Inertia\Response;

class QueueController extends Controller
{
    public function index(): Response
    {
        $queues = Queue::with('user')->orderBy('created_at', 'desc')->get();
        return Inertia::render('Admin/Queues/Index', ['queues' => $queues]);
    }

    public function update(Request $request, Queue $queue)
    {
        $request->validate(['status' => 'required|in:waiting,seated,cancelled']);
        $queue->update(['status' => $request->status]);

        return redirect()->route('admin.queues.index')->with('success', 'Queue status updated!');
    }

    public function destroy(Queue $queue)
    {
        $queue->delete();
        return redirect()->route('admin.queues.index')->with('success', 'Queue removed!');
    }
}

