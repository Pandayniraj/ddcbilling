@extends('layouts.master')

@section('head_extra')
    @include('partials._head_extra_select2_css')
    <link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet"
          type="text/css"/>
@endsection

@section('content')
    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            {!! $page_title !!}
            <small>{!! $page_description !!}</small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
    </section>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box">
                <div class="box-body">
                    <form action="{{route('admin.payment.invoice.create',$invoice_id)}}" method="post"
                          enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="content col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-sm-6">Payment Date</label>
                                        <div class="input-group ">
                                            <input type="text" name="date" id="target_date"
                                                   value="{{\Carbon\Carbon::now()->toDateString()}}" placeholder="Date"
                                                   class="form-control datepicker" required="required">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-sm-4">Reference No</label>
                                        <div class="input-group ">
                                            <input type="text" name="reference_no" placeholder="Reference No" id=""
                                                   value="{{ old('company_id') ?? date('Ymds')  }}" class="form-control"
                                                   required>
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-building"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="invoice_id" value="{{$invoice_id}}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-sm-6">Amount</label>
                                        <div class="input-group ">
                                            <input type="text" name="amount" placeholder="Amount" id="price_value"
                                                   value="{{ $payment_remain }}" class="form-control"
                                                   required readonly>
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-credit-card"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-sm-4">
                                            Received In
                                            <input type="hidden" name="payment_method" value="Cash">
                                            {{-- <select name="payment_type" class="payment-type form-control">--}}
                                            {{--     <option value="Cash" @if($purchase_order->bill_type == 'Cash') selected @endif>Cash</option>--}}
                                            {{--     <option value="Credit" @if($purchase_order->bill_type == 'Credit') selected @endif>Credit</option>--}}
                                            {{-- </select>--}}
                                            {{-- <input type="radio" id="cash" name="payment_type" class="payment-type" value="Cash" @if($purchase_order->bill_type == 'Cash') checked @endif>--}}
                                            {{-- <label for="cash">Cash</label><br>--}}
                                            {{-- <input type="radio" id="credit" name="payment_type" class="payment-type" value="Credit" @if($purchase_order->bill_type == 'Credit') checked @endif>--}}
                                            {{-- <label for="credit">Credit</label><br>--}}
                                        </label>
                                        <div class="col-md-8" style="padding: 0">
                                            {{-- <div class="col-md-8" @if($purchase_order->bill_type == 'Credit') style="display: none;" @endif id="cash-ledger">--}}
                                                 <select class='form-control searchable select2' name='payment_method'>
                                                     <?php $groups = \App\Models\COALedgers::orderBy('code', 'asc')->where('group_id', '13')->get();
                                                     foreach ($groups as $grp) {
                                                         // echo '<option value="' . $grp->id . '"' . ((isset($purchase_order) && ($grp->name == $purchase_order->type)) ? 'selected' : "") .'>'. $grp->name . '</option>';
                                                         echo '<option value="' . $grp->id . '"' . (($grp->id == 4) ? 'selected' : "") .'>'. $grp->name . '</option>';
                                                     }
                                                     ?>
                                                 </select>
                                            {{-- </div>--}}
                                            {{-- <select class='form-control searchable select2' name='payment_method'>--}}
                                            {{--     <?php $groups = \App\Models\COALedgers::orderBy('code', 'asc')->where('group_id', '219')->where('org_id', \Auth::user()->org_id)->get();--}}
                                            {{--     foreach ($groups as $grp) {--}}
                                            {{--         echo '<option value="' . $grp->id . '"' . ((isset($purchase_order) && ($grp->id == $purchase_order->client->ledger_id)) ? 'selected' : "disabled") .'>'. $grp->name . '</option>';--}}
                                            {{--     }--}}
                                            {{--     ?>--}}
                                            {{-- </select>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: none;">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-sm-4">Attachment</label>
                                        <div class="input-group ">
                                            <input type="file" name="attachment">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-sm-4">TDS</label>
                                        <div class="input-group ">
                                            <input type="number" step="any" name="attachment">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <label for="inputEmail3" class="control-label">
                                        Note
                                    </label>
                                    <textarea class="form-control" name="note" id="description" rows="1"
                                              placeholder="Write Note">{!! \Request::old('note') !!}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button @if($payment_remain>0) type="Submit" @else type="button" @endif class="btn btn-success" id="make-payment">
                                    @if($payment_remain>0) Make Payment @else Already Received @endif
                                </button>
                                <a href="{!! route('admin.invoice.index') !!}"
                                   class='btn btn-default'>{{ trans('general.button.cancel') }} / Credited</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('body_bottom')
    <script src="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
    <script src="{{ asset ("/bower_components/admin-lte/plugins/daterangepicker/moment.js") }}"
            type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}"
            type="text/javascript"></script>

    <script type="text/javascript">
        $(function () {
            $('.datepicker').datetimepicker({
                //inline: true,
                format: 'YYYY-MM-DD',
                sideBySide: true,
                allowInputToggle: true
            });
        });
    </script>

    <script>
        $(document).on('click', '.payment-type', function () {
            var v = $(this).val();
            if(v == 'Credit') {
                $('#credit-ledger').css('display', 'block');
                $('#cash-ledger').css('display', 'none');
            } else {
                $('#credit-ledger').css('display', 'none');
                $('#cash-ledger').css('display', 'block');
            }
        })

        $(document).ready(function () {
           if($("#price_value").val() == 0) {
               $("#make-payment").html('');
               $("#make-payment").html('Already Received');
               $("#make-payment").attr('type', 'button');
           }
        })
    </script>

@endsection

