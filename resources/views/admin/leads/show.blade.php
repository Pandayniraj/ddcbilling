@extends('layouts.master')

@section('head_extra')
@php 
$user = isset($user) ? $user : null;
$mobile_no = isset($mobile_no) ? $mobile_no : null;
$courses = isset($courses) ? $courses : null;
@endphp
<!-- Select2 css -->
@include('partials._head_extra_select2_css')
<link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet" />
<script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>
<link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet" type="text/css" />
<link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet" type="text/css" />
<script src="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
<script src="{{ asset ("/bower_components/admin-lte/plugins/daterangepicker/moment.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}" type="text/javascript"></script>
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> -->
 <!--  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> -->
  <script>
    $(function() {
        $('#task_due_date').datetimepicker({
            //inline: true,
            //format: 'YYYY-MM-DD HH:mm',
            format: 'DD-MM-YYYY HH:mm'
            , sideBySide: true
        });
    });

    $(document).on('click', '#note_to_task', function() {
        if ($("#note_to_task").is(':checked'))
            $("#task_dates").show();
        else
            $("#task_dates").hide();
    });

</script>
@endsection

@section('content')

<style type="text/css">
    [data-letters]:before {
        content: attr(data-letters);
        display: inline-block;
        font-size: 1em;
        width: 2.5em;
        height: 2.5em;
        line-height: 2.5em;
        text-align: center;
        border-radius: 50%;
        background: red;
        vertical-align: middle;
        margin-right: 0.3em;
        color: white;
    }
    body{
        font-family: Montserrat,Helvetica,Arial,serif !important;
    }
    .has-error {
        border-color: #f14668
    }
    .panel-default>.panel-heading {
     box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
     padding: 5px 0px 10px 0px;
 }
 .panel.panel-default {
    box-shadow: rgb(100 100 111 / 20%) 0px 7px 29px 0px;
}
.panel-heading {
    padding: 10px 15px;
    border-bottom:none !important;
}
.content {
    min-height: 0 !important;
}
img.p-image {
    width: 90%;
    border-radius: 50%;
}
.p-profile h3 {
    text-transform: capitalize;
    font-weight: 600;
}
.p-profile {
    margin-top: -16px;
    margin-left: -22px;
}
dd, dt {
    line-height: 2.428571;
}
.dl-horizontal dt {
    text-align: start !important;
}
.editable-click, a.editable-click, a.editable-click:hover {
    text-decoration: none;
    border-bottom: none !important;
}
.nav-tabs-custom {
    box-shadow: none !important;
}
.box {
   border-top:none !important;
}
.bg-info {
    background-color: transparent !important;
}
.panel-body.pdbody {
    padding: 0px !important;
}
.bx-body{
    padding: 0px !important;
}
.bg-success {
    background-color: transparent;
}
.btn-warning {
    background-color: #f5f5f5;
    border-color: #ddd !important;
    color: #000c !important;
    margin-left: !important;
    font-weight: 400;
    padding: 7px !important;
}
tr.bg-info td {
    font-size: 14px;
}
.nav-tabs-custom>.nav-tabs>li.active {
    border-top-color: transparent;
}
.nav-tabs-custom>.nav-tabs>li {
    border-top: none;
}
.nav-tabs-custom>.nav-tabs>li.active>a, .nav-tabs-custom>.nav-tabs>li.active:hover>a {
    background-color: #3c8dbc;
    color: #fff;
}
.nav-tabs-custom>.nav-tabs>li:first-of-type.active>a {
    border-left-color: transparent;
    border-right-color: transparent;
}
.bg-orange {
    background-color:transparent !important;
    border: 1px solid #ddd;
    color: #000 !important;
    padding: 7px;
}
.mar22 {
    padding: 3px;
}
.btn-success1 {
    background-color: transparent;
    border-color: 1px solid #ddd !important;
    border: 1px solid #ddd !important;
    color: #000;
    padding: 7px;
}
.btn-danger1 {
    background-color: transparent;
    border: 1px solid #ddd;
    color: #000;
    padding: 7px;
}
.btn-warning1 {
    background-color: transparent;
    border: 1px solid #ddd;
    color: #000;
    padding: 7px;
}
.btn-warning {
    padding: 5px;
}
.btn-default {
    background-color: #f4f4f4;
    color: #444;
    border-color: #ddd;
    padding: 7px;
}
.so-icon {
    border: 1px solid #000;
    padding: 10px;
    border-radius: 50%;
    width: 27px;
    height: 27px;
}
.social-icon li {
    text-decoration: none !important;
    float: right;
    display: flex;
    margin-left: 4px;
}
ul.social-icon {
    position: relative;
    bottom: 35px;
    left: 96px;
}
li.so-icon.face {
    background-color: #4867aa;
    border: 1px solid #4867aa;
    color: #fff;
}
li.so-icon.twit {
    background-color: #1da1f2;
    border: 1px solid #1da1f2;
    color: #fff;
}
li.so-icon.linkin {
    background-color: #0077b5;
    border: solid 1px #0077b5;
    color: #fff;
}
i.fa.fa-twitter {
    margin-top: -2px;
    margin-left: -3px;
    font-size: 13px;
}
i.fa.fa-linkedin {
    margin-top: -4px;
    margin-left: -3px;
}
i.fa.fa-facebook {
    margin-top: -2px;
    margin-left: -1px;
}
.bg-danger li.active:after {
    left: 98%;
    top: 50%;
    border: solid transparent;
    content: " ";
    height: 0;
    width: 0;
    position: absolute;
    pointer-events: none;
    border-left-color: #3c8dbc;
    border-width: 22px;
    margin-top: -23px;
    transition: all .15s ease-in-out;
}

.tabpanel-section {
  margin: 0;
  padding: 0; 
  list-style: none; 
  overflow: hidden;
  
}

.tabpanel-section li {
  float: left;
  
}

.tabpanel-section li a{
  display: block; 
  background-color: #3c8dbc; 
  padding: 10px 20px 10px 40px;
  position: relative;
  
  box-sizing: border-box
}

.nav-section a:after {
  content: '';
  position: absolute;
  top: 0;
  right: -29px;
  height: 0;
  width: 0;
  border-top: 50px solid transparent;
  border-bottom: 50px solid transparent;
  border-left: 30px solid #3c8dbc;
  z-index: 2;
}

.nav-section a:before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  height: 0;
  width: 0;
  border-top: 50px solid transparent;
  border-bottom: 50px solid transparent;
  border-left: 30px solid #fff;
}

.nav-section:first-child a {
  padding-left: 39px;
}

.nav-section:first-child a:before {
  border: none;
}

.nav-section a:before, .nav-section a:after {
  top: 50%;
  margin-top: -50px;
}

.nav-section a:hover {
  background-color: #f39c12;
  color: #000;
}

.nav-section a:hover:after {
  border-left-color: #f39c12;
}
.active a:after {
    border-left-color: #f39c12 !important;
}
li.nav-item.nav-section a {
    color: #fff;
}
.tabpanel-section>li.active>a, .tabpanel-section>li.active>a:focus, .tabpanel-section>li.active>a:hover {
    color: #fff;
    cursor: default;
    background-color: #f39c12;
    border: 1px solid #ddd;
    border-bottom-color: transparent;
}
.nav>li>a:hover, .nav>li>a:active, .nav>li>a:focus {
    color: #fff;
    background: #f39c12;
}
.open>.dropdown-menu {
    display: none;
}
.dropdown {
  position: relative;
  display: inline-block;
}
.nav-tabs {
    border-bottom: none !important;
}

.tabpanel-section>li>a {
    position: relative;
    display: block;
    padding: 10px 35px;
}
.box {
    box-shadow: none !important;
}
i.fa.fa-edit.mar22 {
    padding: 6px;
}
@media (min-width: 320px) and (max-width: 480px) {
    .p-profile {
        margin-top: -8px;
        margin-left: 0px;
    }
    img.p-image {
        width: 40%;
        border-radius: 50%;
    }
    ul.social-icon {
        position: relative;
        bottom: 30px;
        left: -3px;
    }
    .p-profile h3 {
        text-transform: capitalize;
        font-weight: 600;
        font-size: 18px;
    }
}
@media (min-width: 481px) and (max-width: 768px) {
    img.p-image {
        width: 28%;
        border-radius: 50%;
    }
    .p-profile {
        margin-top: -16px;
        display: inline-table;
        margin-left: 0px;
    }
    .tabpanel-section li a {
        padding: 0px;

    }
}
@media (min-width: 769px) and (max-width: 1200px) {
    img.p-image {
    width: 30%;
    border-radius: 50%;
}
.p-profile {
    margin-top: -16px;
    margin-left: 3px;
    display: inline-block;
}
  .tabpanel-section li a {
        padding: 0px;

    }

}
a.btn.btn-primary.btn-xs {
    padding: 7px;
}
button.btn.btn-warning.btn-xs.btn-mail {
    padding: 7px;
}
a.nav-link.active {
    background-color: #f29c33;
}
 .nav>li>a:active:after {
    color: #fff;
    background: #f39c12;
}
a.nav-link.active:after {
    border-left-color: #f29c33;
}
ul.nav.nav-tabs.bg-danger li {
    padding-left: 19px;
}

i.fa.fa-globe {
    margin-top: -4px;
    margin-left: -3px;
}
img.p-image {
    width: 115px;
  height: 110px;
  object-fit: cover;
  object-position: bottom;
  border-radius: 50%;
}
input#imgInp {
    margin-top: 15px;
}
.nav-tabs-custom>.nav-tabs>li:first-of-type {
    margin-left: -18px;
}
</style>

<input type="hidden" id='lead_id' value="{{ \Request::segment(3) }}">
<div class="row">
    <div class="col-md-9">
        <div class="panel panel-default">
          <div class="panel-heading">
              <section class="content-header">
                <div class="row">
                    <div class="col-md-2">
                       <h3 style="white-space: nowrap; text-transform: capitalize; margin-top: 8px; font-size:15px;
    font-weight: 600;">
                        {{$lead->name}}
                        <small>{{ PayrollHelper::getDepartment($user->departments_id??'') }}
                            {{ PayrollHelper::getDesignation($user->designations_id??'') }}</small>
                        </h3>
                    </div>
                    <div class="col-md-10">
                       <span class="pull-right">
                         <small id="ajax_status"></small>
                         

                        <button class="btn btn-warning btn-xs" href="#" onClick="openmodal()" title="{{ trans('admin/leads/general.button.create') }}">
                             <i class="fa fa-plus " aria-hidden="true"></i> New
                        </button>
                       

                         <button class="btn bg-orange btn-xs" href="#" data-target="#sendSMS" data-toggle="modal">
                            <i class="fa fa-comment " aria-hidden="true"></i>SMS
                        </button>

                        @if($lead->stage_id == 3 || $lead->stage_id == 4)
                        <a class="btn btn-default btn-xs quotations-proposal" href="/admin/orders/create?type=quotation"> <i class="fa fa-book"></i> Quote</a>

                        @endif
                        <button href="/admin/mail/{!! $lead->id !!}/show-offerlettermodal" class="btn btn-success1 btn-xs" data-target="#modal_dialog" data-toggle="modal" title="Offer Letter"> <i class="fa fa-envelope-o mar22" aria-hidden="true"></i>Mail</button>

                        <button href="/admin/mail/{!! $lead->id !!}/show-unsuccessfulapplicationmodal" class="btn btn-danger1 btn-xs" data-toggle="modal" data-target="#modal_dialog" title="Reminder"><i class="fa fa-bell mar22" aria-hidden="true"></i>Reminder Mail</button>

                        <button href="/admin/mail/{!! $lead->id !!}/show-pendingmodal" class="btn btn-warning btn-xs btn-mail" data-toggle="modal" data-target="#modal_dialog" title="Pending"><i class="fa fa-envelope-o mar22" aria-hidden="true"></i>Thank You Mail</button>
                        <a class="btn btn-default btn-xs" href="/admin/transfer_lead/{!! $lead->id !!}"> <i class="fs fa-exchange-alt"></i> Transfer Lead</a>

                        @if(!$lead->moved_to_client)
                        <a class="btn btn-primary btn-xs convertbtn" href="{{route('admin.lead.convert_lead_clients-confirm',$lead->id)}}" data-toggle="modal" data-target="#modal_dialog"> <i class="fa fa-exchange"></i> Convert</a>
                        @endif
                    </span> 
                </div>

            </div>


        </section>
    </div>
    <div class="panel-body">
        <div class="profileSection">
            <div class="row">
                <div class="col-md-10">
                    <div class="row">

                        <form action="{!! route('admin.leads.storelogo', $lead->id) !!}" method="POST" enctype="multipart/form-data">

                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                        <div class="col-md-4">
                            @if($lead->logo)

                            <img src="{{asset('leads/'.$lead->logo)}}" class="p-image" id="blah" src="#" alt="your image" />

                            @else

                            <img src="/images/profiles/default.png" class="p-image" id="blah" src="#" alt="your image" />
                            @endif
                            <!-- <img src="/images/profiles/default.png" class="p-image" > -->
                              

                            <input accept="image/*" type='file' name="logo" class:"upload-img" id="imgInp" />
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-default btn-xs" type="submit" style="position: relative; top: 110px;margin-left: -25px;">Upload</button>
                        </div>
                        </form>
                        <div class="col-md-8 p-profile">
                           <h3>{{ $lead->name}}</h3> 
                           <ul class="social-icon">
                           
                            <li class="so-icon twit"> <i class="fa fa-twitter " aria-hidden="true"></i></li>
                            <li class="so-icon face"><i class="fa fa-facebook " aria-hidden="true"></i></li>
                            <a target="_blank" href="{{ $lead->homepage}}">
                            <li class="so-icon face"><i class="fa fa-globe " aria-hidden="true"></i></li></a>

                            <li>
                              <a href="{!! route('admin.leads.index') !!}?type={{\Request::get('type')}}" class='btn btn-default btn-xs'>{{ trans('general.button.close') }}</a></li><li>
                                @if ( $lead->isEditable() || $lead->canChangePermissions() )
                                <a href="{!! route('admin.leads.edit', $lead->id) !!}?type={{\Request::get('type')}}" class='btn btn-default btn-xs'>{{ trans('general.button.edit') }}</a>
                                @endif
                            </li>
                        </ul>   
                        <p>{{ $lead->position}}</p>
                        <p><i class="fa fa-map-marker" aria-hidden="true" style="margin-right: 4px;"></i>
                        {!! $lead->city !!}, {!! $lead->address_line_1 !!}</p>

                    </div>

                </div>

            </div> 
            <div class="col-md-7">

            </div> 
        </div>

    </div>
</div>
</div>
<div class="panel panel-default" style="margin-top: -15px;">
    <!--   <div class="panel-heading">Panel Heading</div> -->
    <div class="panel-body">
      <div class="profile-d">
          <dl class="dl-horizontal">
            <div class="row">
                <div class="col-md-6">
                  
                @if($lead->mob_phone != '')

                <dt>Mobile Phone</dt>
                <dd><span id='mob_phone'>{{ $lead->mob_phone}}</span> &nbsp;
                    <a _blank href="viber://chat?number=977{{$mobile_no}}">
                        <span class="fa fa-viber"> Viber </span> </a> .. &nbsp;
                        <a target="_blank" href="https://api.whatsapp.com/send?phone={{$mobile_no}}"> <i class="fa fa-whatsapp"> Whatsapp </i></a>

                    </dd>
                    @endif

                    @if($lead->home_phone != '')
                    <dt>Home Phone</dt>
                    <dd><a href="tel:{{ $lead->home_phone }}"> {{ $lead->home_phone }} </a></dd>
                    @endif


                    @if(!empty($lead->homepage))
                    <dt>Homepage</dt>
                    <dd><a target="_blank" href="{{ $lead->homepage }}">
                    Visit Website</a></dd>
                    @endif

                    @if(!empty($lead->email))
                    <dt>Email</dt>
                    <dd>
                        <a href="mailto:{{ $lead->email }}" id='email-edit'>{{ $lead->email }}</a>
                        <a href="javascript:void()" title="edit email" id='email-edit-button'><i class="fa fa-edit editable"></i></a>
                    </dd>
                    @endif

                    <dt>Owner</dt>
                <dd>
                    {!! $lead->user->first_name !!}
                </dd>

                <dt>Description</dt>
                <dd>
                    <span style=" overflow: hidden;text-overflow: ellipsis;display: -webkit-box;-webkit-line-clamp: 4;-webkit-box-orient: vertical;" id='lead_description'>{!! $lead->description !!}</span>

                </dd>

                @if(!empty($lead->city))
                   <dt>Address</dt>
                   <dd>
                    {!! $lead->city !!}, {!! $lead->address_line_1 !!}
                </dd>
                @endif

                </div>
                <div class="col-md-6">
                

                @if($lead->target_date != '0000-00-00')
                <dt>Next Action Date</dt>
                <dd>
                    <input id="datepicker_follow_date" style="width: 60px;border:none;" value="{{date('d M y',strtotime($lead->target_date))}}">
                </dd>
                @endif

                @if($lead->rating)
                <dt>Rating</dt>
                <dd>
                    <span id='rating' data-type='select' data-value='{{  $lead->rating }}'></span>
                </dd>
                @endif

                @if($lead->status_id)
                <dt>Status</dt>
                <dd>
                    <span id='status_id' data-type='select' data-value='{!! $lead->status_id !!}'></span>
                </dd>
                @endif

                @if($lead->campaign_id)
                <dt>Campaign </dt>
                <dd>
                    <span id='campaign_id' data-type='select' data-value='{{ $lead->campaign_id }}'></span>
                </dd>
                @endif

                @if($lead->communication_id)
                <dt>Source</dt>
                <dd>
                    <span id="source_id" data-type='select' data-value='{!! $lead->communication_id !!}'> </span>
                </dd>
                @endif

                @if($lead->skype)
                <dt>Skype</dt>
                <dd>
                    {!! $lead->skype !!}
                </dd>
                @endif

                @if($lead->price_value)
                <dt>Value</dt>
                <dd>
                    {!! $lead->price_value !!}
                </dd>
                @endif

                @if($lead->dob)
                <dt>Date of Birth</dt>
                <dd>
                    <input id="date_of_birth" style="width: 60px;border:none;" value="{{date('d M y',strtotime($lead->dob))}}">
                </dd>
                @endif

                
            </div>
        </div>



    </dl>
</div>
</div>
</div>

   <!--  <section class="content">
       <style>

        .steps{min-height:90px;padding:30px 0 0 0;font-family:'Open Sans', sans-serif;position:relative}.steps .steps-container{background:#DDD;height:10px;width:100%;border-radius:10px   ;-moz-border-radius:10px   ;-webkit-border-radius:10px   ;-ms-border-radius:10px   ;margin:0;list-style:none}.steps .steps-container li{text-align:center;list-style:none;float:left}.steps .steps-container li .step{padding:0 50px}.steps .steps-container li .step .step-image{margin:-14px 0 0 0}.steps .steps-container li .step .step-image span{background-color:#DDD;display:block;width:37px;height:37px;margin:0 auto;border-radius:37px   ;-moz-border-radius:37px   ;-webkit-border-radius:37px   ;-ms-border-radius:37px   }.steps .steps-container li .step .step-current{font-size:11px;font-style:italic;color:#999;margin:8px 0 0 0}.steps .steps-container li .step .step-description{font-size:13px;font-style:italic;color:#538897}.steps .steps-container li.activated .step .step-image span{background-color:#5DC177}.steps .steps-container li.activated .step .step-image span:after{background-color:#FFF;display:block;content:'';position:absolute;z-index:1;width:27px;height:27px;margin:5px;border-radius:27px   ;-moz-border-radius:27px   ;-webkit-border-radius:27px   ;-ms-border-radius:27px   ;box-shadow: 4px 4px 0px 0px rgba(0,0,0,0.15) ;-moz-box-shadow: 4px 4px 0px 0px rgba(0,0,0,0.15) ;-webkit-box-shadow: 4px 4px 0px 0px rgba(0,0,0,0.15) }.steps .step-bar{background-color:#5DC177;height:10px;position:absolute;top:30px;border-radius:10px 0 0 10px;-moz-border-radius:10px 0 0 10px;-webkit-border-radius:10px 0 0 10px;-ms-border-radius:10px 0 0 10px}.steps .step-bar.last{border-radius:10px   ;-moz-border-radius:10px   ;-webkit-border-radius:10px   ;-ms-border-radius:10px   }
    </style>
</section>
<div class="steps">
    <ul class="steps-container">
        <li style="width:25%;" class="activated">
            <div class="step">
                <div class="step-image"><span></span></div>
                <div class="step-current">Etapa 1</div>
                <div class="step-description">New</div>
            </div>
        </li>
        <li style="width:25%;">
            <div class="step">
                <div class="step-image"><span></span></div>
                <div class="step-current">Etapa 2</div>
                <div class="step-description">Hot</div>
            </div>
        </li>
        <li style="width:25%;">
            <div class="step">
                <div class="step-image"><span></span></div>
                <div class="step-current">Etapa 3</div>
                <div class="step-description">Cold</div>
            </div>
        </li>
        <li style="width:25%;">
            <div class="step">
                <div class="step-image"><span></span></div>
                <div class="step-current">Etapa 4</div>
                <div class="step-description">Pre Sales</div>
            </div>
        </li>

    </ul>
    <div class="step-bar" style="width: 25%;"></div>
</div> -->
<div class="panel panel-default" style="margin-top:-15px;">
  <div class="panel-body">
      <ul class="nav nav-tabs tabpanel-section" id="myTab" role="tablist">

        @if($stages)
        @foreach($stages as $key=>$stage)
        <li class="nav-item nav-section">
            <a href="{{route('admin.leads.updatestage', ['lead_id' => $lead->id, 'stage_id'=>$key])}}" class="nav-link {{ $key == $lead->stage_id ? 'active' : '' }}">{{ $stage }}</a>
        </li>
        @endforeach

        @endif
          
        <!-- <li class="nav-item nav-section">
            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile"
            aria-selected="false">Hot Prospectus</a>
        </li>
        <li class="nav-item nav-section">
            <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact"
            aria-selected="false">Negotiation</a>
        </li>
        <li class="nav-item nav-section">
            <a class="nav-link" id="address-tab" data-toggle="tab" href="#address" role="tab" aria-controls="address"
            aria-selected="false">Pre Sales</a>
        </li>
        <li class="nav-item nav-section">
            <a class="nav-link" id="phone-tab" data-toggle="tab" href="#phone" role="tab" aria-controls="phone"
            aria-selected="false">Won</a>
        </li>
        <li class="nav-item nav-section ">
            <a class="nav-link" id="email-tab" data-toggle="tab" href="#email" role="tab" aria-controls="email"
            aria-selected="false">Lost</a>
        </li> -->
  <!--   <li class="nav-item drop-nav nav-section dropdown">
      <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">Dropdown</a>
      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
    <a class="dropdown-item" href="#">Action</a>
    <a class="dropdown-item" href="#">Another action</a>
    <a class="dropdown-item" href="#">Something else here</a>
  </div>
</li> -->
</ul>


</div>
</div>

<div class="panel panel-default" style="margin-top:-15px;">
  <div class="panel-body">
    <div class="box  box-primary">
        <div class="box-header">

            <h3 class="box-title" style="font-size: 20px !important; font-weight: 600; margin-top: -13px;">Update Follow up</h3>
            <p><i class="fa fa-info-circle"></i> Tips: Record your customer interaction and iterate through stages. This will be added to the follow up feeds. If the lead in won or lost it will go to archive, however won leads will be copied to <a target="_blank" href="/admin/clients">clients</a> </p>
        </div>

        {!! Form::textarea('lead_note', $lead->lead_note, ['class' => 'form-control', 'id'=>'lead_note', 'placeholder' => 'Make a note for this lead', 'rows'=>'2']) !!}<br />
        <input type="checkbox" name="note_to_task" id="note_to_task" value="1"> &nbsp; Add Note to Task<br /><br/>
        <div id="task_dates" style="display: none">
            <div class="row">
                <div class="col-md-4" id='task_due_date_div' style="display: none">
                    <div class="form-group">
                        {!! Form::label('task_due_date', trans('admin/tasks/general.columns.task_due_date')) !!} <i class="fa fa-calendar"></i>
                        {!! Form::text('task_due_date', $lead->task_due_date, ['class' => 'form-control', 'id'=>'task_due_date']) !!}
                    </div>
                </div>

                <div class="col-md-4" id='closure_reason_div' style="display: none">
                    <div class="form-group">
                        {!! Form::label('closure_reason', 'Closure Reasons') !!} <i class="fa fa-edit"></i>
                        {!! Form::select('closure_reason', [''=>'select reason']+$closure_reason, $lead->reason_id,['class' => 'form-control', 'id'=>'closure_reason']) !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('lead_stage', 'Lead Stage') !!} <i class="fa fa-user"></i>
                        {!! Form::select('lead_stage_id', $stages, $lead->stage_id, ['class' => 'form-control input-sm', 'id'=>'lead_stage_id']) !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('task_assign_to', trans('admin/tasks/general.columns.task_assign_to')) !!} <i class="fa fa-user"></i>
                        {!! Form::select('task_assign_to', $users, \Auth::user()->id, ['class' => 'form-control input-sm', 'id'=>'task_assign_to']) !!}
                    </div>
                </div>

            </div>
        </div>
        {!! Form::button('Submit', ['class' => 'btn btn-primary btn', 'id'=>'submit-note']) !!}

        <br><br>

    </div>
</div>
</div>

<div class="panel panel-default" style="margin-top: -15px;">
  <div class="panel-body">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs bg-danger">
            <li class=""><a href="#activity" data-toggle="tab" aria-expanded="false">Notes</a></li> 
            <li class="active"><a href="#timeline" data-toggle="tab" aria-expanded="true">Follow up</a></li>
            @if($lead->stage_id == 3 || $lead->stage_id == 4)
            <li class="quotations-proposal"><a href="#quote" data-toggle="tab">Quotations</a></li>
            @endif
            <li><a href="#filendocs" data-toggle="tab">Files & Docs</a></li>
            <li><a href="#meetings" data-toggle="tab">Meetings</a></li>
            <li><a href="#logs" data-toggle="tab">Logs</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane" id="activity">
                <!-- Post -->



                <div class="">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <i class="fa fa-info"></i>

                            <h3 class="box-title">{{ $lead->name }} <small>looking for</small>
                                <span style=""><b>{{ $lead->product->name }}</b></span>
                              
                            </h3>

                        </div>
                        <!-- /.box-header -->
                       
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>

                    <div class="note-list" id="note-list">
                        @foreach($notes as $k => $v)
                        <div class="note-wrap" style="margin-top:10px; padding:0 15px; position: relative;">

                            <p data-letters="{{mb_substr($v->note,0,3)}}">{!! $v->note !!}</p>
                            <i class="date">{!! ' ('.\Carbon\Carbon::createFromTimeStamp(strtotime($v->created_at))->diffForHumans().') by '.$v->user->first_name !!}</i>
                            @if(Auth::user()->id == $v->user_id)
                            <a title="Delete" data-target="#modal_dialog" data-toggle="modal" href="/admin/leadnotes/{!! $v->id !!}/confirm-delete" style="position:absolute; top:0; right:0;"><i class="fa fa-trash deletable"></i></a>
                            @endif
                        </div>
                        <hr style="margin:5px 0 0; border-color:#000;">
                        @endforeach
                    </div>

                    <!-- /.post -->
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane active" id="timeline">
                    <!-- The timeline -->
                    <ul class="timeline timeline-inverse">
                        <!-- timeline time label -->

                        @foreach($follow_up as $dates => $timeline)
                        <?php ++$loop ?>
                        <li class="time-label">
                            <span class="@if($loop  % 2 == 0) bg-red @else bg-blue @endif ">
                                {{ date('dS Y M',strtotime($dates)) }}
                            </span>
                        </li>
                        <!-- /.timeline-label -->
                        <!-- timeline item -->
                        @foreach($timeline as $key=>$tm)
                        <li>
                            <i class="fa {{ $tm->icons }} {{ $tm->color }}"></i>

                            <div class="timeline-item">
                                <span class="time"><i class="fa fa-clock-o"></i> {!! \Carbon\Carbon::createFromTimeStamp(strtotime($tm->created_at))->diffForHumans() !!}</span>
                                @if($tm->change_type == 'tasks')
                                <h3 class="timeline-header"><a href="/admin/profile/show/{{$tm->user->id}}">{{ $tm->user->username }}</a> has assigned task to {{ $tm->assigned_to->username }}</h3>
                                @elseif($tm->change_type =='closure')
                                <h3 class="timeline-header"><a href="/admin/profile/show/{{$tm->user->id}}">{{ $tm->user->username }}</a> has Closed the lead</h3>
                                @else
                                <h3 class="timeline-header"><a href="/admin/profile/show/{{$tm->user->id}}">{{ $tm->user->username }}</a> changed {{ $tm->change_type }}</h3>
                                @endif
                                <div class="timeline-body">
                                    {!! ucfirst($tm->activity) !!}
                                </div>

                            </div>
                        </li>
                        @endforeach
                        @endforeach
                        <!-- /.timeline-label -->
                        <!-- timeline item -->

                        {{-- <li>
                            <i class="fa fa-camera bg-purple"></i>

                            <div class="timeline-item">
                              <span class="time"><i class="fa fa-clock-o"></i> 2 days ago</span>

                              <h3 class="timeline-header"><a href="#">Mina Lee</a> uploaded new photos</h3>

                              <div class="timeline-body">
                                <img src="http://placehold.it/150x100" alt="..." class="margin">
                                <img src="http://placehold.it/150x100" alt="..." class="margin">
                                <img src="http://placehold.it/150x100" alt="..." class="margin">
                                <img src="http://placehold.it/150x100" alt="..." class="margin">
                            </div>
                        </div>
                    </li> --}}
                    <!-- END timeline item -->

                    <li>
                        <i class="fa fa-clock-o bg-gray"></i>
                    </li>
                </ul>

            </div>
            <!-- /.tab-pane -->

            <div class="tab-pane" id="quote">

                @if(isset($proposal) && sizeof($proposal))
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-briefcase"></i> Recent Contracts and Proposals</h3>

                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                        </div>
                    </div>

                    <!-- /.box-header -->
                    <div class="box-body">
                        <ul class="products-list product-list-in-box">

                            @foreach($proposal as $k)
                            <li class="item">
                                <div class="product-img">
                                    <span data-letters="{{mb_substr($k->product->name,0,3)}}"></span>
                                </div>

                                <div class="product-info">
                                    <a href="javascript:void(0)" class="product-title">{{ $k->product->name }}
                                        <span class="label label-success pull-right">{{ ucfirst($k->status)}}</span></a>
                                        <span class="product-description">
                                            {!! link_to_route('admin.proposal.show', $k->subject, [$k->id], ['target' => '_blank']) !!}
                                        </span>
                                    </div>
                                </li>
                                @endforeach

                            </ul>
                        </div>
                        <!-- /.box-body -->

                    </div>
                    @endif


                    @if(isset($quotations) && sizeof($quotations))
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-briefcase"></i> Recent Quotations</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                </button>
                                <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                            </div>
                        </div>


                        <!-- /.box-header -->
                        <div class="box-body">
                            <ul class="products-list product-list-in-box">


                                @foreach($quotations as $k)
                                <li class="item">
                                    <div class="product-img">
                                        <span data-letters="{{mb_substr($k->total,0,3)}}"></span>
                                    </div>

                                    <div class="product-info">
                                        <a href="javascript:void(0)" class="product-title">{{ ucfirst($k->status)}}
                                            <h3 class="pull-right ">{{env('APP_CURRENCY')}} {{ number_format($k->total_amount,2)}}</h3>
                                        </a>
                                        <span class="product-description">
                                            {!! link_to_route('admin.orders.show', $k->lead->name . ' #'.$k->id , [$k->id], []) !!}
                                        </span>
                                    </div>
                                </li>
                                @endforeach

                            </ul>
                        </div>
                        <!-- /.box-body -->

                    </div>
                    @endif

                </div>

                <div class="tab-pane" id="filendocs">

                    <div class="box box-default">
                        <div class="box-body">
                            <label>File: </label>
                            {!! Form::file('lead_file', ['class' => 'lead_file', 'id'=>'lead_file']) !!}<br />

                            {!! Form::button('Upload', ['class' => 'btn btn-primary btn-xs', 'id'=>'upload-file']) !!}<br />

                            <div class="file-list" id="file-list">

                                @foreach($files as $key => $val)
                                <div class="task-wrap" style="margin-top:10px; padding:0 15px; position: relative;">
                                    <p style="margin-bottom:0; font-weight:bold;"><a href="/files/{!! $val->file !!}">{!! $val->file !!}</a></p>
                                    <i class="date">{!! \Carbon\Carbon::createFromTimeStamp(strtotime($val->created_at))->diffForHumans().' by '.$val->user->first_name !!}</i>
                                    @if(\Auth::user()->id == $val->user_id)
                                    <a title="Delete" data-target="#modal_dialog" data-toggle="modal" href="/admin/leadfiles/{!! $val->id !!}/confirm-delete" style="position:absolute; top:0; right:0;"><i class="fa fa-trash deletable"></i></a>
                                    @endif
                                </div>
                                @endforeach
                            </div>

                        </div>
                    </div>

                </div>

                <div class="tab-pane" id="meetings">

                    <div class="box box-default">
                        <div class="box-body">
                            <div class="col-md-12">
                                <label>Tasks: </label>
                                <div style="float:right; display:inline-block;"><a class="btn btn-primary btn-xs" href="/admin/tasks/create?lead_id={!! $lead->id !!}" data-target="#modal_dialog" data-toggle="modal" data-backdrop="static" data-keyboard="false">Create Task</a></div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="task-list" id="task-list">
                                @foreach($tasks as $tk => $tv)
                                <div class="task-wrap" style="margin-top:10px; padding:0 15px; position: relative;">
                                    <a href="/admin/tasks/{!! $tv->id !!}">{!! $tv->task_subject !!}</a><br />
                                    <i class="date">Assigned To: {!! $tv->assigned_to->first_name !!}</i>
                                    <?php
                                    $due = date('Y-m-d',strtotime($tv->task_due_date));
                                    if($due == date('Y-m-d'))
                                      $d_date = '<span style="color:red;">'.$tv->task_due_date.'</span>';
                                  else
                                      $d_date = $tv->task_due_date;
                                  ?>
                                  <span style="position:absolute; top:0; right:0;">Due Date: {!! $d_date !!}</span>
                              </div>
                              @endforeach
                          </div>
                      </div>
                  </div>

              </div>

              <div class="tab-pane" id="logs">
                <h4><i class="fa fa-mobile"></i> &nbsp;&nbsp; App Call Logs</h4>
                <table class="table">
                    <tr>
                        <th>User</th>
                        <th>Mobile No.</th>
                        <th>Created At</th>
                    </tr>
                    @foreach($phone_logs as $pl)
                    <tr>
                        <td>{{ $pl->user->username }}</td>
                        <td><a href="tel:{{ $pl->mob_phone }}">{{ $pl->mob_phone }}</a></td>
                        <td>{{ date('dS Y M',strtotime($pl->created_at)) }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
            <!-- /.tab-pane -->
        </div>
        <!-- /.tab-content -->
    </div>
</div>
</div>

</div>
<!-- /.nav-tabs-custom -->
<!--   </div> -->

<div class="col-md-3">

    <div class="box box-primary">
     <div class="panel panel-default">
      <div class="panel-heading">
          <div class="box-header with-border bg-info">
            <h3 class="box-title">New {{\Request::get('type')}}</h3>
        </div>
    </div>
    <div class="panel-body pdbody">
        <!-- /.box-header -->
        <div class="box-body bx-body">

            <table class="table  table-no-border ">
                <tr class="bg-success">
                    <th> ID#</th>
                    <th> Name</th>

                    <th> Followup </th>

                </tr>
                <tbody id="leads-table">
                    @foreach($leads as $l)
                    <tr @if(\Request::segment(3)==$l->id) class="bg-info" @endif>
                        <td style="font-size: 12px;">{{\FinanceHelper::getAccountingPrefix('LEADS_PRE')}}{{ $l->id }}</td>
                        <td style="float:left; font-size: 12px;">
                            <a href="/admin/leads/{{$l->id}}?type={{\Request::get('type')}}">{{$l->name}}</a>

                        </td>

                        <td>
                            @if($l->target_date >= date('Y-m-d') OR $l->target_date == date('0000-00-00'))
                            <span class="btn bg-success btn-xs">{{$l->target_date}}</span>
                            @else
                            <span class="btn btn-danger btn-xs">
                                <i class="fa fa-clock-o fa-spin"></i>
                            missed</span>
                            @endif
                        </td>


                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- /.box-body -->
</div>
<!-- /.box -->
</div>

</div>







<div role="dialog" class="modal fade" id="sendSMS" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width:400px;">
        <!-- Modal content-->
        {!! Form::open( array('route' => 'admin.leads.send-lead-sms') ) !!}
        <div class="modal-content">
            <div class="modal-header bg-orange">
                <button data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">Send SMS</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <!-- <span>Note: Maximum 138 character limit</span><br/>
                        <textarea rows="3" name="message" class="form-control" id="message-textarea" placeholder="Type your message." maxlength="138" required></textarea> -->
                        <textarea rows="3" name="message" class="form-control" id="message-textarea" placeholder="Type your message." required></textarea>
                        <input type="hidden" name="lead_id" id="lead_id" value="{!! $lead->id !!}">
                        <input type="hidden" name="recipients_no" value="{!! $lead->mob_phone !!}">
                        <!-- <span class="char-cnt"></span> -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('general.button.cancel') }}</button>
                    <button type="submit" class="btn bg-orange" name="submit">Send</button>
                </div>
            </div>
            <script>
            /*var text_max = 138;
            $('.char-cnt').html(text_max + ' characters remaining');*/
            $(document).ready(function() {
                /*$('#message-textarea').keyup(function() {
                      var text_length = $('#message-textarea').val().length;
                      var text_remaining = text_max - text_length;
                      $('.char-cnt').html(text_remaining + ' characters remaining');
                  });*/

                  $(".modal").on("hidden.bs.modal", function() {
                    //$(".modal-body1").html("");
                });
              });

          </script>
          {!! Form::close() !!}
      </div>
  </div>
  @endsection
  <link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" />
  @section('body_bottom')
  <!-- Select2 js -->
  @include('partials._body_bottom_select2_js_role_search')
  <script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>

  <script>

    //$(".nav-link").each(function(i){ 
  //$(this).click(function(e){
    //$(this).attr("href", $('.nav-link')[i].id);
    //window.location.hash  = $('.nav-link')[i].id;
  //});
//});

    imgInp.onchange = evt => {
      const [file] = imgInp.files
      if (file) {
        blah.src = URL.createObjectURL(file)
      }
    }



    function HandlePopupResult(result) {
        let l = result.leads;

        let html = `<tr>
        <td>{{ env('APP_CODE')}} ${l.id}</td>                                  
        <td style="float:lef; font-size: 16px">
        <a href="/admin/leads/${l.id}?type={{\Request::get('type')}}">${l.name}</a>

        </td>
        <td></td>
        <td>
        <span class="btn btn-xs"></span>
        </td>           
        </tr>`;

        $('#leads-table').prepend(html);
    }

    function openmodal() {
        var win = window.open(`/admin/leads/create/modal?type={!! $_GET['type'] !!}`, '_blank', 'toolbar=yes, scrollbars=yes, resizable=yes, top=500,left=500,width=600, height=650');
    }
    const lead_reason_id = '{{$lead->reason_id ? $lead->reason_id : null}}';

    function handleLeadStage(lead_stage) {

        if (lead_stage == 5 || lead_stage == 6) {
            if (lead_stage == 5) {
                $('#closure_reason').val(lead_reason_id ? lead_reason_id : '11');
            }
            $('#closure_reason_div').css('display', 'block');
            $('#task_due_date_div').css('display', 'none');
        } else {
            $('#closure_reason_div').css('display', 'none');
            $('#task_due_date_div').css('display', 'block');
        }
    }
    $(function() {
        var lead_stage = '{{$lead->stage_id ? $lead->stage_id : null}}';
        handleLeadStage(lead_stage);
        $('#lead_stage_id').change(function() {
            let id = $(this).val();
            handleLeadStage(id);
        })
    })


        // To submit the note for the Lead - by Ajax 
        $(document).on('click', '#submit-note', function() {

            var token = $('meta[name="csrf-token"]').attr('content');
            var user_id = '{!!\Auth::user()->id!!}';
            var lead_id = '{!! $lead->id !!}';
            var task_assign_to = $("#task_assign_to").val();
            var note = $("#lead_note").val();
            var stage_id = $('#lead_stage_id').val();
            if (note != '') {
                $("#lead_note").removeClass("has-error");
                // Check if 'Add Note to Task' is checked or not
                if ($("#note_to_task").is(':checked')) {
                    $("#task_due_date").removeClass("has-error");

                    var task_due_date = $("#task_due_date").val();


                    if (stage_id == 5 || stage_id == 6) {
                        let c = confirm('Are you sure you want to make changes');
                        if (!c) {
                            return false;
                        }
                    } else {
                        if (task_due_date.trim() == '') {
                            $("#task_due_date").addClass("has-error");
                            return false;
                        }
                    }
                    let closure_reason = $('#closure_reason').val();
                    var datastring = '_token=' + token + '&user_id=' + user_id + '&lead_id=' + lead_id + '&note=' + note + '&task_due_date=' + task_due_date + '&task_assign_to=' + task_assign_to + '&stage_id=' + stage_id + '&closure_reason=' + closure_reason;
                    $.ajax({
                        url: '/admin/leadnotes'
                        , dataType: 'JSON'
                        , type: 'post'
                        , contentType: 'application/x-www-form-urlencoded'
                        , data: datastring
                        , success: function(data) {

                            if (stage_id == 4) //redirect to customer
                                location.href = `{!! url('/') !!}/admin/leads/${lead_id}?type=customer`;
                            else if (stage_id == 5 || stage_id == 6) //redirect to quotations
                                location.reload();
                            else
                                location.reload();
                            $("#lead_note").val('')
                            // document.getElementById('note-list').innerHTML = data.messages;
                        }
                        , error: function(jqXhr, textStatus, errorThrown) {
                            alert("Some Thing Went Wrong");
                            console.log(errorThrown);
                        }
                    });

                } else {
                    var datastring = '_token=' + token + '&user_id=' + user_id + '&lead_id=' + lead_id + '&note=' + note;
                    $.ajax({
                        url: '/admin/leadnotes'
                        , dataType: 'JSON'
                        , type: 'post'
                        , contentType: 'application/x-www-form-urlencoded'
                        , data: datastring
                        , success: function(data) {
                            document.getElementById('note-list').innerHTML = data.messages;
                        }
                        , error: function(jqXhr, textStatus, errorThrown) {
                            console.log(errorThrown);
                        }
                    });
                }
            } else {
                $("#lead_note").addClass("has-error");
            }

        });


        $(document).on('click', '.delete-note', function() {

            var token = $('meta[name="csrf-token"]').attr('content');
            var note_id = $(this).attr('id').split('-')[1];

            var datastring = '_token=' + token + '&note_id=' + note_id;
            $.ajax({
                url: '/admin/leadnotes/' + note_id + '/confirm-delete'
                , dataType: 'JSON'
                , type: 'get'
                , contentType: 'application/x-www-form-urlencoded'
                , data: datastring
                , success: function(data) {
                    document.getElementById('note-list').innerHTML = data.messages;
                    $("#lead_note").val('');
                }
                , error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                    $("#lead_note").val('');
                }
            });
        });


    //To submit the file for the Lead - by Ajax 
    $(document).on('click', '#upload-file', function() {
      if($("#lead_file").val() !='')
      {
        $("#lead_file").removeClass("danger");
        var formData = new FormData();
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('lead_id', {!! $lead->id !!});
        formData.append('user_id', {!! \Auth::user()->id !!});
        formData.append('file_name', $('#lead_file')[0].files[0].name.split('.').shift());
        //formData.append('file', $('#lead_file').val());
        formData.append('file', $('#lead_file')[0].files[0]);
        //formData.append('file', $('input[type=file]')[0].files[0]);
        $.ajax({
          url: '/admin/leadfiles',
          data: formData,
          dataType: 'JSON',
          type: 'post',
          //async:false,
          processData: false,
          contentType: false,
          //contentType: 'application/x-www-form-urlencoded',
          success: function(data){
             document.getElementById('file-list').innerHTML = data.messages;
         },
         error: function( jqXhr, textStatus, errorThrown ){
            console.log( errorThrown );
        }
    });
    }
    else
        $("#lead_file").addClass("danger");
});

    <!-- To delete the note for the Lead -->
    $(document).on('click', '.delete-file', function() {

      var token =  $('meta[name="csrf-token"]').attr('content');
      var file_id = $(this).attr('id').split('-')[1];

      var datastring = '_token='+token+'&file_id='+file_id;
      $.ajax({
        url: '/admin/leadfiles/'+file_id+'/confirm-delete',
        dataType: 'JSON',
        type: 'get',
        contentType: 'application/x-www-form-urlencoded',
        data: datastring,
        success: function(data){
           document.getElementById('file-list').innerHTML = data.messages;
       },
       error: function( jqXhr, textStatus, errorThrown ){
          console.log( errorThrown );
      }
  });
  });

    <!-- To show the detail of the email -->
    $(document).on('click', '.mail-wrap', function() {

      var token =  $('meta[name="csrf-token"]').attr('content');
      var mail_id = $(this).attr('id').split('-')[1];

      var datastring = '_token='+token+'&mail_id='+mail_id;
      $.ajax({
        url: '/admin/mail/from_lead/'+mail_id,
        //dataType: 'JSON',
        type: 'get',
        contentType: 'application/x-www-form-urlencoded',
        data: datastring,
        success: function(data){
          $('#modal_lead_mail').find(".modal-content").html(data.messages);
          $('#modal_lead_mail').modal();
      },
      error: function( jqXhr, textStatus, errorThrown ){
          console.log( errorThrown );
      }
  });
  });

    <!-- To clear the content of the modal box and reload another content -->
    $(document).ready(function() {
      $(".modal").on("hidden.bs.modal", function(){
         $(this).removeData();
     });
  });

    // $(document).on('change', '#status_id', function() {
    //    var id = $('#show_lead_id').val();
    //   var status_id = $(this).val();

    //   $.post("/admin/ajax_lead_status",
    //   {id: id, status_id: status_id, _token: $('meta[name="csrf-token"]').attr('content')},
    //   function(data, status){
    //     if(data.status == '1')
    //         $("#ajax_status").after("<span style='color:green;' id='status_update'>Status is successfully updated.</span>
    //");
    //     else
    //         $("#ajax_status").after("<span style='color:red;' id='status_update'>Problem in updating status; Please try again.</span>");

    //     $('#status_update').delay(3000).fadeOut('slow');
    //     //alert("Data: " + data + "\nStatus: " + status);
    //   });
    // });

    $(document).on('change', '#rating', function() {
        var id = $('#lead_id').val();
        var rating = $(this).val();

        $.post("/admin/ajax_lead_rating", {
            id: id
            , rating: rating
            , _token: $('meta[name="csrf-token"]').attr('content')
        }
        , function(data, status) {
            if (data.status == '1')
                $("#ajax_status").after("<span style='color:green;' id='status_update'>Successfully updated.</span>");
            else
                $("#ajax_status").after("<span style='color:red;' id='status_update'>Problem in updating ! Please try again.</span>");

            $('#status_update').delay(3000).fadeOut('slow');
                //alert("Data: " + data + "\nStatus: " + status);
            });
    });


    function makeChanges(type, value) {
        if (type == 'stages') {
            if (value == 3 || value == 4)
                $('.quotations-proposal').show();
            else
                $('.quotations-proposal').hide();
        }
    }

    function handleChange(lead_id, value, type, parent) {
        $.post("/admin/ajaxLeadUpdate", {
            id: lead_id
            , update_value: value
            , type: type
            , _token: $('meta[name="csrf-token"]').attr('content')
        }
        , function(data) {
            if (data.status == '1') {
                makeChanges(type, value);
                $("#ajax_status").after("<span style='color:green;' id='status_update'>" + type + " sucessfully updated</span>");
                $('#status_update').delay(3000).fadeOut('slow');
            }

                //alert("Data: " + data + "\nStatus: " + status);
            });
    }

    var courses = @php echo json_encode($courses); @endphp;
    const active_lead_id = $('#lead_id').val();


    $('#courses_id').each(function() {
        let parent = $(this).parent().parent();
        let lead_id = active_lead_id
        $(this).editable({
            source: courses
            , success: function(response, newValue) {
                handleChange(lead_id, newValue, 'courses', parent);
            }
            , });
    });
    $('#mob_phone').each(function() {
        let parent = $(this).parent().parent();
        let lead_id = active_lead_id
        $(this).editable({
            success: function(response, newValue) {
                handleChange(lead_id, newValue, 'mob_phone', parent);
            }
            , });
    });

    $('#source_id').each(function() {
        let parent = $(this).parent().parent();
        let lead_id = active_lead_id
        $('#source_id').editable({
            source: @php echo json_encode($sources) @endphp 
            , success: function(response, newValue) {
                handleChange(lead_id, newValue, 'sources', parent);
            }
            , });
    });

    $('#status_id').each(function() {
        let parent = $(this).parent().parent();
        let lead_id = active_lead_id;
        $(this).editable({
            source: @php echo json_encode($lead_status) @endphp
            , success: function(response, newValue) {
                handleChange(lead_id, newValue, 'status', parent);
            }
            , });
    });

    $('#campaign_id').each(function() {
        let parent = $(this).parent().parent();
        let lead_id = active_lead_id;
        $(this).editable({
            source: @php echo json_encode($campaigns) @endphp 
            , success: function(response, newValue) {
                handleChange(lead_id, newValue, 'campaign', parent);
            }
            , });
    });
    const lead_rating = @php echo json_encode($lead_rating); @endphp;
    $('#rating').each(function() {
        let parent = $(this).parent().parent();
        let lead_id = active_lead_id;
        $(this).editable({
            source: lead_rating
            , success: function(response, newValue) {
                handleChange(lead_id, newValue, 'rating', parent);
            }
            , });
    });

    $('#email-edit').editable({
        toggle: 'manual'
        , success: function(response, newValue) {
            handleChange(active_lead_id, newValue, 'email', null);
        }
        , });

    $('#email-edit-button').click(function(e) {
        e.stopPropagation();
        $('#email-edit').editable('toggle');
    })

    $('#datepicker_follow_date').each(function() {
        let parent = $(this).parent().parent();
        let lead_id = active_lead_id;
        $(this).datepicker({
            dateFormat: 'd M y'
            , sideBySide: true
            , onSelect: function(dateText) {
                handleChange(lead_id, dateText, 'target_date', parent);
            }
        });
    });

    $('#date_of_birth').each(function() {
        let parent = $(this).parent().parent();
        let lead_id = active_lead_id;
        $(this).datepicker({
            dateFormat: 'd M y'
            , sideBySide: true
            , changeMonth: true
            , changeYear: true
            , yearRange: "-150:-0"
            , onSelect: function(dateText) {
                handleChange(lead_id, dateText, 'dob', parent);
            }
        });
    });


    $('#lead_description').editable({
        type: 'textarea'
        , placement: 'left'
        , title: 'Lead Description'
        , success: function(response, newValue) {
            handleChange(active_lead_id, newValue, 'description', null);
        }
        , });



    $(document).on('hidden.bs.modal', '#modal_dialog', function(e) {
        $('#modal_dialog .modal-content').html('');
    });


    function handleModalResults(result) {
        console.log(result.html);
        $('#task-list').prepend(result.html);
        $('#modal_dialog').modal('hide');
    }

</script>
@endsection
