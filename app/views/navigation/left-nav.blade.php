@if (Sentry::check())
	<li class="dropdown" >
	    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="glyphicon glyphicon-cog"></i> <span>{{ trans('custom.config') }}</span></a>
	    <ul class="dropdown-menu">
	        <li><a href="{{ url('config') }}">{{ trans('custom.config') }}</a></li>
	        @if ($currentUser->hasAccess('administrator'))
	        	<li><a href="{{ url('config/prefix') }}">{{ trans('custom.config.prefix') }}</a></li>
	        @endif
	        <li><a href="{{ url('backup') }}">{{ trans('custom.backup') }} </a></li>
	        
	    </ul>
	</li>
	<li class="dropdown" >
	    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="glyphicon glyphicon-wrench"></i> <span>{{ trans('custom.tools') }}</span></a>
	    <ul class="dropdown-menu">
	        <li><a href="#" onclick="popUp('http://www.dxsummit.fi/#/')">{{ trans('custom.cluster') }}</a></li>
	        <li><a href="{{ url('tools/eqsl') }}">{{ trans('custom.eqsl') }} </a></li>
	    </ul>
	</li>
	<li class="dropdown" >
	    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="glyphicon glyphicon-search"></i> <span>{{ trans('custom.search') }}</span></a>
	    <ul class="dropdown-menu">
	        <li><a href="{{ url('search') }}">{{ trans('custom.advanced.search') }} </a></li>
	    </ul>
	</li>
@endif
