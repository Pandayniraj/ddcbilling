<style>
    #notviewed-leads_length {
        float: left;
    }

    #notviewed-leads_filter {
        float: right;
    }

    @media (max-width: 767px) {
        .MobileView {
            display: none;
        }
    }

    @media (min-width: 767px) {

        .DeskView {
            display: none;
        }
    }

</style>
<link href="{{ asset("/bower_components/admin-lte/plugins/jQueryUI/jquery-ui.css") }}" rel="stylesheet"
      type="text/css"/>
<link href="{{ asset("/bower_components/admin-lte/bootstrap/css/bootstrap-datetimepicker.css") }}" rel="stylesheet"
      type="text/css"/>

<script src="{{ asset ("/bower_components/admin-lte/plugins/daterangepicker/moment.js") }}"
        type="text/javascript"></script>
<script src="{{ asset ("/bower_components/admin-lte/bootstrap/js/bootstrap-datetimepicker.js") }}"
        type="text/javascript"></script>
<script type="text/javascript">
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    if (getCookie(('sidebar_collapse'))) {
        $('.sidebar-mini').addClass('sidebar-collapse');
    }

</script>
<!-- Main Header -->
<header class="main-header">
    <!-- Logo -->
    <a href="{{ route('home') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <!-- logo for regular state and mobile devices -->
        <!--  <span class="logo-lg" style="margin-top:-10px">
            <img src="/org/<?php //  echo TaskHelper::GetOrgName(\Auth::user()->org_id)->logo ?>">
             </span> -->
        {{ \Config::get('erp.appname') }}
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>
        <ul class="nav navbar-nav MobileView">
            <li>
                <a href="/home" class=""><i class="fa fa-home"></i> {{ trans('general.navbar.home') }} </a>
            </li>
            {{--   <li>
                  <a href="/admin/talk"> {{ trans('admin/chat/general.title.navbar') }} </a>
              </li> --}}

            <li>
                <a href="#"><i class="fa fa-calendar-plus-o"></i>&nbsp;{{ FinanceHelper::cur_fisc_yr()->fiscal_year }}
                </a>
            </li>
            <li>
                <a href="#"><i class="fa fa-sun-o"></i>&nbsp;{{ FinanceHelper::cur_leave_yr()->leave_year }}</a>
            </li>

            <li class="dropdown dropdown-large" style="display: none;">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">BEV <b class="caret"></b></a>
                <ul class="dropdown-menu dropdown-menu-large row">
                    <li class="col-sm-3">
                        <ul>
                            <li class="dropdown-header">Financial Mgmt</li>
                            <li><a href="/admin/stock/list">Fixed Assets</a></li>
                            <li><a href="/admin/finance/dashboard">Finance Board</a></li>
                            <li><a href="/admin/accounts/reports/balancesheet">Balance Sheet</a></li>
                            <li><a href="/admin/accounts/reports/profitloss">Profit Loss</a></li>
                            <li><a href="/admin/accounts/reports/trialbalance">Trial Balance</a></li>
                            <li><a href="/admin/salesboard">Sales Analysis</a></li>
                            <li class="divider"></li>
                            <li class="dropdown-header">CRM</li>
                            <li><a href="/admin/leads?type=leads">Leads</a></li>
                            <li><a href="/admin/leads?type=target">Online Leads</a></li>
                            <li><a href="/admin/proposal">Proposals</a></li>
                            <li><a href="/admin/orders?type=quotation">Quotes</a></li>

                        </ul>
                    </li>
                    <li class="col-sm-3">
                        <ul>
                            <li class="dropdown-header">Accounting</li>
                            <li><a href="/admin/entries">Voucher Entry</a></li>
                            <li><a href="/admin/bank">Income</a></li>
                            <li><a href="/admin/expenses">Expense</a></li>
                            <li><a href="/admin/daybook">Day Book</a></li>
                            <li><a href="/admin/reconciliations">Reconcile</a></li>
                            <li><a href="/admin/chartofaccounts">COA</a></li>


                            <li class="divider"></li>
                            <li class="dropdown-header">Office/ PM</li>
                            <li><a href="/admin/darta">Darta</a></li>
                            <li><a href="/admin/chalani">Chalani</a></li>
                            <li><a href="/admin/documents">DMS</a></li>
                            <li><a href="/admin/calendar">Planner</a></li>
                        </ul>
                    </li>
                    <li class="col-sm-3">
                        <ul>
                            <li class="dropdown-header">Inventory</li>
                            <li><a href="/admin/requisition">Requisition</a></li>
                            <li><a href="/admin/products">Products</a></li>
                            <li><a href="/admin/product/stock_adjustment">Stock Adjustment</a></li>
                            <li><a href="/admin/product/statement">Stock Ledger</a></li>
                            <li><a href="/admin/grn">GRN</a></li>

                            <li class="divider"></li>
                            <li class="dropdown-header">Sales</li>
                            <li><a href="/admin/sales-book">Sales Book</a></li>
                            <li><a href="/admin/orders?type=invoice">P Invoice</a></li>
                            <li><a href="/admin/invoice1">Tax Invoice</a></li>
                            <li><a href="/admin/orders/payments/list">Receipts</a></li>
                            <li><a href="/admin/debtors_lists">Debtors List</a></li>

                        </ul>
                    </li>
                    <li class="col-sm-3">
                        <ul>
                            <li class="dropdown-header">HRMS</li>
                            <li><a href="/admin/employee/directory">Directory</a></li>
                            <li><a href="/admin/timeHistory">Attendance</a></li>
                            <li><a href="/admin/leavereport">Leaves</a></li>
                            <li><a href="/admin/tarvelrequest">Outstation</a></li>
                            <li><a href="/admin/job_posted">Recruitment</a></li>
                            <li><a href="/admin/hrcalandar">Public Calendar</a></li>

                            <li class="divider"></li>
                            <li class="dropdown-header">Purchase</li>
                            <li><a href="/admin/purchase-book">Purchase Book</a></li>
                            <li><a href="/admin/purchase?type=purchase_orders">PO</a></li>
                            <li><a href="/admin/purchase?type=bills">Bills</a></li>
                            <li><a href="/admin/purchase/paymentslist">Payments</a></li>
                            <li><a href="/admin/creditors_lists">Creditors List</a></li>

                        </ul>
                    </li>


                </ul>

            </li>

            <?php $orgs = \App\Models\Organization::pluck('organization_name', 'id')->all();
            ?>
            @if( isset(\Auth::user()->super_manager) &&  \Auth::user()->super_manager == 1)

                <form class="navbar-form navbar-left" role="search">
                    <div class="form-group">
                        {!! Form::select('org_id', [''=>'Select Org']+$orgs, \Auth::user()->org_id, ['class' => 'form-control input-sm label-default', 'id' => "header_org_id"]) !!}
                        <input type="hidden" name="header_user_id" id="header_user_id" value="{{\Auth::user()->id}}">
                    </div>
                </form>

            @endif

        </ul>


        <div class="navbar-custom-menu DeskView">

            <ul class="nav navbar-nav">

                <li>
                    <a href="/home" class=""><i class="fa fa-home"></i> Home </a>
                </li>
                <li>
                    <a href="/admin/talk"> Chat </a>
                </li>

                @if (Auth::check())

                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->

                            @if(\Auth::user()->image)
                                <img src="/images/profiles/{{\Auth::user()->image}}" class="user-image"
                                     alt="User Image"/>
                            @else
                                <img src="{{ Gravatar::get(Auth::user()->email , 'tiny') }}" class="user-image"
                                     alt="User Image"/>
                            @endif


                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs">{{ Auth::user()->username }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                                @if(\Auth::user()->image)
                                    <img src="/images/profiles/{{\Auth::user()->image}}" wi class="img-circle"
                                         alt="User Image"/>
                                @else
                                    <img src="{{ Gravatar::get(Auth::user()->email , 'tiny') }}" class="img-circle"
                                         alt="User Image"/>
                                @endif
                                <p>
                                    {{ Auth::user()->full_name }}
                                    <small>
                                        {{ PayrollHelper::getDepartment(\Auth::user()->departments_id) }},
                                        {{ PayrollHelper::getDesignation(\Auth::user()->designations_id) }}
                                    </small>
                                </p>


                            </li>


                            <!-- Menu Footer-->
                            <li class="user-footer">

                                @if ( \Config::get('settings.app_user_profile_link') )
                                    <div class="pull-left">
                                        {!! link_to_route('user.profile', 'Profile', [], ['class' => "btn btn-default btn-flat"]) !!}
                                    </div>
                                @endif

                                <div class="pull-right">
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"
                                       class="btn btn-default btn-flat">
                                        Logout
                                    </a>
                                </div>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>

                @else
                    <li>{!! link_to_route('login', 'Sign in') !!}</li>
                    @if (\Config::get('settings.app_allow_registration'))
                        <li>{!! link_to_route('register', 'Register') !!}</li>
                    @endif

                @endif


            </ul>
        </div>

        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu MobileView">


            <ul class="nav navbar-nav ">

                @if (Auth::check())

                    {{--
                                    <li class="dropdown messages-menu">
                                        <div class="margin ">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-danger btn-xs"><i class="fa fa-plus"></i> New</button>
                                                <button type="button" class="btn btn-danger btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="/admin/leads/create?type=leads"><i class="fa fa-check"> </i> New Lead</a></li>
                                                    <li><a href="/admin/tasks/create?q=task"><i class="fa fa-check"> </i> New Marketing Task</a></li>
                                                    <li><a href="/admin/contacts/create"><i class="fa fa-check"> </i> New Contact</a></li>
                                                    <li><a href="/admin/proposal"><i class="fa fa-check"> </i> New Proposal</a></li>
                                                    <li><a href="/admin/campaigns/create"><i class="fa fa-check"> </i> New Marketing Campaign</a></li>
                                                    <li class="divider"></li>
                                                    <li><a href="/admin/entries"><i class="fa fa-check"> </i> New Voucher</a></li>
                                                    <li><a href="/admin/orders?type=quotation"><i class="fa fa-check"> </i> New Quotation</a></li>
                                                    <li><a href="/admin/invoice"><i class="fa fa-check"> </i> New Tax Invoice</a></li>
                                                    <li><a href="/admin/purchase?type=purchase_orders"><i class="fa fa-check"> </i> New Purchase Order</a></li>
                                                    <li><a href="/admin/clients"><i class="fa fa-check"> </i> New Billing Customer and Supplier</a></li>
                                                    <li class="divider"></li>
                                                    <li><a href="/admin/cases/create"><i class="fa fa-check"> </i> New Case</a></li>
                                                    <li><a href="/admin/knowledge/create"><i class="fa fa-book"> </i> New Knowledge Base/ Manual</a></li>

                                                    <li class="divider"></li>
                                                    <li><a href="/admin/leave_management"><i class="fa fa-clock-o"> </i> New Leave</a></li>
                                                    <li><a href="/admin/tarvelrequest/create"><i class="fa fa-plane"> </i> New Travel Request</a></li>
                                                    <li><a href="/admin/addevent"><i class="fa fa-calendar"> </i> New Event</a></li>

                                                </ul>
                                            </div>
                                        </div>
                                    </li> --}}



                    @if ( \Config::get('settings.app_context_help_area') && (isset($context_help_area)))
                        {!! $context_help_area !!}
                    @endif

                    @if(\Auth::user()->hasRole(['admins','hr-staff']))
                        <li class="dropdown notifications-menu open">
                            <a href="/admin/allpendingprofilerequest" class="dropdown-toggle" aria-expanded="true"
                               title="Profile Change Request">
                                <span class="material-icons">switch_account</span>
                                <span class="label label-danger">{{ $pending_profile }}</span>
                            </a>
                        </li>
                    @endif

                    @if(\Auth::user()->hasRole(['admins','hr-staff']))
                        <li class="dropdown notifications-menu">
                            <a href="/admin/allpendingleaves?leave_status=4" class="dropdown-toggle"
                               aria-expanded="true" title="Leave Request">
                                <i class="fa fa-bell-o"></i>
                                <span class="label label-danger">{{ $pending_leaves }}</span>
                            </a>
                        </li>

                        <li style="margin-left: -11px;">
                            <a href="/admin/timechange_request" title="Time Change Request"><i class="material-icons">timer</i>
                                <span class="label label-primary">{{ $p_attendance }}</span>
                            </a>
                        </li>
                    @elseif(\Auth::user()->isAuthsupervisor())
                        <li class="dropdown notifications-menu open">
                            <a href="/admin/allpendingleaves?leave_status=4" class="dropdown-toggle"
                               aria-expanded="true" title="Leave Request">
                                <i class="fa fa-bell-o"></i>
                                <span class="label label-danger">{{ $line_pending_leaves }}</span>
                            </a>
                        </li>

                        <li style="margin-left: -11px;">
                            <a href="/admin/timechange_request" title="Time Change Request"><i class="material-icons">timer</i>
                                <span class="label label-primary">{{ $line_attendance }}</span>
                            </a>
                        </li>
                    @endif

                    @if(\Config::get('settings.app_notification_area'))
                        <!-- Messages: style can be found in dropdown.less-->


                        <li style="margin-left: -11px;">
                            <a href="/admin/stickynote"><i class="material-icons">sticky_note_2</i></a>
                        </li>


                        <li style="margin-left: -11px;">
                            <a href="/admin/talk"> <i class="material-icons">chat</i></a>
                        </li>

                    @endif
                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <!-- The user image in the navbar-->

                            @if(\Auth::user()->image)
                                <img src="/images/profiles/{{\Auth::user()->image}}" class="user-image"
                                     alt="User Image"/>
                            @else
                                <img src="{{ Gravatar::get(Auth::user()->email , 'tiny') }}" class="user-image"
                                     alt="User Image"/>
                            @endif


                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span class="hidden-xs">{{ Auth::user()->username }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- The user image in the menu -->
                            <li class="user-header">
                                @if(\Auth::user()->image)
                                    <img src="/images/profiles/{{\Auth::user()->image}}" wi class="img-circle"
                                         alt="User Image"/>
                                @else
                                    <img src="{{ Gravatar::get(Auth::user()->email , 'tiny') }}" class="img-circle"
                                         alt="User Image"/>
                                @endif
                                <p>
                                    {{ Auth::user()->full_name }}
                                    <small>
                                        {{ PayrollHelper::getDepartment(\Auth::user()->departments_id) }},
                                        {{ PayrollHelper::getDesignation(\Auth::user()->designations_id) }}
                                    </small>
                                </p>


                            </li>

                            @if( \Config::get('settings.app_extended_user_menu') )
                                <!-- Menu Body -->
                                <!--      <li class="user-body">
                                            <div class="col-xs-4 text-center">
                                                <a href="#">My PIS</a>
                                            </div>
                                            <div class="col-xs-4 text-center">
                                                <a href="#">Sales</a>
                                            </div>
                                            <div class="col-xs-4 text-center">
                                                <a href="#">Friends</a>
                                            </div>
                                        </li> -->
                            @endif

                            <!-- Menu Footer-->
                            <li class="user-footer">

                                @if ( \Config::get('settings.app_user_profile_link') )
                                    <div class="pull-left">
                                        {!! link_to_route('user.profile', 'Profile', [], ['class' => "btn btn-default btn-flat"]) !!}
                                    </div>
                                @endif

                                <div class="pull-right">
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"
                                       class="btn btn-default btn-flat">
                                        Logout
                                    </a>
                                </div>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                      style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </li>

                    @if ( \Config::get('settings.app_right_sidebar') )
                        <!-- Control Sidebar Toggle Button -->

                    @endif
                @else
                    <li>{!! link_to_route('login', 'Sign in') !!}</li>
                    @if (\Config::get('settings.app_allow_registration'))
                        <li>{!! link_to_route('register', 'Register') !!}</li>
                    @endif
                @endif
            </ul>
        </div>
    </nav>

</header>

<script type="text/javascript">
    $(document).on('change', '#header_org_id', function () {
        var id = $('#header_user_id').val();
        var org_id = $(this).val();

        $.post("{{route('admin.organization.ajaxorg')}}", {
                id: id
                , org_id: org_id
                , _token: $('meta[name="csrf-token"]').attr('content')
            }
            , function (data, status) {
                if (data.status == '1')
                    location.reload();
                else
                    $("#header_ajax_status").after("<span style='color:red;' id='header_status_update'>Problem in updating org; Please try again.</span>");

                $('#header_status_update').delay(3000).fadeOut('slow');
                //alert("Data: " + data + "\nStatus: " + status);
            }).fail(function () {
            alert("Error In changing location");
        });
    });
    $(document).ready(function () {
        $('#chatpopover').click(function () {
            var e = $(this);
            if (!e.attr('aria-describedby')) {

                $.get(e.data('poload'), function (d) {
                    e.popover({
                        container: 'body'
                        , content: d.message
                    }).popover('show');

                });

            } else {
                e.popover('destroy');
                $('.popover').html('');
                e.attr('aria-describedby', undefined);
            }
        });
    });

    $('.sidebar-toggle').click(function () {


        if ($('body').hasClass('sidebar-collapse')) {

            //user has removed collapse
            document.cookie = "sidebar_collapse=;path=/"

        } else {

            console.log("OK");
            document.cookie = "sidebar_collapse=true;path=/"
        }

        //console.log(document.cookie);

    });

</script>
