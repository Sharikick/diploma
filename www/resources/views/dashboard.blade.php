<x-layout>
    <x-slot:title>Главная</x-slot>
    <div class="dashboard">
        <h1 class="dashboard__title">Панель управления</h1>

        <div class="dashboard__grid">
            <div class="dashboard__card">
                <h3>Загрузите файл .docx</h3>
                <form class="card__upload__form" action="/upload" method="post" enctype="multipart/form-data">
                    @csrf
                    <input class="card__upload__input" type="file" name="document" accept=".docx" required />
                    <button class="card__upload__button" type="submit">Проверить оформление</button>
                </form>
            </div>

            <div class="dashboard__card">
                <h3>История проверок</h3>
                <ul class="history-list">
                    <li>1.docx — 2 дня назад</li>
                    <li>2.docx — 2 дня назад</li>
                    <li>3.docx — 2 дня назад</li>
                </ul>
            </div>
        </div>
    </div>
</x-layout>
