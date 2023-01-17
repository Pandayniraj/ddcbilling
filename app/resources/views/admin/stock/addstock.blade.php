@extends('layouts.master')

@section('head_extra')
    @include('partials._head_extra_select2_css')
@endsection

@section('content')

<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
            <h1>
                Add Stock
                <small>{!! $page_description ?? "Page description" !!}</small>
            </h1>
            {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
        </section>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box box-header">
                <div class="content">

                {!! Form::open( ['route'=>'admin.stock.storestock', 'class' => 'form-horizontal', 'id' => 'form_edit_client','files'=>'true'] ) !!}

                <div class="col-sm-6">
                    <label class="control-label">Outlets</label>
                      {!! Form::select('store_id',[''=>'Select Outlets']+$stores, $current_store ?? null, ['class'=>'form-control']) !!}
                  </div>
                  <div class="col-sm-6 ">
                    <label class="control-label"> Recevice From Store Date</label>
                    <input type="date" name="date" class="form-control datepicker date-toggle-nep-eng" value="{{$date}}">
                  </div>

                <table class="table table-bordered">
                    <tr>
                        <th>S.N</th>
                        <th>Product Name</th>
                        <th >Quantity</th>
                        <th >Store Return</th>
                        <th>Remarks</th>
                    </tr>
                    <tbody>
                        @foreach ($products as $key=>$product)
                        <tr>
                            <td>{{$loop->index+1}}</td>
                            <td>{{$product}}</td>
                            <td><input type="number" id="quantity" name="quantity[{{$key}}]" value="0"></td>
                            <td><input type="number" id="return_quantity" name="return_quantity[{{$key}}]" readonly></td>
                            <td><input type="text" id="remarks" name="remarks[{{$key}}]"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>


                <div class="form-group">
                    {!! Form::button( trans('general.button.create'), ['class' => 'btn btn-primary', 'id' => 'btn-submit-edit'] ) !!}

                    <a href="/admin/addstock/list" class="btn btn-default"> Cancel</a>
                </div>


                {!! Form::close() !!}
            </div>

            </div><!-- /.box-body -->
        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection

@section('body_bottom')
    <!-- form submit -->
    @include('partials._date-toggle')
    @include('partials._body_bottom_submit_client_edit_form_js')
    <script>
        $(function() {
            $('.date-toggle-nep-eng').nepalidatetoggle();
            $('.datepicker').datetimepicker({
          //inline: true,
            format: 'YYYY-MM-DD',
            sideBySide: true,
            allowInputToggle: true
        });

      });
    </script>
@endsection
