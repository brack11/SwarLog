@extends(Config::get('syntara::views.master'))

@section('head')
<script>
	$('document').ready(function(){
		$('form#confForm').submit(function(e){
			e.preventDefault();
			var formData = $(this).serialize();
			$.ajax({
				type: 'post',
				data: formData,
				url: "{{ url('config') }}",
				success: function(data) {
					$('.alert').show();
				}
			},'json');
		});

		$('#gridCalc').on('click',function(e){
			e.preventDefault();
			var lat = $("input[name='uLat']").val();
			var lon = $("input[name='uLon']").val();
			var base_url = "{{ url('get/grid') }}";
			$.ajax({
				type: 'get',
				url: base_url+'/'+lat+'/'+lon,
				success: function(data){
					$("input[name='uGrid']").val(data);
				}
			},'json');
		});
	});
</script>
@stop

@section('content')
	<div class="container" id="main-container">
		<div class="row">
			<div class="col-xs-12 col-md-8 col-md-offset-2">
			<div class="alert alert-success fade-in" style="display:none;">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<strong>{{ trans('custom.success') }}</strong> {{ trans('custom.conf.saved') }}
			</div>
			{{ Form::open(array('id'=>'confForm','class'=>'form-horizontal bv-form')) }}
				<div class="panel panel-default">
					<div class="panel-heading">{{ trans('custom.personal.info') }}</div>
					<div class="panel-body">
						<div class="form-group has-feedback">
							{{ Form::label('uName', trans('custom.name'), array('class'=>'col-sm-2 control-label')) }}
							<div class="col-sm-10">
								{{ Form::text('uName', (isset($confs['uName']))?$confs['uName']:'', array('class'=>'form-control')) }}
							</div>
						</div>
						<div class="form-group has-feedback">
							{{ Form::label('uCall', trans('custom.call'), array('class'=>'col-sm-2 control-label')) }}
							<div class="col-sm-10">
								{{ Form::text('uCall', (isset($confs['uCall']))?$confs['uCall']:'', array('class'=>'form-control')) }}
							</div>
						</div>
						<div class="form-group has-feedback">
							{{ Form::label('uLat', trans('custom.lat'), array('class'=>'col-sm-2 control-label')) }}
							<div class="col-sm-10">
								{{ Form::text('uLat', (isset($confs['uLat']))?$confs['uLat']:'', array('class'=>'form-control')) }}
							</div>
						</div>
						<div class="form-group has-feedback">
							{{ Form::label('uLon', trans('custom.lon'), array('class'=>'col-sm-2 control-label')) }}
							<div class="col-sm-10">
								{{ Form::text('uLon', (isset($confs['uLon']))?$confs['uLon']:'', array('class'=>'form-control')) }}
							</div>
						</div>
						<div class="form-group has-feedback">
							{{ Form::label('uGrid', trans('custom.grid'), array('class'=>'col-sm-2 control-label')) }}
							<div class="col-sm-10">
								<div class="input-group">
									{{ Form::text('uGrid', (isset($confs['uGrid']))?$confs['uGrid']:'', array('class'=>'form-control')) }}
									<span class="input-group-btn">
										<button class="btn btn-danger form-control" id="gridCalc" type="button">{{ trans('custom.calculate') }}</button>
									</span>
								</div>
							</div>
						</div>
						<div class="form-group has-feedback">
							{{ Form::label('uQslVia', trans('custom.qsl.route'), array('class'=>'col-sm-2 control-label')) }}
							<div class="col-sm-10">
								{{ Form::select('uQslVia',array(
									'B'=>trans('custom.bureau'),
									'D'=>trans('custom.direct'),
									'E'=>trans('custom.eqsl.cc'),
									'M'=>trans('custom.manager')),
									(isset($confs['uQslVia']))?$confs['uQslVia']:'E', array('class'=>'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						{{ trans('custom.google.api') }} ({{ trans('custom.not.change') }})
					</div>
					<div class="panel-body">
						<div class="form-group has-feedback">
							{{ Form::label('gApi', trans('custom.api.key'), array('class'=>'col-sm-2 control-label')) }}
							<div class="col-sm-10">
								{{ Form::text('gApi', (isset($confs['gApi']))?$confs['gApi']:'', array('class'=>'form-control')) }}
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						{{ trans('custom.eqsl') }}
					</div>
					<div class="panel-body">
						<div class="form-group has-feedback">
							{{ Form::label('qUser', trans('custom.eqsl.username'), array('class'=>'col-sm-2 control-label')) }}
							<div class="col-sm-10">
								{{ Form::text('qUser', (isset($confs['qUser']))?$confs['qUser']:'', array('class'=>'form-control')) }}
							</div>
						</div>
						<div class="form-group has-feedback">
							{{ Form::label('qPassword', trans('custom.eqsl.password'), array('class'=>'col-sm-2 control-label')) }}
							<div class="col-sm-10">
								{{ Form::text('qPassword', (isset($confs['qPassword']))?$confs['qPassword']:'', array('class'=>'form-control')) }}
							</div>
						</div>
						<div class="col-sm-10 col-sm-offset-2">
							<!-- <div class="checkbox"> -->
								{{ Form::checkbox('qDynamic', '1', (isset($confs['qDynamic']) && $confs['qDynamic'] == 1) ? 'checked' : '') }}
								{{ trans('custom.eqsl.dynamic') }}
							<!-- </div> -->
						</div>
					</div>
				</div>
				<div class="col-sm-2 col-sm-offset-5">
					{{ Form::submit(trans('custom.save'), array('class'=>'btn btn-danger')) }}
				</div>
			{{ Form::close() }}
			</div>
		</div>
	</div>
@stop