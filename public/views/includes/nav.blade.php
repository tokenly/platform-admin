<?php $buildActiveClass = function($route_suffix) { 
    $l = strlen('platform.admin.'.$route_suffix);
    return ('platform.admin.'.$route_suffix == substr(Route::current()->getName(), 0, $l)) ? ' active' : ''; 
}; ?>
<nav class="nav">
    <a class="brand" href="{{ route('platform.admin.home') }}">Tokenly Platform Administration</a><!-- 
 --><a class="nav{{ $buildActiveClass('home') }}" href="{{ route('platform.admin.home') }}">Home</a><!-- 
 --><a class="nav{{ $buildActiveClass('user.index') }}" href="{{ route('platform.admin.user.index') }}">Users</a><!-- 
 --><?php if (env('PLATFORM_ADMIN_DEVELOPMENT_MODE_ENABLED')) {?><!--

        --><a class="nav{{ $buildActiveClass('xchain') }}" href="{{ route('platform.admin.xchain') }}">XChain</a><!--
        --><a class="nav{{ $buildActiveClass('promise') }}" href="{{ route('platform.admin.promise.index') }}">Promises</a><!--

--><?php } ?><!-- 
 -->
 </nav>
