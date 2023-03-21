@extends('layouts.master')

@section('head_extra')
    @include('partials._head_extra_select2_css')
    <link href="{{ asset('/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css') }}" rel="stylesheet"
        type="text/css" />
@endsection

@section('content')
    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            {!! $page_title !!}
            <small>{!! $page_description !!}</small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
    </section>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box">
                <div class="box-body">
                    <form action="{{ route('admin.payment.multiple-invoice.create') }}" method="post"
                        enctype="multipart/form-data" id="paymentForm">
                        {{ csrf_field() }}
                        @foreach ($purchase_orders as $key => $purchase_order)
                            <input type="hidden" name="orders[]" value="{{ $purchase_order->id }}">
                            <input type="hidden" name="reference_no[]" value="{{ date('Ymds') + $key . 'm' }}">
                        @endforeach
                        <div class="content col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Payment Date</label>
                                        <div class="input-group col-md-9">
                                            <input type="text" name="date" id="target_date"
                                                value="{{ \Carbon\Carbon::now()->toDateString() }}" placeholder="Date"
                                                class="form-control datepicker" required="required">
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-calendar"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Amount</label>
                                        <div class="input-group col-md-9">
                                            <input type="text" name="amount" placeholder="Amount" id="price_value"
                                                value="{{ $payment_remain }}" class="form-control" required readonly>
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-credit-card"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3">
                                            Received In
                                            <input type="hidden" name="payment_method" value="Cash">
                                        </label>
                                        <div class="col-md-9" style="padding: 0">
                                            <select class='form-control searchable select2' name='paid_by'>
                                                @foreach ($groups as $grp)
                                                    <option value="{{ $grp->id }}"
                                                        {{ $grp->id == 4 ? 'selected' : '' }}> {{ $grp->name }}
                                                    </option>;
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="inputEmail3" class="control-label col-md-3">
                                        Note
                                    </label>
                                    <div class="col-md-9 input-group">
                                        <input type="text" name="note" placeholder="Write Note" id="description"
                                            value="{!! \Request::old('note') !!}" class="form-control">
                                        <div class="input-group-addon">
                                            <a href="#"><i class="fa fa-file-text"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button @if ($payment_remain > 0) type="Submit" @else type="button" @endif
                                    class="btn btn-success btn-submit" id="make-payment">
                                    @if ($payment_remain > 0)
                                        Make Payment
                                    @else
                                        Already Received
                                    @endif
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
    <script>
        const submitBtn = document.getElementsByClassName('btn-submit')[0];
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm("Are you sure you want to make this payment!")) {
                document.getElementById('paymentForm').submit();
            }
        });
    </script>
    <script src="{{ asset('/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js') }}"
        type="text/javascript"></script>

    <script type="text/javascript">
        $(function() {
            $('.datepicker').datetimepicker({
                //inline: true,
                format: 'YYYY-MM-DD',
                sideBySide: true,
                allowInputToggle: true
            });
        });

        $(document).ready(function() {
            if ($("#price_value").val() == 0) {
                $("#make-payment").html('');
                $("#make-payment").html('Already Received');
                $("#make-payment").attr('type', 'button');
            }
        })
    </script>
@endsection
