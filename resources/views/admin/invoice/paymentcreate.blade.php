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
            <a class="btn btn-info btn-xs" href="javascript:void(0);" onclick="printBill()" title="In case No Pop Pop Occur"> <i class="fa fa-print"></i> Print</a>
            <small>{!! $page_description !!}</small>
        </h1>
        <div class="float-end">
            Print
        </div>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
    </section>

    <div class='row'>
        <div class='col-md-12'>
            <div class="box">
                <div class="box-body">
                    <form action="{{ route('admin.payment.invoice.create', $invoice_id) }}" method="post"
                        enctype="multipart/form-data" id="paymentForm">
                        {{ csrf_field() }}
                        <div class="content col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-sm-6">Payment Date</label>
                                        <div class="input-group ">
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
                                        <label class="control-label col-sm-4">Reference No</label>
                                        <div class="input-group ">
                                            <input type="text" name="reference_no" placeholder="Reference No"
                                                id="" value="{{ old('company_id') ?? date('Ymds') }}"
                                                class="form-control" required>
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-building"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="invoice_id" value="{{ $invoice_id }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-sm-6">Amount</label>
                                        <div class="input-group ">
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
                                        <label class="control-label col-sm-4">
                                            Received In
                                            <input type="hidden" name="payment_method" value="Cash">
                                        </label>
                                        <div class="col-md-8" style="padding: 0">
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

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-sm-6">Deposit Amount</label>
                                        <div class="input-group ">
                                            <input type="text"
                                                value="{{ $deositClosing + $payment_remain }}" class="form-control" required readonly>
                                            <div class="input-group-addon">
                                                <a href="#"><i class="fa fa-credit-card"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- @if(($deositClosing + $payment_remain) >= $payment_remain)
                                <div class="col-md-6">
                                    <div class="">
                                        <input type="checkbox" name="from_deposit" id="from_deposit">
                                        <label class="control-label cursor-pointer" for="from_deposit">Make Payment from deposit</label>
                                    </div>
                                </div>
                                @endif --}}
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
                                    <textarea class="form-control" name="note" id="description" rows="1" placeholder="Write Note">{!! \Request::old('note') !!}</textarea>
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

    <script>
        @if(session()->get( 'printbill' ))
        printBill();
        @endif

        function printBill(){
            window.open( "{{ route('admin.invoice.print', $invoice_id) }}",'_blank','toolbar=yes');
        }
    </script>
@endsection
