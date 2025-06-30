@extends('layouts.client')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">Suporte Administrativo Dísponivel</div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($admins as $admin)
                            <li class="list-group-item {{ $selectedAdmin && $selectedAdmin->id == $admin->id ? 'active' : '' }}">
                                <a href="{{ route('client.chat', ['admin_id' => $admin->id]) }}">
                                    {{ $admin->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    @if($selectedAdmin)
                        Chat com {{ $selectedAdmin->name }}
                    @else
                        Selecione um administrador para iniciar o chat
                    @endif
                </div>
                <div class="card-body">
                    @if($selectedAdmin)
                        <div class="chat-messages" id="chat-messages" style="height: 400px; overflow-y: auto;">
                            @foreach($messages as $message)
                                <div class="message mb-3 {{ $message->from_user_id == Auth::id() ? 'text-end' : 'text-start' }}">
                                    <div class="message-content p-2 d-inline-block {{ $message->from_user_id == Auth::id() ? 'bg-primary text-white' : 'bg-light' }}" style="border-radius: 10px; max-width: 80%;">
                                        {{ $message->message }}
                                        <div class="message-time small text-{{ $message->from_user_id == Auth::id() ? 'light' : 'muted' }}">
                                            {{ $message->created_at->format('H:i') }}
                                            @if($message->from_user_id == Auth::id() && $message->is_read)
                                                <i class="fas fa-check-double"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <form action="{{ route('client.chat.send') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="to_user_id" value="{{ $selectedAdmin->id }}">
                            <div class="input-group">
                                <input type="text" name="message" class="form-control" placeholder="Digite sua mensagem..." required>
                                <button type="submit" class="btn btn-primary">Enviar</button>
                            </div>
                        </form>
                    @else
                        <p class="text-center">Selecione um administrador para iniciar o chat.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($selectedAdmin)
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    chatMessages.scrollTop = chatMessages.scrollHeight;
    
    // Configurar polling para novas mensagens
    let lastMessageId = {{ $messages->count() ? $messages->last()->id : 0 }};
    let isFirstLoad = true; // Flag para controlar a primeira carga
    
    setInterval(function() {
        fetch(`{{ route('client.chat.new-messages') }}?admin_id={{ $selectedAdmin->id }}&last_message_id=${lastMessageId}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            // Ignorar a primeira carga para evitar duplicação
            if (isFirstLoad) {
                isFirstLoad = false;
                if (data.messages.length > 0) {
                    lastMessageId = data.last_id;
                }
                return;
            }
            
            if (data.messages.length > 0) {
                data.messages.forEach(message => {
                    const messageDiv = document.createElement('div');
                    messageDiv.className = `message mb-3 ${message.from_user_id == {{ Auth::id() }} ? 'text-end' : 'text-start'}`;
                    
                    const messageContent = document.createElement('div');
                    messageContent.className = `message-content p-2 d-inline-block ${message.from_user_id == {{ Auth::id() }} ? 'bg-primary text-white' : 'bg-light'}`;
                    messageContent.style.borderRadius = '10px';
                    messageContent.style.maxWidth = '80%';
                    messageContent.textContent = message.message;
                    
                    const messageTime = document.createElement('div');
                    messageTime.className = `message-time small text-${message.from_user_id == {{ Auth::id() }} ? 'light' : 'muted'}`;
                    
                    // Formatar a data
                    const date = new Date(message.created_at);
                    const hours = date.getHours().toString().padStart(2, '0');
                    const minutes = date.getMinutes().toString().padStart(2, '0');
                    messageTime.textContent = `${hours}:${minutes}`;
                    
                    messageContent.appendChild(messageTime);
                    messageDiv.appendChild(messageContent);
                    chatMessages.appendChild(messageDiv);
                });
                
                // Atualizar o último ID de mensagem
                lastMessageId = data.last_id;
                
                // Rolar para o final
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        });
    }, 5000); // Verificar a cada 5 segundos
});
</script>
@endif
@endsection
