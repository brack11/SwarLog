@extends(Config::get('syntara::views.master'))

@section('head')

<script>
	$("document").ready(function(){
		$("input[name=qFreq]").on('change',function(e){
			e.preventDefault();
			var khz = $("input[name=qFreq]").val();
			$.ajax({
				type: 'post',
				url: "{{ url('freq/band') }}",
				data: "f="+khz,
				success: function(data){
					$("#bandSelect option[value='"+data+"']").attr("selected", "selected");
				}
			},'json');
		});

		$(':checkbox').on('change',function(){
			$('.module-body button').show();
			if(!$(':checkbox').is(':checked')) {
				$('.module-body button').hide();
			}
		});

		$(document).on('change',"input.form-call",function(e){
			e.preventDefault();
			var call = $(this).val();
			var baseUrl = "{{ url('call') }}";
			$(this).siblings('.nav').addClass('currentDOM');
			$.ajax({
				type: 'get',
				url: baseUrl+'/'+call,
				success: function(data){
					$(document).find('.currentDOM').html(data).removeClass('currentDOM');
					if(data.length > 0) $('#submit_btn').show();
					// alert(data);
				}
			},'html');
		});

		$(document).on('click',".addBlock", function(e){
			e.preventDefault();
			$(this).parents(".form-group").clone().find("input:text").val("").end().appendTo(".logBlock").find("ul.nav").empty();
		});

		$(document).on('click',".removeBlock", function(e){
			e.preventDefault();
			$(this).parents(".form-group").remove();
		});

		$(document).on('click','button#refreshQsos', function(e){
			e.preventDefault();
			var dataToRefresh = $(':checkbox').serialize();
			var baseUrl = "{{ url('refresh') }}";
			$.ajax({
				type: 'post',
				data: dataToRefresh,
				url: baseUrl,
				success: function(data) {
					// alert(data);
					window.location.reload(true);
				}
			},'json');
		});

		$(document).on('click','button#deleteQsos', function(e){
			e.preventDefault();
			var dataToDelete = $(':checkbox').serialize();
			var baseUrl = "{{ url('delete') }}";
			$.ajax({
				type: 'post',
				data: dataToDelete,
				url: baseUrl,
				success: function(data) {
					// alert(data);
					window.location.reload(true);
				}
			},'json');
		});

	    $('#loggerForm').bootstrapValidator({
	        message: "{{ trans('custom.not.valid') }}",
	        feedbackIcons: {
	            valid: 'glyphicon glyphicon-ok',
	            invalid: 'glyphicon glyphicon-remove',
	            validating: 'glyphicon glyphicon-refresh'
	        },
	       	fields: {
	            qFreq: {
	                validators: {
	                    notEmpty: {
	                        message: "{{ trans('custom.field.empty') }}"
	                    },
	                	integer: {
	                		message: "{{ trans('custom.field.number') }}"
	                	}
	                }
	            },
	        }
	    });

	});

	function checkTime(i){
		if (i<10){
			i="0" + i;
		}
		return i;
	}
</script>
@stop

@section('content')
	<div class="container" id="main-container">
	{{ Form::open(array('id'=>'loggerForm')) }}
		<div class="row">
			<div class="col-xs-12">
			@if (!$errors->empty)
				<div class="alert alert-danger">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<strong>ERROR!</strong>
					@foreach ($errors as $error)
						<br>{{ $error }}
					@endforeach
				</div>
			@endif
				<div class="panel panel-default">
					<div class="panel-body logBlock">
						<div class="row form-group">
							<div class="col-sm-12 col-md-3">
								{{ Form::label('qDate', trans('custom.qso.date')) }}
								{{ Form::input('date','qDate',date('Y-m-d'),array('class'=>'form-control uppercase')) }}
							</div>
							<div class="col-sm-12 col-md-3">
								{{ Form::label('qMode', trans('custom.qso.mode')) }}
								{{ Form::select('qMode',$modes,(isset($lastqso->mode->id))?$lastqso->mode->id:'',array('class'=>'form-control uppercase')) }}
							</div>
							<div class="col-sm-12 col-md-3">
								{{ Form::label('qDate', trans('custom.qso.freq')) }}
								{{ Form::text('qFreq',(isset($lastqso->frequency))?$lastqso->frequency:'',array('class'=>'form-control uppercase')) }}
							</div>
							<div class="col-sm-12 col-md-3">
								{{ Form::label('qBand', trans('custom.qso.band')) }}
								{{ Form::select('qBand',$bands,(isset($lastqso->band->id))?$lastqso->band->id:'',array('class'=>'form-control','id'=>'bandSelect')) }}
							</div>
						</div>
						<div class="border-row"></div>
						<div class="row form-group loggerForm">
							<div class="col-sm-12 col-md-3">
								{{ Form::label('qCall', trans('custom.qso.call')) }}
								{{ Form::text('qCall[]', '', array('class'=>'form-control form-call','size'=>'10')) }}
								<ul class="nav nav-pills small">
								</ul>
							</div>
							<div class="col-sm-12 col-md-3">
								<div class="col-sm-12 col-md-5 padding-0">
									{{ Form::label('qRst', trans('custom.qso.rst')) }}
									{{ Form::text('qRst[]','',array('class'=>'form-control uppercase','size'=>'7','ondblclick'=>"this.value='599'",'placeholder'=>'000-599')) }}
								</div>
								<div class="col-sm-12 col-md-7 padding-right-0">
									{{ Form::label('qUtc', trans('custom.qso.utc')) }}
									{{ Form::input('time','qUtc[]','',array('class'=>'form-control uppercase utcTime','size'=>'7','ondblclick'=>"this.value=checkTime((new Date()).getUTCHours())+':'+checkTime((new Date()).getUTCMinutes())+':00'")) }}
								</div>
								<div class="col-xs-12 padding-0">
									{{ Form::label('qNote', trans('custom.qso.note')) }}
									{{ Form::text('qNote[]', '', array('class'=>'form-control')) }}
								</div>
							</div>
							<div class="col-sm-12 col-md-3">
								{{ Form::label('qCall2', trans('custom.qso.call')) }}
								{{ Form::text('qCall2[]', '', array('class'=>'form-control form-call','size'=>'10')) }}
								<ul class="nav nav-pills small">
								</ul>
							</div>
							<div class="col-sm-12 col-md-3">
								<div class="col-sm-10 col-md-5 padding-0">
									{{ Form::label('qRst2', trans('custom.qso.rst')) }}
									{{ Form::text('qRst2[]','',array('class'=>'form-control uppercase','size'=>'7','ondblclick'=>"this.value='599'",'placeholder'=>'000-599')) }}
								</div>
								<div class="col-sm-2 col-md-7 padding-0">
									<div class="pull-right btn-group addRem">
										<span class="btn btn-sm btn-success addBlock"><i class="glyphicon glyphicon-plus addBlockIcon"></i></span>
										<span class="btn btn-sm btn-danger removeBlock"><i class="glyphicon glyphicon-minus removeBlockIcon"></i></span>
									</div>
								</div>
								<div class="col-xs-12 padding-0">
									{{ Form::label('qNote2', trans('custom.qso.note')) }}
									{{ Form::text('qNote2[]', '', array('class'=>'form-control')) }}
								</div>
							</div>
						</div>
					</div>
					
				</div>	
			</div>
			<div class="col-xs-12" id="submit_btn" style="display: none;">
				{{ Form::submit(trans('custom.submit'), array('class'=>'btn btn-lg btn-danger btn-block')) }}
			</div>
		</div>
	{{ Form::close() }}
		@include('common.table')
	@if (isset($qsos) && (!$qsos->isEmpty()))
	@endif
	</div>
@stop