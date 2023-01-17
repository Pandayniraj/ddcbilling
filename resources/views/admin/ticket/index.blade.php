@extends('layouts.master')
@section('content')

<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
    <h1>
        {{ $page_title }}
        <small>View Tickets</small>
    </h1>

    <p> Support tickets from portal or generated</p>


    {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
</section>

<div class="box box-default">
    <div class="box-header with-border">
        <div class='row'>
            <div class='col-md-12'>
                 <div class="box-tools pull-right">
                           <input type="text" class="input-sm" placeholder="{{ trans('admin/ticket/general.placeholder.search') }}" id='search-term'>
                           <button class="btn btn-primary btn-sm" id='search' type="button"><i class="fa fa-search"></i>&nbsp;{{ trans('admin/ticket/general.button.search') }}</button>
                           <button class="btn btn-default btn-sm"  id='clear'><i class="fa fa-close"></i>&nbsp;{{ trans('admin/ticket/general.button.clear') }}</button>
                        </div>

                <div style="display: inline;">
                    <a class="btn btn-primary btn-sm" title="Create New Ticket" href="{{ route('admin.ticket.create') }}">
                        <i class="fa fa-plus"></i>&nbsp;<strong> 
                        {{ trans('admin/ticket/general.button.create') }}</strong>
                    </a>
                </div>
            </div>
        </div>
<br/>




            <div class="tab-responsive">
                <table class="table table-striped table-responsive table-hover">
                 <!--    <thead>
                        <tr class="bg-gray">
                            
                            
                            <th>{{ trans('admin/ticket/general.columns.subject') }}</th>
                            
                            <th>{{ trans('admin/ticket/general.columns.status') }}</th>
                            <th>{{ trans('admin/ticket/general.columns.actions') }}</th>
                        </tr>
                    </thead> -->
                    <tbody>
                        
                        @foreach($tickets as $key=>$ticket)
                            <tr @if($ticket->form_source == 'external') class="bg-info" @endif>
                                
                                
                               
                                
                                <td> <a style="font-size: 21.5px;color: black" href="{{ route('admin.ticket.show',$ticket->id) }}" > {{ $ticket->issue_summary }} </a> <span class="text-muted">#{{  $ticket->ticket_number }}</span>
                                    
                                    <br/>
                                    From: {{ $ticket->from_user }} at <span class="material-icons">calendar_month</span> {{  date('Y/m/d h:i A',strtotime($ticket->created_at)) }}

                                    <br/>
                                    <span class="pull-left">
                                        <img src="/images/profiles/{{$ticket->user->image ? $ticket->user->image:'logo.png'}}" class="img-circle img-bordered-sm" style="width: 33px;height: 33px;" alt="User Image">
                                        Addressing by: {{$ticket->user->fullname}}

                                         <?php 
                                        if($ticket->due_date != '0000-00-00'){
                                        echo '<span style="margin-top: 5px; " class="material-icons">alarm</span>  Complete by ';
                                        echo Carbon\Carbon::parse($ticket->due_date)->diffForHumans();
                                        }
                                        ?>
                                    </span>

                                </td>

                                
                                <td> 
                                    @if($ticket->ticket_status == 1)
                                     <label class="label label-primary">Open</label>
                                    @elseif($ticket->ticket_status == 2)
                                     <label class="label label-success">Resolved</label>
                                    @else
                                     <label class="label label-danger">Closed</label>
                                    @endif
                                   
                                </td>

                                <td>
                                @if ( $ticket->isEditable()  )
                                    <a href="{!! route('admin.ticket.edit', $ticket->id) !!}" title="{{ trans('general.button.edit') }}"><i class="fa fa-edit"></i></a>
                                @else
                                    <i class="fa fa-edit text-muted" title="{{ trans('admin/cases/general.error.cant-edit-this-document') }}"></i>
                                @endif
                                @if ( $ticket->isDeletable() )
                                    <a href="{!! route('admin.ticket.confirm-delete', $ticket->id) !!}" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('general.button.delete') }}"><i class="fa fa-trash deletable"></i></a>
                                @else
                                    <i class="fa fa-trash text-muted" title="{{ trans('admin/cases/general.error.cant-delete-this-document') }}"></i>
                                @endif

                                </td>
                            </tr>

                        @endforeach
                    </tbody>
                </table>
            


<div style="text-align: center;"> {!! $tickets->appends(\Request::except('page'))->render() !!} </div>
        </div>


    </div>
</div>

<script type="text/javascript">
    

      $('#search').click(function(){
            let val =  $('#search-term').val();
            window.location.href = `{{ url('/') }}/admin/ticket?term=${val}`;
        });
        $('#clear').click(function(){
            window.location.href = `{{ url('/') }}/admin/ticket`;
        })
</script>
@endsection