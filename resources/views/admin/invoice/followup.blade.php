@extends('layouts.master')

@section('head_extra')
    <link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet"
          type="text/css"/>
    @include('partials._head_extra_select2_css')

    <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet"
          type="text/css"/>

    <style>
        img.p-image {
            width: 50px;
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
                <span style="" class="erp5nepalidate float-start"></span>

                <a class="btn btn-primary btn-sm float-right" href="/admin/sales/receipts">
                    <i class="fa fa-plus"></i> Receipts
                </a>
            </div>
        </div>
    </section>

    <div class='card'>
        <div class="card-body pt-6">
            <div class="box">
                <div class="box-header"></div>
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="kt_datatable_fixed_columns" class="table table-row-bordered gy-5 gs-7">
                            <thead>
                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                <th> Num</th>
                                <th>Bill date</th>
                                <th>Officer</th>
                                <th title="Bill No">#</th>
                                <th>Customer name</th>
                                <th>Followup Date</th>
                                <th>Total</th>
                                <th>Outlet</th>
                                <th>Status</th>
                                <th>Tools</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($orders) && !empty($orders))
                                @foreach($orders as $key=> $o)
                                    @php
                                        $paidAmount = TaskHelper::getTaxInvoicePaidAmount($o->id);
                                        if( $paidAmount >= $o->total_amount ){
                                            $paystatus = 'Paid';
                                        }elseif($paidAmount > 0){
                                            $paystatus = 'Partial';
                                        }else{
                                            $paystatus = 'Pending';
                                        }
                                    @endphp

                                    @if(!Request::get('pay_status') || Request::get('pay_status') == $paystatus )
                                        <tr>
                                            <td>
                                                <a target="_blank"
                                                   href="/admin/entries/show/{{\FinanceHelper::get_entry_type_label($o->entry->entrytype_id??'')}}/{{$o->entry->id??''}}">{{$o->entry->number??''}}</a>
                                            </td>

                                            <td title="{{ date('dS M y',strtotime($o->bill_date))}}">{{ TaskHelper::getNepaliDate($o->bill_date) }}</td>
                                            <td title="{{ucfirst($o->user->username)}}" style="padding-left: 8px;"><span
                                                    class="symbol symbol-50px symbol-circle"> <img
                                                        src="/images/profiles/{{$o->user->image ? $o->user->image : 'default.png'}}"
                                                        class="img-circle img-fluid" style="width: 27px;height: 27px;"
                                                        alt="User Image"
                                                        onerror="this.src='/images/profiles/default.png';">   </span>
                                            </td>
                                            <td>{{env('SALES_BILL_PREFIX')}}{{ $o->bill_no }}</td>
                                            <td class="symbol symbol-25px">
                                                @if($o->client->image)
                                                    <img src="{{asset($o->client->image)}}" class="p-image" id="blah"
                                                         src="#" alt="your image"/>
                                                @else
                                                    <img src="/images/profiles/default.png" class="p-image" id="blah"
                                                         src="#" alt="your image"/>
                                                @endif

                                                <span> <a class="fw-bolder text-info" href="/admin/invoice1/{{$o->id}}"
                                                          style="color: #1a2226c9;"> {{ $o->client->name }}</a> <small>{{ $o->name }}</small> </span>
                                            </td>
                                            <td>
                                                <a class="fw-bolder text-info" href="#" data-bs-toggle="modal" data-bs-target="#makePaymentModal_{{$key}}">
                                                    {{ date('d/m/y',strtotime($o->followupInvoice->followup_date??$o->due_date))}}
                                                </a>
                                                <div id="makePaymentModal_{{$key}}" class="modal fade" role="dialog">
                                                    <div class="modal-dialog">
                                                        <!-- Modal content-->
                                                        <div class="modal-content">
                                                            <form method="post" action="/admin/invoice/changeduedate/{{$o->id}}">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h4 class="modal-title">Change Followup Date</h4>
                                                                </div>
                                                                <div class="modal-body" style="z-index: 90000">
                                                                    <div class="row">
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <input name="date" type="date" class="form-control" value="{{$o->followupInvoice->followup_date??$o->due_date}}">
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" class="btn btn-primary">Change</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{!! number_format($o->total_amount,2) !!}</td>
                                            <td> {{ $o->outlet->outlet_code  }} </td>
                                            @if( $paidAmount >= $o->total_amount )
                                                <td><span class="badge badge-success">Paid</span></td>
                                            @elseif($paidAmount > 0)
                                                <td><span class="badge badge-info">Partial</span></td>
                                            @else
                                                <td><span class="badge badge-warning">Unpaid</span></td>
                                            @endif

                                            <td class="text-end">
                                                {{-- @if( $o->isEditable())--}}
                                                {{--     <a href="{{ route('admin.invoice.edit',$o->id) }}"--}}
                                                {{--        title="{{ trans('general.button.edit') }}" class="menu-link px-3">--}}
                                                {{--         <i class="fa fa-edit text-muted" title="Edit"></i>--}}
                                                {{--     </a>--}}
                                                {{-- @endif--}}
                                                <a href="/admin/invoice/print/{{$o->id}}" class="menu-link px-3">
                                                    <i class="fa fa-print" title="Print" style="font-size: 18px;"></i>
                                                </a>
                                                <a href="/admin/invoice/payment/{{$o->id}}" class="menu-link px-3">
                                                    <i class="fa fa-money" title="Receive" style="font-size: 18px;"></i>
                                                </a>

                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>


                        <div class="d-flex">
                            {!! $orders->links('pagination::bootstrap-4') !!}
                        </div>

                    </div> <!-- table-responsive -->

                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->

    </div>
@endsection

@section('body_bottom')
    <script src="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
    <script src="{{ asset ("/bower_components/admin-lte/plugins/daterangepicker/moment.js") }}"
            type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}"
            type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.js") }}"></script>
    @include('partials._date-toggle')
    <script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>

    <script language="JavaScript">
        function toggleCheckbox() {
            checkboxes = document.getElementsByName('chkClient[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = !checkboxes[i].checked;
            }
        }
        $('.date-toggle-nep-eng1').nepalidatetoggle();
    </script>

    <script>
        $(function () {
            $('#start_date').datepicker({
                //format: 'YYYY-MM-DD',
                dateFormat: 'yy-m-d',
                sideBySide: true,
            });
            $('#end_date').datepicker({
                //format: 'YYYY-MM-DD',
                dateFormat: 'yy-m-d',
                sideBySide: true,
            });
        });
    </script>

    <script type="text/javascript">
        $('.searchable').select2();
    </script>
@endsection
