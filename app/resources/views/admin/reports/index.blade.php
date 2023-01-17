@extends('layouts.master')
@section('content')
<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
    <h1>
        {{ env('APP_COMPANY')}} Report Centre
        <small>{!! $page_description ?? "Page description" !!}</small>
    </h1>
    {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
</section>


<div class='row'>

    <div class='col-md-6'>
        <!-- Box -->

        <div class="box">

            <div class="box-header">
                <h3> <span class="material-icons">factory</span> CRM Reports </h3> 
                
    
               
            </div>
            <div class="box-body">

                <div class="table-responsive">

                    <table class="table table-hover table-bordered" id="cases-table">
                        <thead>
                            <tr>
                                <th>Report Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td> <a href="/admin/reports/lead_today">Leads Today</a> </td>
                                <td>Todays Leads</td>
                            </tr>

                            <tr>
                                <td> <a href="/admin/reports/converted_leads">Converted Leads</a> </td>
                                <td>Converted Leads</td>
                            </tr>

                            
                            <tr>
                                <td> <a href="/admin/reports/all_activities">Lead Activities</a> </td>
                                <td>Lead Activities</td>
                            </tr>


                            <tr>
                                <td> <a href="/admin/reports/all_contacts">All Contacts</a> </td>
                                <td>All Contacts</td>
                            </tr>

                            <tr>
                                <td> <a href="/admin/reports/all_clients">All Customers</a> </td>
                                <td>All Customers</td>
                            </tr>

                            <tr>
                                <td> <a href="/admin/reports/leads_by_status">Lead by Status</a> </td>
                                <td>Lead by Status</td>
                            </tr>
                            
                            
            
                        </tbody>
                    </table>
                    
                  

                </div> <!-- table-responsive -->

            </div><!-- /.box-body -->
        </div><!-- /.box -->

          <!-- /.box -->
        </div>
   

     <div class='col-md-6'>
        <!-- Box -->

        <div class="box">

            <div class="box-header">
                <h3> <span class="material-icons">paid</span> Account Reports </h3> 
            </div>
            <div class="box-body">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="cases-table">
                        <thead>
                            <tr>
                                <th>Report Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td> <a href="/admin/debtors_lists"> Debtors List</a> </td>
                                <td>Debtors Ledgers</td>
                            </tr>
                            <tr>
                                <td> <a href="/admin/creditors_lists"> Creditors List</a> </td>
                                <td>Creditors Ledgers</td>
                            </tr>

                            

                            <tr>
                                <td> <a href="/admin/orders/returnpos"> Credit Notes </a> </td>
                                <td>Sales Return Book</td>
                            </tr>
                             <tr>
                                <td> <a href="/admin/orders/returnpos"> Debit Notes </a> </td>
                                <td>Purchase Return Book</td>
                            </tr>


                            <tr>
                                <td> <a href="/admin/daybook"> Day Book </a> </td>
                                <td>Day Book and Cash Flow</td>
                            </tr>

                            <tr>
                                <td> <a href="/admin/accounts/reports/ledgerstatement"> Ledger  Statement </a> </td>
                                <td>Individual Ledger statement</td>
                            </tr>

                            <tr>
                                <td> <a href="/admin/accounts/reports/group-ledger-bulk"> Ledger Group Statement </a> </td>
                                <td>Bulk Group Ledger statement</td>
                            </tr>


                            <tr>
                                <td> <a href="/admin/gl"> General Ledger </a> </td>
                                <td>General Ledger</td>
                            </tr>

                            
            
                        </tbody>
                    </table>

                </div> <!-- table-responsive -->

            </div><!-- /.box-body -->
        </div><!-- /.box -->

          <!-- /.box -->
        </div>
   


</div><!-- /.row -->


<div class='row'>
<div class='col-md-6'>
        <!-- Box -->

        <div class="box box-primary">

            <div class="box-header">
                <h3> <span class="material-icons">paid</span> Sales Reports </h3> 
            </div>
            <div class="box-body">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="cases-table">
                        <thead>
                            <tr>
                                <th>Report Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td> <a href="/admin/posSummaryAnalysis"> Sales Analysis</a> </td>
                                <td>Outlets sales analysis</td>
                            </tr>

                            <tr>
                                <td> <a href="/admin/posSummaryAmount">Sales Summary</a> </td>
                                <td>Payment summary datewise</td>
                            </tr>

                            
                            <tr>
                                <td> <a href="/admin/posAmountSummaryDateWise">Daywise Report</a> </td>
                                <td>Daywise Sales Report</td>
                            </tr>


                            <tr>
                                <td> <a href="/admin/product-sales-report/show"> Product Sales Report </a> </td>
                                <td>Sales report by each product item</td>
                            </tr>
                            
                            
            
                        </tbody>
                    </table>

                </div> <!-- table-responsive -->

            </div><!-- /.box-body -->
        </div><!-- /.box -->

          <!-- /.box -->
</div>

<div class='col-md-6'>
        <!-- Box -->

        <div class="box box-primary">

            <div class="box-header">
                <h3> <span class="material-icons">paid</span> Purchase Reports </h3> 
            </div>
            <div class="box-body">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="cases-table">
                        <thead>
                            <tr>
                                <th>Report Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>

                        <tbody>
                            

                            <tr>
                                <td> <a href="/admin/payment-report"> Payment Report </a> </td>
                                <td>Vendor Payment Report</td>
                            </tr>
                            
                            
            
                        </tbody>
                    </table>

                </div> <!-- table-responsive -->

            </div><!-- /.box-body -->
        </div><!-- /.box -->

          <!-- /.box -->
</div>
</div>

<div class="row">

<div class='col-md-6'>
        <!-- Box -->

        <div class="box box-primary">

            <div class="box-header">
                <h3 class="box-title">System Reports </h3> 
            </div>
            <div class="box-body">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="cases-table">
                        <thead>
                            <tr>
                                <th>Report Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td> <a href="/admin/audit">User Activity Audit</a> </td>
                                <td>Audit Trails</td>
                            </tr>

                            <tr>
                                <td> <a href="/admin/errors">Error Exceptions</a> </td>
                                <td>Exceptions Report</td>
                            </tr>
                            

            
                        </tbody>
                    </table>

                </div> <!-- table-responsive -->

            </div><!-- /.box-body -->
        </div><!-- /.box -->

          <!-- /.box -->
</div>




<div class='col-md-6'>
        <!-- Box -->

        <div class="box box-primary">

            <div class="box-header">
                <h3> <span class="material-icons">savings</span> Financial  Reports </h3> 
            </div>
            <div class="box-body">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="cases-table">
                        <thead>
                            <tr>
                                <th>Report Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td> <a href="/admin/accounts/reports/trialbalance">Trial Balance</a> </td>
                                <td>Trial Balance</td>
                            </tr>

                            <tr>
                                <td> <a href="/admin/accounts/reports/balancesheet">Balance Sheet</a> </td>
                                <td>Balance Sheet</td>
                            </tr>

                            <tr>
                                <td> <a href="/admin/accounts/reports/profitloss">Profit Loss</a> </td>
                                <td>Profit Loss Report</td>
                            </tr>
                            

            
                        </tbody>
                    </table>

                </div> <!-- table-responsive -->

            </div><!-- /.box-body -->
        </div><!-- /.box -->

          <!-- /.box -->
</div>




<div class='col-md-6'>
        <!-- Box -->

        <div class="box box-primary">

            <div class="box-header">
                <h3> <span class="material-icons">savings</span> Inventory  Reports </h3> 
            </div>
            <div class="box-body">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="cases-table">
                        <thead>
                            <tr>
                                <th>Report Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td> <a target="_blank" href="/admin/products">inventory Items</a> </td>
                                <td>Items Report</td>
                            </tr>

                             <tr>
                                <td> <a target="_blank" href="/admin/reports/products-purchase">Product Purchase Report</a> </td>
                                <td>Purcahase report by product items</td>
                            </tr>

                            
                            

            
                        </tbody>
                    </table>

                </div> <!-- table-responsive -->

            </div><!-- /.box-body -->
        </div><!-- /.box -->

          <!-- /.box -->
</div>

<div class='col-md-6'>
        <!-- Box -->

        <div class="box box-primary">

            <div class="box-header">
                <h3> <span class="material-icons">savings</span> Tax  Reports </h3> 
            </div>
            <div class="box-body">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="cases-table">
                        <thead>
                            <tr>
                                <th>Report Name</th>
                                <th>Description</th>
                            </tr>
                        </thead>

                        <tbody>

                           <tr>
                                <td> <a href="/admin/purchase-book"> Purchase book</a> </td>
                                <td>Purchase Book</td>
                            </tr>
                            <tr>
                                <td> <a href="/admin/orders/alloutlet"> Sales book</a> </td>
                                <td>Sales Book</td>
                            </tr>

                
            
                        </tbody>
                    </table>

                </div> <!-- table-responsive -->

            </div><!-- /.box-body -->
        </div><!-- /.box -->

          <!-- /.box -->
</div>



    </div>


<script type="text/javascript">
    
    function dowloadSales(ev)
    {   

        var url = $(ev).attr('data-href');

        let date = $('#selected_dates').val();
        
        url = `${url}?date=${date}`;
        location.href = url;
        
        return;

    }


</script>
@endsection
