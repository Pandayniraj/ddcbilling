@extends('layouts.master')

@section('head_extra')
    @include('partials._head_extra_select2_css')
@endsection

@section('content')
    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            Stock List
            <small>{!! $page_description ?? "Page description" !!}</small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
    </section>

    <div class='row'>
        <a href="/admin/addstock" class="btn btn-default"><i class="fa fa-plus"></i>Add Stock</a>
        <div class='col-md-12'>
            <div class="box box-header">
                <div class="content">

                    <table class="table table-bordered">
                        <tr>
                            <th>S.N</th>
                            <th>Date</th>
                            <th>Organization</th>
                            <th>Outlet</th>
                            <th>User</th>
                            <th>Action</th>
                        </tr>
                        <tbody>
                        @foreach ($stock_lists as $key=>$stock)
                            <tr>
                                <td>{{$loop->index+1}}</td>
                                <td>{{ TaskHelper::getNepaliDate($stock->date) }}</td>
                                <td>{{$stock->org->organization_name}}</td>
                                <td>{{$stock->location->name}}</td>
                                <td>{{$stock->users->first_name}} {{$stock->users->last_name}}</td>
                                <td>
                                    <a href="/admin/stock/details?stock_id={{$stock->id}}" class="btn btn-default"><i
                                            class="fa fa-eye"></i></a>
                                    @if (\Auth::user()->hasRole('admins'))
                                    <a href="/admin/stock/edit?stock_id={{$stock->id}}" class="btn btn-primary" title="Edit Stock"><i
                                            class="fa fa-edit"></i></a>
                                    <a href="/admin/stock/destory?stock_id={{$stock->id}}" class="btn btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this item?');"><i
                                            class="fa fa-trash"></i></a>
                                    @endif
                                    <a href="{{route('admin.stock.return', $stock->id)}}" class="btn btn-success" title="Return Stock"><i
                                            class="fa fa-repeat"></i></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

