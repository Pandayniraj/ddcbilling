@extends('layouts.master')
@section('content')


<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
    <h1>
        {{ $pagetitle }}
        <small>{{ $pagedescription }}</small>
    </h1>
    Current Fiscal Year: <strong>{{ FinanceHelper::cur_fisc_yr()->fiscal_year}}</strong>
    {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}

</section>
<style type="text/css">
      @media only screen and (max-width: 770px) {

        .hide_on_tablet{
            display: none;
        }
    }
    .nep-date-toggle{
        width: 120px !important;
    }

        img.p-image {
      width: 27px;
      height: 27px;
      object-fit: cover;
      object-position: bottom;
      border-radius: 50%;
  }
  .table-bordered>thead>tr>th, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .table-bordered>tfoot>tr>td {
    border: none !important;
}
.form-control {
    border-radius: 6px !important;
    box-shadow: none;
    border-color: #aaa;
    height: 28px;
}
.box {
    box-shadow: rgba(17, 17, 26, 0.1) 0px 4px 16px, rgba(17, 17, 26, 0.1) 0px 8px 24px, rgba(17, 17, 26, 0.1) 0px 16px 56px !important;
    border-top: none;
    border-radius: 12px;
    }
</style>
<link href="/bower_components/admin-lte/select2/css/select2.min.css" rel="stylesheet" />
<script src="/bower_components/admin-lte/select2/js/select2.min.js"></script>
<div class='row'>

    <div class='col-md-12'>

        <!-- Box -->

        <div class="box">
          
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped" id="orders-table">
                        <thead>
                            <tr class="bg-info">
                                <th> Sn </th>
                                <th>Api Link</th>
                                <th>IRD User-Name</th>
                                <th>IRD Password</th>
                                <th>Seller Pan</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $irddetail->id }}</td>
                                <td>{{ $irddetail->api_link }}</td>
                                <td>{{ $irddetail->ird_username }}</td>
                                <td>{{ $irddetail->ird_password }}</td>
                                <td>{{ $irddetail->seller_pan }}</td>
                                <td><a href="/admin/irddetail/edit/{{$irddetail->id}}" target="_blank" title="edit"><i class="fa fa-edit"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>


                </div> <!-- table-responsive -->

            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div><!-- /.col -->

</div><!-- /.row -->
@endsection


<!-- Optional bottom section for modals etc... -->
@section('body_bottom')
<link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.min.js") }}"></script>
    <script src="{{ asset ("/bower_components/admin-lte/plugins/daterangepicker/moment.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.js") }}"></script>
@include('partials._date-toggle')
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


     $(function() {
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
