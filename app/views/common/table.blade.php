<div class="marginBottom"></div>
<div class="row">
	<div class="col-xs-12">
		<div class="alert alert-success fade-in" style="display:none;">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<strong>{{ trans('custom.success') }}</strong> {{ trans('custom.qso.saved') }}
		</div>
		<div class="module">
			<div class="module-body">
			@if ((Request::segment(1) != 'logger') && (Request::segment(1) != 'search'))
				@include('tabletop.eqsl')
			@else
				@include('tabletop.index')
			@endif
				<table class="table table-hover table-bordered table-condensed">
					<thead>
						<tr>
							<th></th>
							<th>{{ trans('custom.qso.call') }}</th>
							<th>{{ trans('custom.qso.rst') }}</th>	
							<th>{{ trans('custom.qso.mode') }}</th>	
							<th>{{ trans('custom.qso.date') }}/{{ trans('custom.qso.utc') }}</th>	
							<th>{{ trans('custom.table.qsl') }}</th>	
							<th>{{ trans('custom.qso.freq') }}</th>	
							<th>{{ trans('custom.qso.band') }}</th>	
							<th>{{ trans('custom.table.country') }}</th>	
							<th>{{ trans('custom.table.territory') }}</th>	
							<th></th>	
							<th></th>	
						</tr>
					</thead>
					<tbody>
						@if (isset($qsos))
							@foreach ($qsos as $qso)
								<tr>
									<td>
									@if ($qso->customization()->exists())
										<i onclick="popUp('{{ url('show/map') }}?lat={{ $qso->customization->lat }}&amp;lon={{ $qso->customization->lon }}');" class="glyphicon glyphicon-map-marker mapShower"></i>
									@elseif ($qso->prefixes()->exists())
										<i onclick="popUp('{{ url('show/map') }}?lat={{ $qso->prefixes->last()->lat }}&amp;lon={{ $qso->prefixes->last()->lon }}');" class="glyphicon glyphicon-map-marker mapShower"></i>
									@endif
									</td>
									<td>{{ strtoupper($qso->call) }}</td>
									<td>{{ strtoupper($qso->rst) }}</td>
									<td>{{ isset($qso->mode->name) ? $qso->mode->name : '' }}</td>
									<td>{{ $qso->date }}</td>
									<td class="{{ ($qso->qsl_rcvd == 'Y')?'warning':'info' }}">{{ $qso->qsl_rcvd }}</td>
									<td>{{ $qso->frequency }}</td>
									<td>{{ $bands[$qso->band->id] }}</td>
									<td>{{ is_object($qso->prefixes->first()) ? $qso->prefixes->first()->territory : trans('custom.undefined') }}
									</td>
									<td>{{ is_object($qso->prefixes->last()) ? $qso->prefixes->last()->territory : trans('custom.undefined') }}
									</td>
									<td>
										<i onclick="window.location.href = '{{ url('logger/'.$qso->id.'/edit') }}'" class="glyphicon glyphicon-pencil mapShower"></i>
									</td>
									<td>
										{{ Form::checkbox('qid[]',$qso->id) }}
									</td>
								</tr>
							@endforeach
						@endif
					</tbody>
				</table>
				{{ $qsos->appends(Request::except('page'))->links() }}
			</div>
		</div>	
	</div>
</div>
