<x-layout>
    <x-slot:title>История проверок</x-slot>
    <div class="history">
        <h1 class="history-title">История ваших проверок</h1>

        <div class="history-search-bar">
            <input type="text" class="history-search-input" placeholder="Поиск по названию файла..." />
        </div>

        @if ($validations->isEmpty())
            <p>Вы ещё не загружали ни одного документа.</p>
        @else
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Файл</th>
                        <th>Дата загрузки</th>
                        <th>Действие</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($validations as $validation)
                        <tr>
                            <td>1.docx</td>
                            <td>2025-06-01</td>
                            <td>
                                <a href="{{ route('validation', ['validation' => $validation->id]) }}" class="history-btn-details">Подробнее</>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-layout>
