@extends('layouts.master')

@section('head_extra')
    @include('partials._head_extra_select2_css')
    <style>
        .font-bold td {
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            {!! $page_title ?? "Page Title" !!}
            <small>{!! $page_description ?? "Page Description" !!}</small>
        </h1>
        <div class="d-flex align-items-center py-1">
            <div data-bs-toggle="tooltip" data-bs-placement="left" data-bs-trigger="hover" title="">
                <a class="btn btn-primary btn-sm float-right" href="/admin/sales/receipts">
                    <i class="fa fa-times"></i> Close
                </a>
            </div>
        </div>
    </section>

    <div class='card'>
        <div class="card-body pt-6">
            <div class='row'>
                <div class='col-md-12'>
                    {!! Form::open( array('route' => 'admin.orders.enable-selected', 'id' => 'frmClientList') ) !!}
                    <div class="box box-primary">
                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="orders-table">
                                    <thead>
                                    <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                        <th>Client</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($invoices as $key=> $invoice)
                                        <tr class="font-bold">
                                            <td>
                                                @php $client = App\Models\Client::find($key); @endphp
                                                {{$invoice->client->name}}
                                            </td>
                                            <td>
                                                {{-- {{$invoice->sum('total_amount') -  $invoice->sum('invoice_payment_sum_amount')}}--}}
                                                @php $paidAmount = TaskHelper::getTaxInvoicePaidAmount($invoice->id); @endphp
                                                {{$invoice->total_amount -  $paidAmount}}
                                            </td>
                                            <td>
                                                <a href="/admin/payment/invoice/{{$invoice->id}}/create"
                                                   class="btn btn-sm btn-primary">Pay</a>
                                            </td>
                                        </tr>
                                        {{-- @foreach($invoice as  $inv)--}}
                                        {{--     <tr>--}}
                                        {{--         <td>&nbsp;&nbsp;&nbsp;&nbsp;#{{$inv->id}}</td>--}}
                                        {{--         <td>--}}
                                        {{--             &nbsp;&nbsp;&nbsp;&nbsp;{{$inv->total_amount - $inv->invoice_payment_sum_amount}}</td>--}}
                                        {{--         <td>--}}
                                        {{--             <a href="/admin/payment/invoice/{{$inv->id}}/create"--}}
                                        {{--                class="btn btn-sm btn-primary">Pay</a>--}}
                                        {{--         </td>--}}
                                        {{--     </tr>--}}
                                        {{-- @endforeach--}}
                                    @endforeach
                                    @if(isset($payment_list) && !empty($payment_list))
                                        @foreach($payment_list as $o)
                                            <tr>
                                                <td>{!! $o->id !!}</td>
                                                <td>{!! $o->invoice->bill_no??'' !!}</td>
                                                <td><a target="_blank"
                                                       href="/admin/entries/show/{{\FinanceHelper::get_entry_type_label($o->entry->entrytype_id??'')}}/{{$o->entry->id??''}}">{{$o->entry->number??''}}</a>
                                                </td>
                                                <td>{!! $o->invoice->client->name??'' !!}</td>
                                                <td>{!! date('dS M y', strtotime($o->date)) !!}</td>
                                                <td>
                                                    <a href="/admin/invoice/payment/{{$o->id}}">{!! $o->reference_no??'' !!}</a>
                                                </td>
                                                <td class="fw-bolder text-dark">{{env('APP_CURRENCY')}} {{ number_format($o->amount,2) }}</td>
                                                <td>{!! $o->paidby->name??'' !!}</td>
                                                <td>
                                                    @if ( $o->isEditable() || $o->canChangePermissions() )
                                                        @if($o->status == 'paid' && \Request::get('type') == 'invoice')
                                                            <i class="fa fa-edit text-muted" title=""></i>
                                                        @else
                                                            <a href="/admin/payment/orders/{{$o->invoice_id}}/edit/{{$o->id}}"
                                                               title="{{ trans('general.button.edit') }}"><i
                                                                    class="fa fa-edit"></i></a>
                                                        @endif
                                                    @else
                                                        <i class="fa fa-edit text-muted"
                                                           title="{{ trans('admin/orders/general.error.cant-edit-this-document') }}"></i>
                                                    @endif

                                                    @if ( $o->isDeletable() )
                                                        @if($o->status == 'paid' && \Request::get('type') == 'invoice')
                                                            <i class="fa fa-trash-alt text-muted" title=""></i>
                                                        @else
                                                            <a href="{!! route('admin.payment.orders.confirm-delete', $o->id) !!}"
                                                               data-toggle="modal"
                                                               title="{{ trans('general.button.delete') }}"><i
                                                                    class="fa fa-trash-alt deletable"></i></a>
                                                        @endif
                                                    @else
                                                        <i class="fa fa-trash-alt text-muted"
                                                           title="{{ trans('admin/orders/general.error.cant-delete-this-document') }}"></i>
                                                    @endif
                                                    <a href="/admin/invoice1/printreceipt/{{ $o->id}}"
                                                       title="Print Receipt"><i class="fa fa-lg fa-print"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <div id="makePaymentModal" class="modal fade" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">

                            <h4 class="modal-title">Pending & Partial Receipts</h4>
                        </div>
                        <div class="modal-body" style="z-index: 90000">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <select class="form-control searchable" id='purchaseId'>
                                            @foreach($orderTopay ?? [] as $k=>$value)
                                                <option value="{{ $value->id }}">
                                                    Bill#{{$value->bill_no  }} [ {{$value->client->name  }} ]
                                                    Total: {{$value->total_amount - \TaskHelper::getSalesPaymentAmount($value->id) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" id='payNow'>Pay</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('body_bottom')
    <script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>
    <script type="text/javascript">
        $('.searchable').select2({
            width: '100%',
        });

        $('#payNow').click(function(){
            let pid  = $('#purchaseId').val();
            location.href = `/admin/payment/invoice/${pid}/create`;
        });
    </script>
@endsection
