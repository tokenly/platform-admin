<nav class="nav">
@foreach (\Tokenly\PlatformAdmin\Navigation\NavBuilder::buildNav() as $nav_entry)
<a class="{{ $nav_entry['class'] }}" href="{{ route($nav_entry['route']) }}">{{ $nav_entry['label'] }}</a>@endforeach
</nav>
