@if(isset($messages) && is_array($messages))
    @foreach ($messages as $message)
        @if(isset($messages['type']) && isset($messages['text']))
            <div  class="alert alert-{{$message['type']}}">{{ $message['text'] }}</div>
        @endif
    @endforeach
@endif
