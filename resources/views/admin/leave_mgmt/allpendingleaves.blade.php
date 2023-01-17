@extends('layouts.master')
@section('content')

<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
    <h1>
       {{ $page_title ?? "Page Title"}}
        <small>{!! $page_description ?? "Page description" !!}</small>
    </h1>

    {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
</section>
<link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet" />
<script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>

<div class='row'>
        <div class='col-md-12'>

        	   <div class="box">
                    <form method="GET" action="/admin/allpendingleaves/">
                      <div class='row'>
                                <div class='col-md-12'>
                                      <div class="box box-default">
                            <div class="box-header bg-info">
                                <div class="col-md-6 pull-left">
                                   <div class="col-md-4">
                                   <div class="input-group">
                                          <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                          </div>
                                          <input type="text" placeholder="Start Date" class="form-control input-sm startdate" name='engstartdate' value="{{\Request::get('engstartdate')}}">
                                        </div>
                                    </div>
                                     <div class="col-md-4">
                                   <div class="input-group">
                                          <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                          </div>
                                          <input type="text" placeholder="End Date" class="form-control input-sm enddate" name='engenddate' value="{{\Request::get('engenddate')}}">
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-default btn-sm" name="filter" value="eng">Show Leaves</button>
                                    </div>

                                    <div class="col-md-6 pull-right">
                                    <div class="col-md-4">
                                   <div class="input-group">
                                          <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                          </div>
                                          <input type="text" placeholder="सुरु  मिति " class="form-control input-sm" id='nep-startdate' name="nepstartdate" value="{{\Request::get('nepstartdate')}}"  data-single='true'>
                                        </div>
                                    </div>
                                     <div class="col-md-4">
                                   <div class="input-group">
                                          <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                          </div>
                                          <input type="text" placeholder="अन्तिम मिति" class="form-control input-sm" id ='nep-enddate' name="nependdate" value="{{\Request::get('nependdate')}}"  data-single='true'>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-default btn-sm" name="filter" value="nep">नतिजा </button>
                                        <span class="btn btn-default btn-sm" id="btn-filter-clear">
                                         Reset
                                    </span>
                                    </div>
                        </div>
                         
                          <div class="box-body">

                           <div class="row">
                            <div class="col-md-4">
                                <label class="control-label"> &nbsp; Leave Status</label>

                                {!! Form::select('leave_status',['1'=>'Pending','2'=>'Accepted','3'=>'Rejected','4'=>'All Leaves'],Request::get('leave_status'))  !!}


                            </div>
                            <div class="col-md-4">
                                
                            </div>
                            <div class="col-md-4">
                                

                                     <label class="control-label">User</label>
                                     <select class="form-control searchable" name="user_id" style="width: 150px;">
                                        <option value="">Select User</option>
                                         @foreach($users as $cv)
                                         <option value="{{$cv->id }}" 
                                            @if(\Request::get('user_id') == $cv->id) selected="" @endif>{{$cv->username}}</option>
                                         @endforeach

                                     </select>
                                    <script type="text/javascript">
                                        $('.searchable').select2();
                                    </script>
                                     <button class="btn btn-info btn-sm" class="form-control" name="filter" value="{{\Request::get('filter')}}">Search</button>

                           
                                </div>
                            
                           </div>
                            </form> 
                     <br/>
                    	<div class="">

                    		<table class="table table-hover table-no-border table-striped" id="leads-table">
                        {{--  @if(\Request::get('user_id'))
                          <?php 
                            $userinfo =  \App\User::find(\Request::get('user_id'));
                            $linemanger = $userinfo->firstLineManger;
                           ?>
                          <tr class="bg-blue">
                              <th>
                                Staff:  
                              </th>
                              <td>
                                {{$userinfo->first_name }} {{$userinfo->last_name }}
                              </td>
                              <th>
                                Department:  
                              </th>
                              <td>
                                {{ $userinfo->department->deptname }}
                              </td>
                              <th>
                                Desgination:  
                              </th>
                              <td>
                                {{ $userinfo->designation->designations }}
                              </td>
                          </tr>
                            <tr class="bg-success">
                              <th>
                                Supervisor:  
                              </th>
                              <td>
                               {{$linemanger->first_name }} {{$linemanger->last_name }}
                              </td>
                              <th>
                                Department:  
                              </th>
                              <td>
                                {{ $userinfo->department->deptname }}
                              </td>
                              <th>
                                Desgination:  
                              </th>
                              <td>
                                {{$linemanger->designation->designations }} 
                              </td>
                          </tr>
                          @endif --}}

                               
                                    <tr class="bg-info">
                                      <th> LID</th>
                                        <th>Name</th>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Period</th>
                                        <th>Leave Category</th>
                                        <th>Status</th>
                                        <th class="col-sm-2">Action</th>
                                    </tr>
                     
                                <tbody>
                                	 @foreach($allleaves as $lv)
                                      <?php $user = TaskHelper::getUserData($lv->user_id); ?>
                                         @if($lv->isDeletable())
                                        <tr>
                                          <td>LV{{ $lv->leave_application_id }}</td>
                                            <td style="white-space: normal;line-height: 1.1" title=" 1st Line Manager: {{ \TaskHelper::getUserData($user->first_line_manager)->username}} 2nd Line Manager: {{ \TaskHelper::getUserData($user->second_line_manager)->username}}
                                                Work Station: {{$user->work_station}} &#13;&#10;
                                                Division: {{$user->division}}
                                                    ">

                                              <img src="/images/profiles/{{$lv->user->image ? $lv->user->image : 'attReq.jpg'}}" class="rounded" style="width: 30px;height: 30px;" alt="User Image">

                                              {{ $lv->user->first_name.' '.$lv->user->last_name }}</td>
                                            <td>{{ date('d, M y', strtotime($lv->leave_start_date)) }}</td>
                                            <td>{{ date('d, M y', strtotime($lv->leave_end_date)) }}</td>
                                            <td> <?php 
                                               
                                                  $date1=strtotime($lv->leave_end_date);
                                                  $date2=strtotime($lv->leave_start_date);
                                                  $diff=  $date1 - $date2; //date_diff($date2,$date1);

                                                  if($date1 == $date2){
                                                    echo "1 day";
                                                  }else{
                                                  //echo $diff->format("%R%a days"); 
                                                  echo round($diff / (60 * 60 * 24) + 1) . ' days';
                                                }

                                               ?></td>
                                            <td><span class="material-icons">{{ $lv->category->icon }}</span>{{ $lv->category->leave_category }}</td>
                                            <td><span class="label @if($lv->application_status == 1) label-warning @elseif($lv->application_status == 2) label-success @else label-danger @endif">@if($lv->application_status == 1) Pending @elseif($lv->application_status == 2) Accepted @else Rejected @endif</span></td>
                                            <td>

                                          

                                  @if((($lv->user_id != \Auth::user()->id) &&(\Auth::user()->canapprove_or_deny  == 1 ) && ($lv->application_status != 2) && ($lv->application_status !=3))|| ((\Auth::user()->hasRole(['admins','hr-staff'])) && ($lv->application_status ==1))) 
                                    @if((\TaskHelper::getUserData($user->first_line_manager)->id == \Auth::user()->id && $lv->is_forwarded != 2) ||  ((\Auth::user()->hasRole(['admins','hr-staff'])) && ($lv->application_status ==1))) 
                                     <a href="/admin/leave_request_modal/{{ $lv->leave_application_id }}"
                                                            class="btn btn-primary btn-xs" title="Edit"
                                                            data-toggle="modal" data-target="#modal_dialog"><span
                                                class="fa fa-edit"></span></a>
                                     @elseif( (\TaskHelper::getUserData($user->second_line_manager)->id == \Auth::user()->id) && ($lv->is_forwarded == 2)) 
                                     @if(\TaskHelper::getUserData($user->second_line_manager)->id == \Auth::user()->id)
                                         <a href="/admin/leave_request_modal/{{ $lv->leave_application_id }}"
                                                            class="btn btn-primary btn-xs" title="Edit"
                                                            data-toggle="modal" data-target="#modal_dialog"><span
                                                class="fa fa-edit"></span></a> 
                                      @endif      
                                     @endif   
                                    @endif 


                                          <!-- {{$lv->leave_application_id}} -->
                                          @if(!empty(\TaskHelper::getUserData($user->second_line_manager)->id) || ($lv->user_id != \Auth::user()->id)) 
                                         @if($lv->is_forwarded !=2)
                                         @if(!\Auth::user()->hasRole(['admins','hr-staff']) )
                                         @if(\Auth::user()->canforward_time_change_request  == 1)
                                         @if($lv->application_status == 1)
                                         @if(\TaskHelper::getUserData($user->first_line_manager)->id != \Auth::user()->id)
                                          <a href="/admin/leave_forward_modal/{{ $lv->leave_application_id }}"
                                                            class="btn btn-primary btn-xs" title="Forward"
                                                            data-toggle="modal" data-target="#modal_dialog"><span
                                            class="fa fa-paper-plane"></span></a>
                                         @elseif((\TaskHelper::getUserData($user->first_line_manager)->id == \Auth::user()->id) && !empty(\TaskHelper::getUserData($user->second_line_manager)->id))  
                                           <a href="/admin/leave_forward_modal/{{ $lv->leave_application_id }}"
                                                            class="btn btn-primary btn-xs" title="Forward"
                                                            data-toggle="modal" data-target="#modal_dialog"><span
                                            class="fa fa-paper-plane"></span></a>  
                                         @endif                
                                         @endif 
                                         @endif 
                                         @endif 
                                         @endif 
                                         @endif 






                                                <a href="/admin/leave_management/detail/{{ $lv->leave_application_id }}" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="top" title="" data-original-title="View"><span class="fa fa-list-alt"></span></a>
                                                 @if($lv->application_status == 2 && !\Auth::user()->hasRole(['admins','hr-staff']))
                                                 <i class="fa fa-trash text-muted" title="Delete"></i>
                                                @elseif(\Auth::user()->hasRole(['admins','hr-staff']))
                                                <a href="/admin/leave_management_delete/{{ $lv->leave_application_id }}" class="btn btn-danger btn-xs" title="" data-toggle="tooltip" data-placement="top" onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" data-original-title="Delete"><i class="fa fa-trash"></i></a>
                                                @else
                                                  @if( $lv->isDeletable() )
                                                  <a href="/admin/leave_management_delete/{{ $lv->leave_application_id }}" class="btn btn-danger btn-xs" title="" data-toggle="tooltip" data-placement="top" onclick="return confirm('You are about to delete a record. This cannot be undone. Are you sure?');" data-original-title="Delete"><i class="fa fa-trash"></i></a>
                                                  @endif
                                                @endif
                                            </td>
                                        </tr>
                                        @endif
                                        @endforeach
                                </tbody>
                            </table>
                    	</div>
                    </div>
                   @if(!\App\Helpers\TaskHelper::is_line_manager(\Auth::user()->id)) 
                    <div style="text-align: center;"> {!! $allleaves->appends(\Request::except('page'))->render() !!} </div>
                   @endif 
                </div>
        </div>
 </div>
<link rel="stylesheet" href="/nepali-date-picker/nepali-date-picker.min.css">
<script type="text/javascript" src="/nepali-date-picker/nepali-date-picker.js"> </script>


 <script type="text/javascript">

     
    
    $(function() {
        $('.startdate').datetimepicker({
          //inline: true,
          format: 'YYYY-MM-DD',
          sideBySide: true,
          allowInputToggle: true,
        });
      });
       $(function() {
        $('.enddate').datetimepicker({
          //inline: true,
          format: 'YYYY-MM-DD',
          sideBySide: true,
          allowInputToggle: true,
        });
      });

 $("#nep-startdate").nepaliDatePicker({
  
});
$("#nep-enddate").nepaliDatePicker({
   
});

  $("#btn-filter-clear").on("click", function () {
      window.location.href = "{!! url('/') !!}/admin/allpendingleaves";
    });



 </script>


@endsection