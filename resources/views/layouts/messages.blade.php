@if(isset($messages) && is_array($messages))
    @foreach ($messages as $message)
        @if(isset($message['type']) && isset($message['text']))
            <div  class="alert alert-{{$message['type']}}">{{ $message['text'] }}</div>
        @endif
    @endforeach
@endif
