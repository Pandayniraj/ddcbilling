@extends('layouts.master')
@section('content')

<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
    <h1>
        {{ $page_title }}
        <small>View Chalani</small>
    </h1>



    {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
</section>

<div class="box box-primary">
    <div class="box-header with-border">
        <div class='row'>
            <div class='col-md-12'>

                <div style="display: inline;">
                    <a class="btn btn-primary btn-sm" title="Create New Ticket" href="{{ route('admin.deliveryroute.create') }}">
                        <i class="fa fa-plus"></i>&nbsp;<strong>
                        {{ trans('admin/ticket/general.button.create') }}</strong>
                    </a>
                </div>

                <div class="box-tools pull-right">
                           <input type="text" class="input-sm" placeholder="{{ trans('admin/ticket/general.placeholder.search') }}" id='search-term'>
                           <button class="btn btn-primary btn-sm" id='search' type="button"><i class="fa fa-search"></i>&nbsp;{{ trans('admin/ticket/general.button.search') }}</button>
                           <button class="btn btn-danger btn-sm"  id='clear'><i class="fa fa-close"></i>&nbsp;{{ trans('admin/ticket/general.button.clear') }}</button>
                        </div>

            </div>
        </div>





        <div class="tab-responsive">
       		<table class="table table-striped">

                <thead>
                    <tr>

                        <th>Id</th>
                        <th>Distributor</th>
                        <th>Route Name</th>
                        <th>Route Code</th>
                        <th>Action</th>
                    </tr>

                </thead>

                <tbody>
                @foreach($deliveryroutes as $key => $deliveryroute)
                    <tr>

                        <td>{{$deliveryroute->id}} </td>
                        <td>{{ $deliveryroute->distributor_id }} </td>
                        <td>{{ $deliveryroute->route_name }}</td>
                        <td>{{ $deliveryroute->route_code }}
                        <td>
                                    <a href="{!! route('admin.deliveryroute.edit', $deliveryroute->id) !!}" title="{{ trans('general.button.edit') }}"><i class="fa fa-edit"></i></a>
                            
                                    <a href="{!! route('admin.deliveryroute.confirm-delete', $deliveryroute->id) !!}" data-toggle="modal" data-target="#modal_dialog" title="{{ trans('general.button.delete') }}"><i class="fa fa-trash deletable"></i></a>
                        </td>

                    </tr>
                @endforeach
            </tbody>

            </table>
            <div align="center"> {!! $deliveryroutes->render() !!} </div>
        </div>


    </div>
</div>

<script type="text/javascript">


      $('#search').click(function(){
            let val =  $('#search-term').val();
            window.location.href = `{{ url('/') }}/admin/deliveryroute?term=${val}`;
        });
        $('#clear').click(function(){
            window.location.href = `{{ url('/') }}/admin/deliveryroute`;
        });
</script>
@endsection
