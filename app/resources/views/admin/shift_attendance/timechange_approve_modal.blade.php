<div class="modal-content">
    <div id="printableArea">
        <div class="modal-header hidden-print">
            <h4 class="modal-title" id="myModalLabel">Time Change Request
                <small>View Time Change request</small>
                <div class="pull-right ">

                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
            </h4>
        </div>
        <div class="modal-body wrap-modal wrap">
            <form method="post" action="{{route('admin.timechange_request.update',$timechange->id) }}">
                {{ csrf_field() }}
            <table class="table table-striped">
                <thead>
                    <tr class="bg-primary">
                        <th colspan="2">Time Change Info</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>Employee Name:</th>
                        <td>{{ $timechange->user->first_name }} {{ $timechange->user->last_name }}</td>
                    </tr>

                    <tr>
                        <th>Attendance Date:</th>
                        <td>{{ date('dS M Y',strtotime($timechange->date)) }}</td>
                    </tr>

                    <tr>
                        <th>Shift Assigned:</th>
                        <td>{{ $timechange->shift->shift_name }}</td>
                    </tr>


                    <tr>
                        <th>Actual Time:</th>
                        <td>{{ date('h:i A',strtotime($timechange->actual_time ))}}</td>
                    </tr>

                    <tr>
                        <th>Request Time:</th>
                      <td>{{ date('h:i A',strtotime($timechange->requested_time ))}}</td>
                    </tr>

                    <tr>
                            <th>Reason</th>
                            <td> @if(!empty($timechange->reason)) {{ $timechange->reason }} @else {{$timechange->reason1}} @endif </td>

                    </tr>
                 @if(\Auth::user()->canapprove_or_deny  == 1 && $timechange->is_forwarded == 2)
                    <tr>
                            <th>Change Forward Note</th>
                            <td> {!! $forwaded->note ?? "Not Available" !!} </td>

                    </tr>
                 @endif   

                    <tr>

                        <th>Status:</th>
                        <td>
                        @if($timechange->status == 1)
                        <span class="label label-warning">Pending</span>
                        @elseif($timechange->status == 2)
                        <span class="label label-success">Accepted</span>
                        @else
                        <span class="label label-danger">Rejected</span>
                        @endif
                        </td>

                    </tr>

                    <tr>

                    {{--    @if($timechange->status == 2 || $timechange->status == 3)

                            <th>Approved By:</th>
                            <td> {{ $timechange->approvedBy->first_name }}   {{ $timechange->approvedBy->last_name }}</td>

                        @else
                          @if((\Auth::user()->hasRole(['admins','hr-staff']))||(\Auth::user()->canapprove_or_deny  == 1 && Auth::user()->id != $timechange->user_id && $timechange->is_forwarded == null ))
                            <th>Action:</th>
                            <td>

                                {!!  Form::select('status',['2'=>'Accept','3'=>'Reject'],$timechange->status,['class'=>'form-control'] ) !!}
                            </td>
                        @elseif(\Auth::user()->canapprove_or_deny  == 1 && $timechange->is_forwarded == 2 && \Auth::user()->id == $forwaded->to_id) --}}
                       
                          <th>Action:</th>
                            <td>

                                {!!  Form::select('status',['2'=>'Accept','3'=>'Reject'],$timechange->status,['class'=>'form-control'] ) !!}
                            </td>
                      {{--  @endif
                        @endif  --}}
                    </tr>

                </tbody>


            </table>


                   <div class="row">
                    <div class="col-md-12">
                        <div class="form-group pull-right">
                            <!-- {{$timechange->id}} -->
                        {{--    @if($timechange->status == 1)
                               @if((\Auth::user()->hasRole(['admins','hr-staff']))||(\Auth::user()->canapprove_or_deny  == 1 && \Auth::user()->id != $timechange->user_id && $timechange->is_forwarded == null ))
                               <input value="Update" type="submit"  class="btn btn-primary">
                             @else
                              @if(\Auth::user()->canapprove_or_deny  == 1 && $timechange->is_forwarded == 2 && \Auth::user()->id == $forwaded->to_id)--}}
                                 <input value="Update" type="submit"  class="btn btn-primary">
                            {{--    @else
                                 <input value="Update" {{'disabled'}} type="submit"  class="btn btn-primary">
                              @endif   
                            @endif
                            @endif --}}
                        <button  type="button"  class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
                        </div>
                        </div>
                 </div>

            </form>
        </div>
    </div>
</div>
