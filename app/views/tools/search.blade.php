@extends(Config::get('syntara::views.master'))


@section('head')
<script>
	$(document).ready(function(){
		$(':checkbox').on('change',function(){
			$('.module-body button').show();
			if(!$(':checkbox').is(':checked')) {
				$('.module-body button').hide();
			}
		});

		$(document).on('click','button.reloadTbl',function(e){
			window.location.reload(true);
		});

		$("#qCountry").on('change',function(e){
			e.preventDefault();
			var newCountry = $(this).val();
			var baseUrl = "{{ url('territory') }}";
			$.ajax({
				type: 'get',
				url: baseUrl+'/2/'+newCountry,
				success: function(data){
					$('#qRegion').html(data);
				}
			},'json')
		});

		$("input[name='qCq']").on('change',function(e) {
			e.preventDefault();
			var value = $(this).val();
			$("#searchForm").trigger('reset');
			$("input[name='qItu']").val('');
			$("input[name='qCq']").val(value);
		});

		$("input[name='qItu']").on('change',function(e) {
			e.preventDefault();
			var value = $(this).val();
			$("#searchForm").trigger('reset');
			$("input[name='qCq']").val('');
			$("input[name='qItu']").val(value);
		});

	});
</script>

@stop


@section('content')
@if (isset($prefix))
	{{dd($qsos)}}
@endif
	<div class="container" id="main-container">
	{{ Form::open(array('id'=>'searchForm', 'method'=>'get')) }}
		<div class="row">
			<div id="accordion" class="col-xs-12 panel-group">
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
					<div class="panel-heading">
				      	<h4 class="panel-title">
				        	<a data-toggle="collapse" data-parent="#accordion" href="#advancedSearch">Advanced Search</a>
				      	</h4>
				    </div>
					<div id="advancedSearch" class="panel-body logBlock collapse in">
						<div class="row form-group">
							<div class="col-sm-12 col-md-3">
								{{ Form::label('qDate', trans('custom.qso.date')) }}
								{{ Form::text('qDate',(isset($input['qDate'])) ? $input['qDate'] : '',array('class'=>'form-control uppercase')) }}
							</div>
							<div class="col-sm-12 col-md-3">
								{{ Form::label('qMode', trans('custom.qso.mode')) }}
								{{ Form::select('qMode',$modes,(isset($input['qMode'])) ? $input['qMode'] : '',array('class'=>'form-control uppercase')) }}
							</div>
							<div class="col-sm-12 col-md-3">
								{{ Form::label('qFreq', trans('custom.qso.freq')) }}
								{{ Form::text('qFreq',(isset($input['qFreq'])) ? $input['qFreq'] : '',array('class'=>'form-control uppercase')) }}
							</div>
							<div class="col-sm-12 col-md-3">
								{{ Form::label('qBand', trans('custom.qso.band')) }}
								{{ Form::select('qBand',$bands,(isset($input['qBand'])) ? $input['qBand'] : '',array('class'=>'form-control','id'=>'bandSelect')) }}
							</div>
						</div>
						<div class="border-row"></div>
						<div class="row form-group loggerForm">
							
							<!-- column 1 --!>
							<div class="col-sm-12 col-md-3">
								{{ Form::label('qCall', trans('custom.qso.call')) }}
								{{ Form::text('qCall', (isset($input['qCall'])) ? $input['qCall'] : '', array('class'=>'form-control form-call','size'=>'10')) }}
								<ul class="nav nav-pills small">
								</ul>
							</div>

							<!-- column 2 --!>
							<div class="col-sm-12 col-md-3">
								<div class="col-xs-12 padding-0">
									{{ Form::label('qRst', trans('custom.qso.rst')) }}
									{{ Form::text('qRst',(isset($input['qRst'])) ? $input['qRst'] : '',array('class'=>'form-control uppercase','size'=>'7','ondblclick'=>"this.value='599'",'placeholder'=>'000-599')) }}
								</div>
								<div class="col-xs-12 padding-0">
									{{ Form::label('qNote', trans('custom.qso.note')) }}
									{{ Form::text('qNote', (isset($input['qNote'])) ? $input['qNote'] : '', array('class'=>'form-control')) }}
								</div>
							</div>

							<!-- column 3 --!>
							<div class="col-sm-12 col-md-6">
								<div class="col-xs-12 padding-0">
									{{ Form::label('qCountry', trans('custom.table.country')) }}
									<?php $countryId = isset($input['qCountry']) ? $input['qCountry'] : ''; ?> 
									{{ Form::select('qCountry', PrefixHelper::getTerritory(1,$countryId), $countryId, array('class'=>'form-control')) }}
								</div>
								<div class="col-xs-12 padding-0">
									{{ Form::label('qRegion', trans('custom.table.territory')) }}
									<?php $regionId = isset($input['qRegion']) ? $input['qRegion'] : ''; ?> 
									{{ Form::select('qRegion', PrefixHelper::getTerritory(2,$countryId), $regionId, array('class'=>'form-control')) }}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
				      	<h4 class="panel-title">
				        	<a data-toggle="collapse" data-parent="#accordion" href="#zones">Zones Search</a>
				      	</h4>
				    </div>
					<div id="zones" class="panel-body logBlock collapse">
						<!-- column 1 --!>
						<div class="col-xs-12 col-sm-6">
							{{ Form::label('qCq', trans('custom.cq')) }}
							{{ Form::text('qCq',(isset($input['qCq'])) ? $input['qCq'] : '',array('class'=>'form-control uppercase','size'=>'7')) }}
						</div>
						<div class="col-xs-12 col-sm-6">
							{{ Form::label('qItu', trans('custom.itu')) }}
							{{ Form::text('qItu', (isset($input['qItu'])) ? $input['qItu'] : '', array('class'=>'form-control')) }}
						</div>

					</div>
					
				</div>	
			</div>
			<div class="col-xs-12" id="submit_btn">
				{{ Form::submit(trans('custom.submit'), array('class'=>'btn btn-lg btn-danger btn-block')) }}
			</div>
		</div>
	{{ Form::close() }}

	@if (isset($qsos))
		@include('common.table')
	@endif

@stop