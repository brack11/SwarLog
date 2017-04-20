@extends(Config::get('syntara::views.master'))

@section('head')
<script>
	$(document).ready(function(){
		$('#gridCalc').on('click',function(e){
			e.preventDefault();
			var lat = $("input[name='eLat']").val();
			var lon = $("input[name='eLon']").val();
			var base_url = "{{ url('get/grid') }}";
			$.ajax({
				type: 'get',
				url: base_url+'/'+lat+'/'+lon,
				success: function(data){
					$("input[name='eGrid']").val(data);
				}
			},'json');
		});

		$("#eCountry").on('change',function(e){
			e.preventDefault();
			var newCountry = $(this).val();
			var baseUrl = "{{ url('territory') }}";
			$.ajax({
				type: 'get',
				url: baseUrl+'/2/'+newCountry,
				success: function(data){
					$('#eRegion').html(data);
				}
			},'json')
		});

		$("#eRegion").on('change',function(e){
			e.preventDefault();
			var newRegion = $(this).val();
			var baseUrl = "{{ url('territory') }}";
			$.ajax({
				type: 'get',
				url: baseUrl+'/3/'+newRegion,
				success: function(data){
					var zone = $.parseJSON(data);
					$("input[name='eCq']").val(zone.cq);
					$("input[name='eItu']").val(zone.itu);
				}
			},'json')
		});
	});
</script>
@stop

@section('content')
	<div class="container" id="main-container">
		{{ Form::open(array('id'=>'editForm','class'=>'form-horizontal','role'=>'form','url'=>'logger/'.$qso->id,'method'=>'put')) }}
			@if (count($errors))
				<div class="alert alert-danger">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<strong>ERROR!</strong> 
					@foreach ($errors->all() as $error)
						{{ e($error) }}
					@endforeach
				</div>
			@endif
			<div class="panel panel-default">
				<div class="panel-body">
					<div class="form-group">
						<div class="col-sm-12 inline">
							<div class="col-sm-12 col-md-2">
								{{ Form::label('eDate', trans('custom.qso.date')) }}
								{{ Form::text('eDate',date('Y-m-d H:i:s',strtotime($qso->date)),array('class'=>'form-control')) }}
							</div>
							<div class="col-sm-12 col-md-1">
								{{ Form::label('eMode', trans('custom.qso.mode')) }}
								{{ Form::select('eMode',$modes,$qso->mode->id,array('class'=>'form-control')) }}
							</div>
							<div class="col-sm-12 col-md-2">
								{{ Form::label('eFreq', trans('custom.qso.freq')) }}
								{{ Form::text('eFreq',$qso->frequency,array('class'=>'form-control')) }}
							</div>
							<div class="col-sm-12 col-md-2">
								{{ Form::label('eBand', trans('custom.qso.band')) }}
								{{ Form::select('eBand',$bands,$qso->band->id,array('class'=>'form-control','id'=>'bandSelect')) }}
							</div>
							<div class="col-sm-12 col-md-1">
								{{ Form::label('eRst', trans('custom.qso.rst')) }}
								{{ Form::text('eRst',$qso->rst,array('class'=>'form-control')) }}
							</div>
							<div class="col-sm-12 col-md-2">
								{{ Form::label('eQvia', trans('custom.qsl.via')) }}
								{{ Form::select('eQvia',array(
									'B'=>trans('custom.bureau'),
									'D'=>trans('custom.direct'),
									'E'=>trans('custom.eqsl.cc'),
									'M'=>trans('custom.manager')),
									$qso->qsl_via, array('class'=>'form-control')) }}
							</div>
							<div class="col-sm-12 col-md-2">
								{{ Form::label('eQrcvd', trans('custom.qsl.rcvd')) }}
								{{ Form::select('eQrcvd',array(
									'Y'=>trans('custom.qsl.yes'),
									'N'=>trans('custom.qsl.no'),
									'R'=>trans('custom.qsl.requested'),
									'I'=>trans('custom.qsl.ignore')),
									$qso->qsl_rcvd, array('class'=>'form-control')) }}
							</div>
						</div>
					</div>
					<div class="border-row"></div>
					<div class="form-group">
						<div class="col-sm-12">
							<div class="col-sm-5">
								{{ Form::label('eLat', trans('custom.lat')) }}
								{{ Form::text('eLat', (isset($qso->customization->lat))?$qso->customization->lat:$qso->prefixes->last()->lat, array('class'=>'form-control')) }}
							</div>
							<div class="col-sm-5">
								{{ Form::label('eLon', trans('custom.lon')) }}
								{{ Form::text('eLon', (isset($qso->customization->lon))?$qso->customization->lon:$qso->prefixes->last()->lon, array('class'=>'form-control')) }}
							</div>
							<div class="col-sm-2">
								{{ Form::label('eGrid', trans('custom.grid')) }}
								<div class="input-group">
									{{ Form::text('eGrid', (isset($qso->customization->grid))?$qso->customization->grid:'', array('class'=>'form-control')) }}
									<span class="input-group-btn">
										<button class="btn btn-danger form-control" id="gridCalc" type="button">{{ trans('custom.calculate') }}</button>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<div class="col-sm-4">
								{{ Form::label('eCountry', trans('custom.table.country')) }}
								{{ Form::select('eCountry', PrefixHelper::getTerritory(1,$qso->prefixes->first()->id), $qso->prefixes->first()->id, array('class'=>'form-control')) }}
							</div>							
							<div class="col-sm-5">
								{{ Form::label('eRegion', trans('custom.table.territory')) }}
								{{ Form::select('eRegion', PrefixHelper::getTerritory(2,$qso->prefixes->first()->id), $qso->prefixes->last()->id, array('class'=>'form-control')) }}
							</div>							
							<div class="col-sm-1">
								{{ Form::label('eCq', trans('custom.cq') )}}
								{{ Form::text('eCq', (isset($qso->customization->cq))?$qso->customization->cq:$qso->prefixes->last()->cq, array('class'=>'form-control')) }}
							</div>
							<div class="col-sm-1">
								{{ Form::label('eItu', trans('custom.itu') )}}
								{{ Form::text('eItu', (isset($qso->customization->itu))?$qso->customization->itu:$qso->prefixes->last()->itu, array('class'=>'form-control')) }}
							</div>
							<div class="col-sm-1">
								{{ Form::label('eIota', trans('custom.iota') )}}
								{{ Form::text('eIota', (isset($qso->customization->itu))?$qso->customization->iota:$qso->prefixes->last()->iota, array('class'=>'form-control')) }}
							</div>
						</div>
					</div>
					<div class="border-row"></div>
					<div class="col-sm-12">
						<div class="col-sm-6">
							<div class="form-group">
								{{ Form::label('eCall', trans('custom.qso.call'), array('class'=>'col-sm-3 control-label')) }}	
								<div class="col-sm-9">
									{{ Form::text('eCall', $qso->call, array('class'=>'form-control uppercase')) }}							
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('eAddress', trans('custom.address'), array('class'=>'col-sm-3 control-label')) }}	
								<div class="col-sm-9">
									{{ Form::textarea('eAddress', $qso->address, array('class'=>'form-control','rows'=>'3')) }}							
								</div>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								{{ Form::label('eMsg', trans('custom.qsl.message'), array('class'=>'col-sm-3 control-label')) }}	
								<div class="col-sm-9">
									{{ Form::textarea('eMsg', $qso->message, array('class'=>'form-control','rows'=>'3')) }}							
								</div>
							</div>
							<div class="form-group">
								{{ Form::label('eNote', trans('custom.qso.note'), array('class'=>'col-sm-3 control-label')) }}	
								<div class="col-sm-9">
									{{ Form::text('eNote', $qso->comment, array('class'=>'form-control')) }}							
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-12">
					<a href="javascript:history.back();" class="btn btn-lg btn-info pull-right">{{ trans('custom.back') }} </a>	
					{{ Form::submit(trans('custom.save'), array('class'=>'btn btn-lg btn-danger')) }}
				</div>
			</div>
		{{ Form::close() }}
	</div>
@stop