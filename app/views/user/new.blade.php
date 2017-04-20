@extends(Config::get('syntara::views.master'))

@section('head')
<script src="{{ asset('packages/mrjuliuss/syntara/assets/js/dashboard/user.js') }}"></script>
<script>
$(document).ready(function(){
    $('#gridCalc').on('click',function(e){
        e.preventDefault();
        var lat = $("input[name='lat']").val();
        var lon = $("input[name='lon']").val();
        var base_url = "{{ url('get/grid') }}";
        $.ajax({
            type: 'get',
            url: base_url+'/'+lat+'/'+lon,
            success: function(data){
                $("input[name='grid']").val(data);
            }
        },'json');
    });
});
</script>
@stop

@section('content')
{{ Form::open(array('id'=>'create-user-form','_ipchecked'=>'1','role'=>'form')) }}
    <div class="col-lg-offset-3 col-lg-6">
        <div class="module module-default"></div>
        <div class="module-head">
            <h2>{{ trans('syntara::users.new') }}</h2>
        </div>
        <div class="module-body">
            <div class="form-group">
                <label class="control-label">{{ trans('syntara::users.username') }}</label>
                <p><input class="form-control" type="text" placeholder="{{ trans('syntara::users.username') }}" id="username" name="username"></p>
                </div>
            <div class="form-group">
                <label class="control-label">{{ trans('syntara::all.email') }}</label>
                <p><input class="form-control" type="text" placeholder="{{ trans('syntara::all.email') }}" id="email" name="email"></p>
            </div>
            <div class="form-group">
                <label class="control-label">{{ trans('syntara::all.password') }}</label>
                <p><input class="form-control" type="password" placeholder="{{ trans('syntara::all.password') }}" id="pass" name="pass"></p>
            </div>
            <div class="form-group">
                <label class="control-label">{{ trans('custom.same') }}</label>
                <p><input class="form-control" type="password" placeholder="{{ trans('syntara::all.password') }}" id="pass" name="pass_confirmation"></p>
            </div>
            <div class="form-group">
                <label class="control-label">{{ trans('syntara::users.last-name') }}</label>
                <p><input class="form-control" type="text" placeholder="{{ trans('syntara::users.last-name') }}" id="last_name" name="last_name"></p>
            </div>
            <div class="form-group">
                <label class="control-label">{{ trans('syntara::users.first-name') }}</label>
                <p><input class="form-control" type="text" placeholder="{{ trans('syntara::users.first-name') }}" id="first_name" name="first_name"></p>
            </div>
            <div class="form-group">
                <label class="control-label">{{ trans('custom.call') }} ({{ trans('custom.table.eqsl') }})</label>
                <p><input class="form-control" type="text" placeholder="{{ trans('custom.call') }} ({{ trans('custom.table.eqsl') }})" id="eQsl_call" name="eQsl_call"></p>
            </div>
            <div class="row">
                <div class="col-sm-4">
                    <label class="control-label">{{ trans('custom.lon') }}</label>
                    <p><input class="form-control" type="text" placeholder="{{ trans('custom.lon') }}" id="longitude" name="longitude"></p>
                </div>
                <div class="col-sm-4">
                    <label class="control-label">{{ trans('custom.lat') }}</label>
                    <p><input class="form-control" type="text" placeholder="{{ trans('custom.lat') }}" id="latitude" name="latitude"></p>
                </div>
                <div class="col-sm-4">
                    {{ Form::label('grid', trans('custom.grid')) }}
                    <div class="input-group">
                        {{ Form::text('grid','', array('class'=>'form-control')) }}
                        <span class="input-group-btn">
                            <button class="btn btn-danger form-control" id="gridCalc" type="button">{{ trans('custom.calculate') }}</button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <button id="add-user" type="submit" class="btn btn-primary" style="margin-top: 15px;">{{ trans('syntara::all.create') }}</button>
        </div>
    </div>
{{ Form::close() }}
@stop