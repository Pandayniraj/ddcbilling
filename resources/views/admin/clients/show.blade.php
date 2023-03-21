@extends('layouts.master')

@section('head_extra')
    <!-- Select2 css -->
    @include('partials._head_extra_select2_css')

@endsection

@section('content')
<section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
            <h1>{!! $client->name !!}, {!! $client->stock_symbol !!}<small> {{ $client->type }}</small> </h1>
            {{ TaskHelper::topSubMenu('topsubmenu.clients')}}
          <i class="fa fa-info"></i> {!! $client->name !!}'s Profile page


            {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false)  !!}
        </section>
    <div class='row'>
        <div class='col-md-12'>
        	<div class="box box-primary">
            	<div class="box-header with-border">


                    @if($client->image)
                        <img src="{{asset($client->image)}}" class="p-image" id="blah" src="#" alt="your image" />
                        <br/>
                    @else
                        <img src="/images/profiles/default.png" class="p-image" id="blah" src="#" alt="your image" width="100px" height="100px;" />
                        <br/>
                    @endif

                    @if(!empty($client->location))
                    <p> Location: {!! $client->location !!}
                    @endif
                    @if(!empty($client->industry))
                     Industry: {!! $client->industry !!}
                     @endif

                 </p>
                    <p> Phone: {!! $client->phone !!} </p>
                    <p> Email: {!! $client->email !!} </p>
                    @if(!empty($client->website))
                    <p> Website: {!! $client->website !!} </p>
                    @endif
                    <p> PAN/VAT/Tax ID: {!! $client->vat !!} </p>
                    @php
                        $amount=\App\Models\CustomerDeposit::where('client_id',$client->id)->orderby('id','desc')->first()->closing??0;
                    @endphp
                    <p><a href="/admin/customerdeposits/index?id={{ $client->id }}">Deposit Amount: {!! $amount !!}</a> </p>
                </div>
                <div class="box-body">

                    {!! Form::model($client, ['route' => 'admin.clients.index', 'method' => 'GET']) !!}

                    <div class="content">

                        <div class="form-group">
                            <a  href="/admin/customer?relation_type={{ \Request::get('relation_type') }}"  class="btn btn-primary">{{ trans('general.button.close') }}</a>
                            @if ( $client->isEditable() || $client->canChangePermissions() )
                        <a href="{!! route('admin.clients.edit', $client->id) !!}?relation_type={{\Request::get('relation_type')}}" title="{{ trans('general.button.edit') }}" class='btn btn-default'>{{ trans('general.button.edit') }}</a>
                            @endif

                            <a class="btn btn-primary" href="#" onclick="openwindow('{{$client->id}}')"> <i class="fa fa-external-link-alt"></i> New Contact</a>

                            @if($client->relation_type == 'customer')
                            <a href="/admin/chartofaccounts/detail/{{$client->ledger_id}}/ledgers" title="view ledger" class='btn btn-danger' target="_blank">Orders</a>
                            <a href="/admin/chartofaccounts/detail/{{$client->ledger_id}}/ledgers" title="view ledger" class='btn btn-danger' target="_blank">Invoices</a>
                            @endif

                            @if($client->relation_type == 'distributor')
                            <a href="/admin/clients/distributor/retailer?client_id={{$client->id}}" title="View Retailer" class='btn btn-danger' target="_blank">Retailer</a>
                            <a href="/admin/clients/distributor/boothman?client_id={{$client->id}}" title="View Boothman" class='btn btn-danger' target="_blank">Boothman</a>

                            @endif
                            <a href="/admin/accounts/reports/ledger_statement?ledger_id={{$client->ledger_id}}" title="view ledger" class='btn btn-danger' target="_blank">View Ledger</a>
                        </div>


                        {!! Form::close() !!}



                        <h3>Contacts</h3>
                        <div class="box box-header">
                        <table class="table table-hover table-bordered" id="contacts-table">
                            <thead>
                                <tr>
                                    <th>{{ trans('admin/contacts/general.columns.full_name') }}</th>
                                    <th>Position</th>
                                    <th>Phone</th>
                                    <th>Primary Email</th>
                                    <th>Company</th>
                                </tr>
                            </thead>

                            <tbody>
                            @if(isset($contacts) && !empty($contacts))
                                @foreach($contacts as $contact)
                                    <tr>
                                        <td><strong>{!! link_to_route('admin.contacts.show', $contact->full_name, [$contact->id], []) !!}</strong></td>

                                        <td>{!! $contact->position !!}</td>
                                        <td>{!! $contact->phone !!}</td>
                                        <td><a href="mailto::{{$contact->email_1}}"> {!! $contact->email_1 !!}</a></td>
                                        <td>{!! $contact->client->name !!}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>

                        @if(isset($cashbook) && sizeof($cashbook))
                        <h2>Cashbook</h2>
                        <table class="table table-hover table-bordered" id="cashbook-table">
                            <thead>
                                <tr>
                                    <th>Purpose</th>
                                    <th>Method</th>
                                    <th>Category</th>
                                    <th>Date</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $total = 0;
                                ?>
                                @foreach($cashbook as $k)
                                <tr>
                                    <td class="lead"> {!! link_to_route('admin.cashbook.show', $k->descs, [$k->id], ['target' => '_blank']) !!}</td>
                                    <td class="">{{ $k->paymentmethod->name }}</td>
                                    <td class="">{{ $k->category->name }}</td>
                                    <td class="">{{ date('dS M Y', strtotime($k->date)) }}</td>
                                    <td class="">{{ $k->credit }}</td>
                                    <td class="">{{ $k->debit }}</td>
                                    <td class="">
                                        <?php
                                            if($k->debit != '')
                                                $total = $total + $k->debit;
                                            if($k->credit != '')
                                                $total = $total - $k->credit;
                                        ?>
                                        {{ $total }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif

                        @if(isset($proposal) && sizeof($proposal))
                        <h2>Proposal</h2>
                        <table class="table table-hover table-bordered" id="proposal-table">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Product</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($proposal as $k)
                                    <tr>
                                        <td class="lead"> {!! link_to_route('admin.proposal.show', $k->subject, [$k->id], ['target' => '_blank']) !!}</td>
                                        <td class="">{{ $k->product->name }}</td>
                                        <td class=""><span class="label bg-green">{{ ucfirst($k->status)}}</span></td>
                                        <td class="">{{ ucfirst($k->type)}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif

                    </div><!-- /.content -->

                </div><!-- /.box-body -->
            </div>
        </div><!-- /.col -->

    </div><!-- /.row -->

    <script>
    function openwindow(clientid){

    var win =  window.open(`/admin/contacts/create/modals?clientid=${clientid}`, '_blank','toolbar=yes, scrollbars=yes, resizable=yes, top=500,left=500,width=600, height=650');
    }
     function HandlePopupResult(result) {
      if(result){
        let contact = result.contacts;

        setTimeout(function(){
            location.reload()
        },500);
      }
      else{
        $("#ajax_status").after("<span style='color:red;' id='status_update'>failed to create clients</span>");
        $('#status_update').delay(3000).fadeOut('slow');
      }
    }


    </script>

@endsection

@section('body_bottom')
    <!-- Select2 js -->
    @include('partials._body_bottom_select2_js_user_search')
@endsection
