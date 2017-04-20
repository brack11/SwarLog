				<div class="form-group col-sm-4">
					{{ Form::button(trans('custom.'.Request::segment(1).'.left'), array('class'=>'btn btn-danger btn-block', 'id'=>'sendEqsl', 'style'=>'display:none;')) }}
				</div>
				<div class="form-group col-sm-4">
					{{ Form::button(trans('custom.'.Request::segment(1).'.center'), array('class'=>'btn btn-warning btn-block', 'id'=>'requestQsl', 'style'=>'display:none;')) }}
				</div>
				<div class="form-group col-sm-4">
					{{ Form::button(trans('custom.'.Request::segment(1).'.right'), array('class'=>'btn btn-info btn-block', 'id'=>'noEqsl', 'style'=>'display:none;')) }}
				</div>
