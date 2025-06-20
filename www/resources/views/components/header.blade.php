<header class="header">
    <div class="header__logo">
        <a href="{{ route("dashboard") }}">Главная</a>
    </div>
    <nav class="nav">
        <ul>
            @auth
                <li><a href={{ route("history") }}>История проверок</a></li>
            @else
                <li><a href={{ route("login") }}>Войти</a></li>
                <li><a href={{ route("register") }}>Зарегистрироваться</a></li>
            @endauth
        </ul>
    </nav>
</header>
