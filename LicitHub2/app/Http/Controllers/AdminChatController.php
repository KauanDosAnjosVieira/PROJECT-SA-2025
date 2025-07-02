<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminChatController extends Controller
{
    public function index()
    {
        $clients = User::where('user_type', 'customer')->get();
        $selectedClient = request('client_id') ? User::find(request('client_id')) : null;
        
        $messages = [];
        if ($selectedClient) {
            $messages = ChatMessage::where(function($query) use ($selectedClient) {
                $query->where('from_user_id', Auth::id())
                      ->where('to_user_id', $selectedClient->id);
            })->orWhere(function($query) use ($selectedClient) {
                $query->where('from_user_id', $selectedClient->id)
                      ->where('to_user_id', Auth::id());
            })->orderBy('created_at')->get();
        }
        
        return view('admin.chat.index', compact('clients', 'selectedClient', 'messages'));
    }
    
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'to_user_id' => 'required|exists:users,id',
        ]);
        
        ChatMessage::create([
            'from_user_id' => Auth::id(),
            'to_user_id' => $request->to_user_id,
            'message' => $request->message,
        ]);
        
        return redirect()->route('admin.chat', ['client_id' => $request->to_user_id])
            ->with('success', 'Mensagem enviada.');
    }
}
