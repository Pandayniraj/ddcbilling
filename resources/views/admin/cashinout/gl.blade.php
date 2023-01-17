@extends('layouts.master')
@section('content')
    <link href="{{ asset('/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            {!! $page_title !!}
            <small>{!! $page_description ?? 'DayBook' !!}</small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}


    </section>
    <style type="text/css">
        .total {
            font-size: 16.5px;
        }
    </style>

    <div class='row'>
        <div class='col-md-12'>
            <!-- Box -->

            <div class="box box-primary">

                <div class="box-body">
                    <span id="index_lead_ajax_status"></span>


                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="orders-table">
                            <thead>
                                <tr class="bg-gray">
                                    <th>S.N</th>
                                    <th style="font-size: 16.5px">Legder</th>
                                    <th>Opening Balance</th>
                                    <th>Closing Balance</th>
                                    <th>Action</th>


                                </tr>
                            </thead>
                            <tbody>

                                @foreach($ledgers as $k=> $v)
                                    <tr>
                                        <td> {{ $v->id}}</td>
                                        <td> <a href="/admin/accounts/reports/ledger_statement?ledger_id={{$v->id}}">{{ $v->name}}</a> </td>
                                        <td> {{ $v->op_balance}}</td>
                                        <td> {{ $v->op_balance}}</td>
                                        <td> <a href="{{route('admin.chartofaccounts.edit.ledgers', $v->id)}}">Edit</a></td>
                                    </tr>
                                @endforeach

                            </tbody>

                        </table>
                        <div style="text-align: center;">{!! $ledgers->appends(\Request::except('page'))->render() !!}   </div>
                    </div> <!-- table-responsive -->
                </div><!-- /.box-body -->

            </div><!-- /.box -->


        </div><!-- /.col -->

    </div><!-- /.row -->
@endsection


<!-- Optional bottom section for modals etc... -->
@section('body_bottom')
    <!-- DataTables -->


    <script src="{{ asset('/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.js') }}"></script>

    <script language="JavaScript">
        function toggleCheckbox() {
            checkboxes = document.getElementsByName('chkClient[]');
            for (var i = 0, n = checkboxes.length; i < n; i++) {
                checkboxes[i].checked = !checkboxes[i].checked;
            }
        }
        $('.date-toggle').nepalidatetoggle();
    </script>


    <script type="text/javascript">
        $(document).on('change', '#order_status', function() {

            var id = $(this).closest('tr').find('.index_sale_id').val();

            var purchase_status = $(this).val();
            $.post("/admin/ajax_order_status", {
                id: id,
                purchase_status: purchase_status,
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(data, status) {
                if (data.status == '1')
                    $("#index_lead_ajax_status").after(
                        "<span style='color:green;' id='index_status_update'>Status is successfully updated.</span>"
                        );
                else
                    $("#index_lead_ajax_status").after(
                        "<span style='color:red;' id='index_status_update'>Problem in updating status; Please try again.</span>"
                        );

                $('#index_status_update').delay(3000).fadeOut('slow');
                //alert("Data: " + data + "\nStatus: " + status);
            });

        });
    </script>
    <script type="text/javascript">
        $("#btn-submit-filter").on("click", function() {

            status = $("#filter-status").val();
            type = $("#order_type").val();

            window.location.href = "{!! url('/') !!}/admin/orders?status=" + "&type=" + type;
        });

        $("#btn-filter-clear").on("click", function() {

            type = $("#order_type").val();
            window.location.href = "{!! url('/') !!}/admin/edm/order";
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('.customer_id').select2();
        });

        $('.datepicker').datetimepicker({

            format: 'YYYY-MM-DD',
        })
    </script>
@endsection
