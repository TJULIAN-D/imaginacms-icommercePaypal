@php
    $options = array('required' =>'required');
    $formID = uniqid("form_id");
@endphp

{!! Form::open(['route' => ['admin.icommerce.paymentmethod.update',$method->id], 'method' => 'put','name' => $formID]) !!}

<div class="col-xs-12 col-sm-9">

    <div class="row">

        <div class="nav-tabs-custom">
            @include('partials.form-tab-headers')
            <div class="tab-content">
                <?php $i = 0; ?>
                @foreach (LaravelLocalization::getSupportedLocales() as $locale => $language)
                    <?php $i++; ?>
                    <div class="tab-pane {{ locale() == $locale ? 'active' : '' }}" id="{{$method->name}}_tab_{{ $i }}">
                        
                        {!! Form::i18nInput('title', trans('icommerce::paymentmethods.table.title'), $errors, $locale, $method) !!}
                        {!! Form::i18nInput('description', trans('icommerce::paymentmethods.table.description'), $errors, $locale, $method) !!}
                    
                    </div>
                @endforeach
            </div>
        </div>
        
    </div>

    <div class="row">
    <div class="col-xs-12">
        
        <div class="form-group ">
            <label for="clientid">{{trans('icommercepaypal::icommercepaypals.table.clientid')}}</label>
            <input placeholder="{{trans('icommercepaypal::icommercepaypals.table.clientid')}}" required="required" name="clientid" type="text" id="clientid" class="form-control" value="{{$method->options->clientid}}">
        </div>

        <div class="form-group ">
            <label for="clientsecret">{{trans('icommercepaypal::icommercepaypals.table.clientsecret')}}</label>
            <input placeholder="{{trans('icommercepaypal::icommercepaypals.table.clientsecret')}}" required="required" name="clientsecret" type="text" id="clientsecret" class="form-control" value="{{$method->options->clientsecret}}">
        </div>

        <div class="form-group">
            <label for="mode">{{trans('icommercepaypal::icommercepaypals.table.mode')}}</label>
            <select class="form-control" id="mode" name="mode" required>
                    <option value="sandbox" @if(!empty($method->options->mode) && $method->options->mode=='sandbox') selected @endif>SANDBOX</option>
                    <option value="live" @if(!empty($method->options->mode) && $method->options->mode=='live') selected @endif>LIVE</option>
            </select>
        </div>

        <div class="form-group">
            <div>
                <label class="checkbox-inline">
                    <input name="status" type="checkbox" @if($method->status==1) checked @endif>{{trans('icommerce::paymentmethods.table.activate')}}
                </label>
            </div>   
        </div>

    </div>
    </div>

</div>

<div class="col-sm-3">
    
    @include('icommercepaypal::admin.icommercepaypals.partials.featured-img',['crop' => 0,'name' => 'mainimage','action' => 'create'])
    
</div>
    
    
 <div class="clearfix"></div>   

    <div class="box-footer">
    <button type="submit" class="btn btn-primary btn-flat">{{ trans('icommerce::paymentmethods.button.save configuration') }} {{$method->title}}</button>
    </div>



{!! Form::close() !!}