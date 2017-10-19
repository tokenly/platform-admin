@if ($pagination['pages_count'] > 1)
{{--       
count
count_per_page
offset
pages_count
 --}}
<div class="row">
    Showing page {{ ($pagination['offset'] + 1) }} of {{ $pagination['pages_count'] }}.
</div>
<div class="row">
    <div class="one column"><a class="button" href="{{ route($pagination['route_prefix'].'.index', ['pg' => max(0, $pagination['offset'] - 1)] + request()->query())  }}">&lt;&lt;</a></div>

    @php
        $_start_offset = 0;
        $_end_offset = $pagination['pages_count'] - 1;
        $_length = $_end_offset - $_start_offset + 1;
        if ($_length > 10) {
            $middle = $pagination['offset'];
            if ($middle < 5) { $middle = 5; }
            if ($_end_offset - $middle < 4) { $middle = $_end_offset - 4; }
            // $middle = $_start_offset + floor($_length / 2);
            $_end_offset = $middle + 4;
            $_start_offset = $middle - 5;
            if ($_start_offset < 0) {
                $_start_offset = 0;
                $_end_offset = 9;
            } else if ($_end_offset > $pagination['count'] - 1) {
                $_end_offset = $pagination['count'] - 1;
                $_start_offset = $_end_offset - 10;
            }
            $_length = 10;
        }
        $start_padding = floor((10 - $_length) / 2);
        $end_padding = 10 - $_length - $start_padding;
    @endphp
    @for ($i = 0; $i < $start_padding; $i++)
        <div class="one column">&nbsp;</div>
    @endfor
    @for ($i = $_start_offset; $i <= $_end_offset; $i++)
        <div class="one column"><a class="button{{ intval($pagination['offset']) == $i ? ' button-primary' : '' }}" href="{{ route($pagination['route_prefix'].'.index', ['pg' => $i] + request()->query())  }}">{{ $i + 1}}</a></div>
    @endfor
    @for ($i = 0; $i < $end_padding; $i++)
        <div class="one column">&nbsp;</div>
    @endfor

    <div class="one column"><a class="button" href="{{ route($pagination['route_prefix'].'.index', ['pg' => min($pagination['pages_count']-1, $pagination['offset'] + 1)] + request()->query())  }}">&gt;&gt;</a></div>
</div>
@endif
