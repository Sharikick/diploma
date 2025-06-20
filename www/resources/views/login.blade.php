<x-layout>
    <x-slot:title>Авторизация</x-slot>
    <div class="auth">
        <div class="auth__inner">
            <h2 class="auth__title">Авторизация</h2>
            <form class="auth__form" action="{{ route("login") }}" method="post">
                @csrf
                <label class="form__label" for="email">Email:</label>
                <input class="form__field" type="email" id="email" name="email" required />

                <label class="form__label" for="password">Пароль:</label>
                <input class="form__field" type="password" id="password" name="password" required />

                <button class="form__button" type="submit">Войти</button>
            </form>
        </div>
    </div>
</x-layout>
