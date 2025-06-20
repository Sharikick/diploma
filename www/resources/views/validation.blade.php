<x-layout>
    <x-slot:title>Проверка</x-slot>
    <div class="check">
        <h1 class="check-title">Ошибки проверки документа</h1>

        @if(count($validation->errors))
            @foreach ($validation->errors as $error)
                <div class="error">
                    <div>
                        <strong>Место:</strong> {{ $error['location'] ?? '-' }}
                    </div>
                    <div>
                        <strong>Тип ошибки:</strong> {{ $error['type'] ?? '-' }}
                    </div>
                    <div>
                        <strong>Сообщение:</strong> {{ $error['message'] ?? '-' }}
                    </div>
                    <div>
                        <strong>Контекст:</strong>
                        @if (!empty($error['context']))
                            <ul>
                                @foreach ($error['context'] as $key => $value)
                                    <li><strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span>-</span>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p>Ошибок не найдено!</p>
        @endif
    </div>
</x-layout>
