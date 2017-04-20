<html>
    <head>
        <!-- link rel="stylesheet" href="{{ asset('bootstrap/css/bootstrap.min.css') }}" / -->
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet">
        @if(Config::get('syntara::config.direction') === 'rtl')
            <link rel="stylesheet" href="{{ asset('packages/mrjuliuss/syntara/assets/css/bootstrap-rtl.min.css') }}" media="all">
            <link rel="stylesheet" href="{{ asset('packages/mrjuliuss/syntara/assets/css/base-rtl.css') }}" media="all">
        @endif
        <link rel="stylesheet" href="{{ asset('packages/mrjuliuss/syntara/assets/css/toggle-switch.css') }}" />
        
        <!-- link rel="stylesheet" href="{{ asset('css/bootstrapValidator.min.css') }}"/ -->

        <link rel="stylesheet" href="{{ asset('packages/mrjuliuss/syntara/assets/css/base.css') }}" media="all">

        {{ HTML::style('css/style.css') }}

        @if (!empty($favicon))
        <link rel="icon" {{ !empty($faviconType) ? 'type="' . $faviconType . '"' : '' }} href="{{ $favicon }}" />
        @endif
        @include('common.scripts')
        <!-- script type="text/javascript" src="{{ asset('js/jquery-2.1.3.min.js') }}"></script -->
        <!-- script type="text/javascript" src="{{ asset('bootstrap/js/bootstrap.min.js') }}"></script -->
        <!-- script type="text/javascript" src="{{ asset('js/bootstrapValidator.min.js') }}"></script -->
        @yield('head')
        <title>{{ (!empty($siteName)) ? $siteName : "Syntara"}} - {{isset($title) ? $title : '' }}</title>
    </head>
    <body>
        @include(Config::get('syntara::views.header'))
        {{ isset($breadcrumb) ? Breadcrumbs::create($breadcrumb) : ''; }}
        <div id="content">
            @yield('content')
        </div>
        @yield('footer')
    </body>
</html>