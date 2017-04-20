@extends(Config::get('syntara::views.master'))


@section('head')
<script>
	$(document).ready(function(){
		$(':checkbox').on('change',function(){
			$('.module-body button').show();
			$('div#mainButtons').hide();
			if(!$(':checkbox').is(':checked')) {
				$('.module-body button').hide();
				$('div#mainButtons').show();
			}
		});

		$(document).on('click','button.reloadTbl',function(e){
			window.location.reload(true);
		});

		$(document).on('click','button#allEqslReceive', function(e){
			e.preventDefault();
			var baseUrl = "{{ url('request') }}";
			$.ajax({
				type: 'post',
				data: 'action=rcv',
				url: baseUrl,
				success: function(data) {
					$('.alert-space2').html("<div class='alert alert-success fade in'><h4>{{ trans('custom.success') }}</h4></div>");
					$('.alert-space2>.alert').append("{{ trans('custom.eqsl.received') }}:<br>");
					$.each(data, function(index, element) {
			            $('.alert-space2>.alert').append(element.call+', ');
			        });
					$('.alert-space2>.alert').append("<br><button class='btn reloadTbl btn-danger'>{{ trans('custom.table.reload') }}</button>");
				}
			},'json');
		});

		$(document).on('click','button#allEqslSend', function(e){
			e.preventDefault();
			var baseUrl = "{{ url('request') }}";
			$.ajax({
				type: 'post',
				data: 'action=e',
				url: baseUrl,
				success: function(data) {
					$('.alert-space1').html("<div class='alert alert-success fade in'><h4>{{ trans('custom.success') }}</h4><button class='btn reloadTbl btn-danger'>{{ trans('custom.table.reload') }}</button></div>");
				}
			},'json');
		});

		$(document).on('click','button#sendEqsl', function(e){
			e.preventDefault();
			var dataToRefresh = $(':checkbox').serialize();
			var baseUrl = "{{ url('request') }}";
			$.ajax({
				type: 'post',
				data: dataToRefresh+'&action=e',
				url: baseUrl,
				success: function(data) {
					$('.alert-space1').html("<div class='alert alert-success fade in'><h4>{{ trans('custom.success') }}</h4><button class='btn reloadTbl btn-danger'>{{ trans('custom.table.reload') }}</button></div>");
				}
			},'json');
		});

		$(document).on('click','button#requestQsl', function(e){
			e.preventDefault();
			var dataToRefresh = $(':checkbox').serialize();
			var baseUrl = "{{ url('request') }}";
			$.ajax({
				type: 'post',
				data: dataToRefresh+'&action=r',
				url: baseUrl,
				success: function(data) {
					window.location.reload(true);
				}
			},'json');
		});

		$(document).on('click','button#noEqsl', function(e){
			e.preventDefault();
			var dataToRefresh = $(':checkbox').serialize();
			var baseUrl = "{{ url('request') }}";
			$.ajax({
				type: 'post',
				data: dataToRefresh+'&action=i',
				url: baseUrl,
				success: function(data) {
					window.location.reload(true);
				}
			},'json');
		});
	});
</script>
@stop


@section('content')
<!-- <pre>{{ print_r($new_qsos) }}</pre> -->
@if (isset($confs))
<div id="mainButtons">
	<div class="col-sm-8 col-sm-offset-2">
		<div class="form-group col-sm-12"><button id="allEqslSend" class="btn btn-block btn-danger">{{ trans('custom.tools.left') }} </button></div>
		<div class="form-group alert-space1 col-sm-12"></div>
		<div class="form-group col-sm-12"><button id="allEqslReceive" class="btn btn-block btn-warning">{{ trans('custom.eqsl.rcvd') }} </button></div>
		<div class="form-group alert-space2 col-sm-12"></div>
	</div>
</div>
	@if (isset($qsos))
		@include('common.table')
	@endif
@endif

@stop