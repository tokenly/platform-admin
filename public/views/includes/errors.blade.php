@if (count($errors) > 0)
<div class="panel error-panel">
    <h5 class="heading">There were some errors.</h5>
    <div class="body">
    @foreach ($errors->all() as $error)
        <div class="error">{{ $error }}</div>
    @endforeach
    </div>
</div>
@endif
