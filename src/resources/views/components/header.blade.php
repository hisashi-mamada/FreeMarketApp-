<header class="toppage-header">
    <div class="toppage-header-icon">
        <a href="{{ route('items.index') }}">
            <img src="{{ asset('images/logo.svg') }}" alt="COACHTECHロゴ">
        </a>
    </div>


    <div class="toppage-header-search">
        <form method="GET" action="{{ route('items.index') }}" class="search-box">
            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="なにをお探しですか？">
            <button type="submit">検索</button>
        </form>
    </div>


    <nav class="toppage-header-nav">
        @guest
        <a href="{{ route('login') }}" class="toppage-nav-link">ログイン</a>
        @endguest

        @auth
        <form method="POST" action="{{ route('logout') }}" class="toppage-nav-form">
            @csrf
            <button type="submit" class="toppage-nav-link logout-button">
                ログアウト
            </button>
        </form>
        @endauth

        <a href="/mypage" class="toppage-nav-link">マイページ</a>
        <a href="/sell" class="toppage-nav-button">出品</a>
    </nav>

</header>