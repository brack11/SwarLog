@extends(Config::get('syntara::views.master'))

@section('head')
<script>
	$(document).ready(function(){
		$(document).on('click','span.fileDelete',function(e){
			e.preventDefault();
			var baseUrl = "{{ url('backup') }}";
			var fileName = $(this).data('file');
			var token = $("input[name='_token']").val();
			$(this).parents('tr').addClass('current');
			$.ajax({
				type: 'GET',
				url: baseUrl+'/'+fileName+'/delete',
				data: '_token='+token,
				success: function(data) {
					$('tr.current').remove();
				}
			},'json');
		});

		$('td input').tooltip();

		$(document).on('click','span.fileProcess',function(e){
			e.preventDefault();
			var baseUrl = "{{ url('backup') }}";
			var fileName = $(this).data('file');
			var token = $("input[name='_token']").val();
			var truncate = $("input[name='truncate']").val();
			var clear = $("input[name='clear']").val();
			$.ajax({
				type: 'GET',
				url: baseUrl+'/'+fileName+'/process',
				data: '_token='+token+'&truncate='+truncate+'&clear='+clear,
				success: function(data) {
					$(".alert-success").show();
				}
			},'json');
		});

		$(document).on('change', '.btn-file :file', function(e) {
			e.preventDefault();
			var input = $(this),
			numFiles = input.get(0).files ? input.get(0).files.length : 1,
			label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
			input.trigger('fileselect', [numFiles, label]);
		});

		$(document).on('click','button#makeBackup', function(e){
			e.preventDefault();
			var baseUrl = "{{ url('backup/backup') }}";
			$.ajax({
				type: 'post',
				url: baseUrl,
				success: function(data) {
					window.location.reload(true);
				}
			},'json');
		});

		$('.btn-file :file').on('fileselect', function(event, numFiles, label) {

			var input = $(this).parents('.input-group').find(':text'),
				log = numFiles > 1 ? numFiles + ' files selected' : label;
			if( input.length == 1 ) {
			    input.val(log);
			} else {
			    if( log ) alert(log);
			}
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
			
			{{ Form::open(array('id'=>'importAdif','files'=>true,'url'=>url('backup'))) }}
			<div class="form-group input-group" style="width:100%;height: 36px">
				<span class="input-group-btn" style="width:auto">
					<span class="form-control btn btn-primary btn-file">{{ trans('custom.browse') }}<input name="uplPfx" type="file"></span>
				</span>
				{{ Form::text('uplText', '', array('class'=>'form-control','readonly')) }}
				<span class="input-group-btn" style="width:auto">
					<button type="submit" id="uploadPfx" class="form-control btn btn-info">{{ trans('custom.submit') }}</button>
				</span>
			</div>
			{{ Form::close() }}
			<div class="border-row"></div>
			<div class="form-group">
				{{ Form::button(trans('custom.generate.backup'), array('class'=>'btn btn-block btn-primary','id'=>'makeBackup')) }}
			</div>
			{{ FileHelper::listFiles(public_path().'/uploads/backups/'.$currentUser->username) }}
			<!-- <div class="col-sm-12">
				<label class="col-sm-9">{{ trans('custom.clear.data') }}</label>
				<input type="checkbox" name="truncate" value="1" class="col-sm-3">
			</div> -->
		</div>
	</div>
@stop