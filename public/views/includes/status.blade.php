@if (session('status'))
<div class="panel info-panel">
    <div class="body">
        {{ session('status') }}
    </div>
</div>
@endif
