@extends('layouts.master')
@section('content')

<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
            <h1>
                {{ $page_title ?? "Page Title"}}
                <small> {{ $page_description ?? "Page Description" }}</small>
            </h1>
            {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
</section>


<div class="box box-primary">
    <div class='row'>
        <div class='col-md-12'>
           <div class="box">
              <div class="box-body ">
                  <form method="get" action="/admin/customerdeposits/index?id={{ $data['id'] }}" enctype="multipart/form-data">
                      <div class="row">
                            <div class="col-md-2">
                                <label class="control-label">Start Date</label>
                                <input type="date"  name="startdate" placeholder="Start Date" id="date" value="{{ $data['startdate'] }}" class="form-control ">
                                <input type="hidden"  name="id" value="{{ $data['id'] }}" class="form-control ">
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">End date</label>
                                <input type="date"  name="enddate" placeholder="End Date" id="date" value="{{ $data['enddate']}}" class="form-control ">
                            </div>
                            <div class="col-md-2">
                                <label class="control-label">Type</label>
                                <select name="type" id="" class="form-control ">
                                <option value="">Select Type</option>
                                <option value="Deposit" @if($data['type'] =="Deposit") selected @endif>Deposit</option>
                                <option value="Deduct" @if($data['type'] =="Deduct") selected @endif >Deduct</option>
                                </select>      
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::submit( "Filter", ['class' => 'btn btn-primary'] ) !!}
                            <a href="/admin/customerdeposits/index?id={{ $data['id'] }}" class='btn btn-default'>Reset</a>
                        </div>
                </div>
             </div>
        </form>
     </div>
 </div>
       <table class="table table-hover table-no-border" id="customerdeposits-table">
        <div class="pull-right">
            <a href="/admin/customerdeposits/create?id={{ $data['id'] }}" class="btn btn-success">Create</a>

        </div>
		<thead>
		    <tr>
		        <th>ID</th>
		        <th>Date</th>
		        <th>Reference No</th>
		        <th>Type</th>
		        <th>Amount</th>
		        <th>Closing</th>
		        {{-- <th>Action</th> --}}
		    </tr>
		</thead>
		<tbody>
		    @foreach($customerdeposits as $key=>$value)
		    <tr>
		        <td>{{ $value->id }}</td>
		        <td>{{ $value->date }}</td>
		        <td>{{$value->reference_no}}</td>
		        <td>{{$value->type}}</td>
		        <td>{{$value->amount}}</td>
		        <td>{{$value->closing}}</td>
		        {{-- <td>
		            <a href="{{route('admin.customerdeposits.edit', $value->id)}}"><i class="fa fa-edit"></i></a>
		            <a href="{!! route('admin.customerdeposits.delete', $value->id) !!}" onclick="return confirm('Are you sure you want to delete this item?');"><i class="fa fa-trash-o deletable"></i></a>

		        </td> --}}
		    </tr>

		    @endforeach

      </tbody>
   </table>
   {{$customerdeposits->appends($_GET)->links()}}
 </div>
</div>


@endsection
