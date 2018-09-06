@php
	$configuration = icommercepaypal_get_configuration();
	$options = array('required' =>'required');

	if($configuration==NULL){
		$cStatus = 0;
		$entity = icommercepaypal_get_entity();
	}else{
		$cStatus = $configuration->status;
		$entity = $configuration;
	}

	$status = icommerce_get_status();
	$formID = uniqid("form_id");

@endphp

	{!! Form::open(['route' => ['admin.icommercepaypal.paypalconfig.update'], 'method' => 'put','name' => $formID]) !!}

<div class="col-xs-12 col-sm-9">

	@include('icommerce::admin.products.partials.flag-icon',['entity' => $entity,'att' => 'description'])

	{!! Form::normalInput('description', trans('icommercepaypal::paypalconfigs.table.description'), $errors,$configuration,$options) !!}

	{!! Form::normalInput('currency', trans('icommercepaypal::paypalconfigs.table.currency'), $errors,$configuration,$options) !!}

	{!! Form::normalInput('clientid', trans('icommercepaypal::paypalconfigs.table.clientid'), $errors,$configuration,$options) !!}

	{!! Form::normalInput('clientsecret', trans('icommercepaypal::paypalconfigs.table.clientsecret'), $errors,$configuration,$options) !!}

	{!! Form::normalInput('endpoint', trans('icommercepaypal::paypalconfigs.table.endpoint'), $errors,$configuration,$options) !!}

	{!! Form::normalInput('mode', trans('icommercepaypal::paypalconfigs.table.mode'), $errors,$configuration,$options) !!}

	<div class="form-group">
		<div>
			<label class="checkbox-inline">
				<input name="status" type="checkbox" @if($cStatus==1) checked @endif>{{trans('icommercepaypal::paypalconfigs.table.activate')}}
			</label>
		</div>
	</div>

</div>

<div class="col-sm-3">

	@include('icommercepaypal::admin.paypalconfigs.partials.featured-img',['crop' => 0,'name' => 'mainimage','action' => 'create'])

</div>


<div class="clearfix"></div>

<div class="box-footer">
	<button type="submit" class="btn btn-primary btn-flat">{{ trans('icommercepaypal::paypalconfigs.button.save configuration') }}</button>
</div>



{!! Form::close() !!}