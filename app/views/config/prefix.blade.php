@extends(Config::get('syntara::views.master'))

@section('head')
<script>
	$(document).ready(function(){
		$(document).on('click','span.fileDelete',function(e){
			e.preventDefault();
			var baseUrl = "{{ url('delete') }}";
			var fileName = $(this).data('file');
			$(this).parents('tr').addClass('current');
			$.get(baseUrl+'/'+fileName,function(data){
				$('tr.current').remove();
			});
		});

		$(document).on('click','span.fileProcess',function(e){
			e.preventDefault();
			var baseUrl = "{{ url('process') }}";
			var fileName = $(this).data('file');
			$.get(baseUrl+'/'+fileName,function(data){
				$(".alert-success").show();
			});
		});
	});
</script>
@stop

@section('content')
	<div class="container" id="main-container">
		<div class="col-xs-12">
			<div class="alert alert-success fade-in" style="display:none;">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<strong>{{ trans('custom.success') }}</strong> {{ trans('custom.conf.saved') }}
			</div>
		</div>
		<div class=" col-sm-4 col-sm-offset-4">
			
			{{ Form::open(array('id'=>'prefixRenewer','files'=>true,'url'=>url('config/upload'))) }}
			<div class="form-group input-group">
				<span class="input-group-btn">
					<button class="form-control btn btn-primary btn-file">{{ trans('custom.browse') }}<input name="uplPfx" type="file"></button>
				</span>
				{{ Form::text('uplText', '', array('class'=>'form-control','readonly')) }}
				<span class="input-group-btn">
					<button type="submit" id="uploadPfx" class="form-control btn btn-info">{{ trans('custom.submit') }} </button>
				</span>
			</div>
			{{ Form::close() }}
		</div>
		<div class="col-sm-4 col-sm-offset-4">
			{{ FileHelper::listFiles(public_path().'/uploads/pfx') }}
		</div>
	</div>
@stop