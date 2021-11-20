<ul class="nav nav-pills nav-justified p-3">
    @foreach ($tabs as $t)
        <li class="nav-item">
            <a class="nav-link {{ strpos(url()->current(), $t['link']) > -1 ? "active": "" }}" href="./{{ $t['link'] }}">{{ $t['text'] }}</a>
        </li>
    @endforeach
</ul>