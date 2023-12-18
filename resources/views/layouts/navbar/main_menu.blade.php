<div class="dropdown me-4">
    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu2" data-bs-toggle="dropdown" aria-expanded="false">
        {{ __('Main menu') }}
    </button>
    @if ($menu = \Menu::getMenu())
        <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
        @foreach($menu as $menu_item)
                <li><a class="dropdown-item" href="{{ $menu_item['url'] }}">{{ $menu_item['title'] }}</a></li>
        @endforeach
        </ul>
    @endif
</div>
