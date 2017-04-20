				<div class="form-group col-sm-4">
					{{ Form::button(trans('custom.'.Request::segment(1).'.left'), array('class'=>'btn btn-danger btn-block', 'id'=>'deleteQsos', 'style'=>'display:none;')) }}
				</div>
				<div class="form-group col-sm-4 col-sm-offset-4">
					{{ Form::button(trans('custom.'.Request::segment(1).'.right'), array('class'=>'btn btn-info btn-block', 'id'=>'refreshQsos', 'style'=>'display:none;')) }}
				</div>
