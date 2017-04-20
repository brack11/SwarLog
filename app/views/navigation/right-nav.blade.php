@if (!Sentry::check())
    <li><a href="{{ url('user') }}"><span class="text"><i class="glyphicon glyphicon-edit"></i> {{ trans('custom.register') }} </span></a></li>
@endif