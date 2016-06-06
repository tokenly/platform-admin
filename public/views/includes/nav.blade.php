<?php $buildActiveClass = function($route_suffix) { return ('platform.admin.'.$route_suffix == Route::current()->getName()) ? ' active' : ''; }; ?>
<nav class="nav">
    <a class="brand" href="{{ route('platform.admin.home') }}">Tokenly Platform Administration</a><!-- 
 --><a class="nav{{ $buildActiveClass('home') }}" href="{{ route('platform.admin.home') }}">Home</a><!-- 
 --><a class="nav{{ $buildActiveClass('user.index') }}" href="{{ route('platform.admin.user.index') }}">Users</a><!-- 
 --><?php if (env('PLATFORM_ADMIN_DEVELOPMENT_MODE_ENABLED')) {?><a class="nav{{ $buildActiveClass('xchain') }}" href="{{ route('platform.admin.xchain') }}">XChain</a><?php } ?><!-- 
 -->
 </nav>
