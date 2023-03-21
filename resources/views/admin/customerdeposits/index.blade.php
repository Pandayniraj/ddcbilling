@extends('layouts.master')
@section('content')
    <style>
        .modal-dialog {
            width: 700px;
            margin: 30px auto;
        }
    </style>
    <section class="content-header" style="margin-top: -35px; margin-bottom: 20px">
        <h1>
            {{ $page_title ?? 'Page Title' }}
            <small> {{ $customer->name ?? '' }}</small>
        </h1>
        {!! MenuBuilder::renderBreadcrumbTrail(null, 'root', false) !!}
    </section>


    <div class="box box-primary">
        <div class='row'>
            <div class='col-md-12'>
                <div class="box">
                    <div class="box-body ">
                        <form method="get" action="/admin/customerdeposits/index?id={{ $data['id'] }}"
                            enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="control-label">Start Date</label>
                                    <input type="date" name="startdate" placeholder="Start Date" id="date"
                                        value="{{ $data['startdate'] }}" class="form-control ">
                                    <input type="hidden" name="id" value="{{ $data['id'] }}" class="form-control ">
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">End date</label>
                                    <input type="date" name="enddate" placeholder="End Date" id="date"
                                        value="{{ $data['enddate'] }}" class="form-control ">
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Type</label>
                                    <select name="type" id="" class="form-control ">
                                        <option value="">Select Type</option>
                                        <option value="Deposit" @if ($data['type'] == 'Deposit') selected @endif>Deposit
                                        </option>
                                        <option value="Deduct" @if ($data['type'] == 'Deduct') selected @endif>Deduct
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <br>
                                    {!! Form::submit('Filter', ['class' => 'btn btn-primary']) !!}
                                    <a href="/admin/customerdeposits/index?id={{ $data['id'] }}"
                                        class='btn btn-default'>Reset</a>
                                    <a href="/admin/customerdeposits/create?id={{ $data['id'] }}"
                                        class="btn btn-success">Add Deposit</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-hover table-no-border" id="customerdeposits-table">
            <thead>
                <tr>
                    <td></td>
                    <th>ID</th>
                    <th>Voucher#</th>
                    <th>Date</th>
                    <th>Reference No</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Balance</th>
                    <th>Closing</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customerdeposits as $key => $value)
                    @php
                        $balance = 0;
                        $invoice = App\Models\Invoice::where('bill_no', $value->reference_no)->first();
                        $payment = App\Models\InvoicePayment::where('reference_no', $value->reference_no)->first();
                        $balance = $value->balance;
                    @endphp
                    <tr>
                        <td>

                            <input type="checkbox" @if ($balance == 0) disabled @endif
                                value="{{ $value->id }}" class="check-this">
                        </td>
                        <td>{{ $value->id }}</td>
                        <td>
                            @if ($invoice)
                                <a target="_blank"
                                    href="/admin/entries/show/{{ \FinanceHelper::get_entry_type_label($invoice->entry->entrytype_id) }}/{{ $invoice->entry->id }}">{{ $invoice->entry->number }}</a>
                            @elseif($payment)
                                <a target="_blank"
                                    href="/admin/entries/show/{{ \FinanceHelper::get_entry_type_label($payment->entry->entrytype_id) }}/{{ $payment->entry->id }}">{{ $payment->entry->number }}</a>
                            @else
                                <a target="_blank"
                                    href="/admin/entries/show/{{ \FinanceHelper::get_entry_type_label($value->entry->entrytype_id) }}/{{ $value->entry->id }}">{{ $value->entry->number }}</a>
                            @endif
                        </td>
                        <td>{{ $value->date }}</td>
                        <td>{{ $invoice->outlet ? $invoice->outlet->short_name . '/' . $invoice->fiscal_year . '/00' . $value->reference_no : ($value->reference_no ? 'RCV' . $value->reference_no : 'DEP00' . $value->id) }}
                        </td>
                        <td>{{ !$value->reference_no ? 'Deposit' : ($value->type == 'Deduct' ? 'Invoice' : 'Receipt') }}
                        </td>
                        <td>{{ $value->type == 'Deduct' ? -$value->amount : $value->amount }}</td>
                        <td class="balance">{{ $balance }}</td>
                        <td>{{ $value->closing }}</td>
                        <td>
                            @if ($balance != 0 && $invoice)
                                <a href="/admin/payment/invoice/{{ $invoice->id }}/create"
                                    class="btn btn-primary btn-xs">Pay Now</a>
                            @endif
                            @if ($payment && $balance == 0)
                                <a href="/admin/customerdeposits/{{ $value->id }}/unsettle"
                                    class="btn btn-danger btn-xs confirm-unsettle">Unsettle</a>

                                <a href="/admin/customerdeposits/{{ $value->id }}/resettle"
                                    class="btn btn-success btn-xs" data-toggle="modal" data-target="#modal_dialog"
                                    escape="false" title="Delete">Resettle</a>
                            @endif
                        </td>
                    </tr>
                @endforeach

            </tbody>
        </table>
        @push('jss')
            <script>
                const checkElements = document.querySelectorAll('.check-this');
                checkElements.forEach(element => {
                    let trElement = element.parentElement.parentElement;
                    element.addEventListener('click', function(e) {
                        let balance = Number(trElement.querySelector('.balance').textContent);
                        // document.getElementById('amount').value = balance;
                        calcBalance();
                    })
                })

                function calcBalance() {
                    let label = "";
                    let balance = 0;
                    let negatives = false;
                    let positives = false;
                    let checkedFields = "";
                    const checkedElements = document.querySelectorAll('.check-this:checked');
                    checkedElements.forEach(element => {
                        let trElement = element.parentElement.parentElement;
                        let blc = Number(trElement.querySelector('.balance').textContent);
                        balance += blc;
                        if (blc < 0) negatives = true;
                        if (blc > 0) positives = true;
                        checkedFields += `<input type="hidden" name="ids[]" value=${element.value}>`;
                    })


                    if (negatives && positives) {
                        document.querySelector('.settlement-fields').style.display = "block";
                        document.getElementById('amount').value = balance;
                        document.querySelector('.deposit-field').innerHTML = checkedFields;
                    } else {
                        document.querySelector('.settlement-fields').style.display = "none";
                        document.getElementById('amount').value = 0;
                        document.querySelector('.deposit-field').innerHTML = "";
                    }
                }
                const unsettleBtn = document.querySelector('.confirm-unsettle');
                unsettleBtn.addEventListener('click', function(e) {
                    let url = this.getAttribute('href');
                    e.preventDefault();
                    if (confirm('Are you sure you want to unsettle this payment')) {
                        window.location.href = url;
                    }
                })
            </script>
        @endpush
        {{ $customerdeposits->appends($_GET)->links() }}
        <div class="box box-primary settlement-fields" style="display:none;">
            <div class='row'>
                <div class='col-md-12'>
                    <div class="box">
                        <div class="box-body ">
                            <form method="post" action="{{ route('admin.customerdeposits.settlement') }}">
                                @csrf
                                <div class="row">
                                    <div class="deposit-field">

                                    </div>
                                    {{-- 
                                        Amount: Calculate
                                        Reference No: Calculate
                                        --}}
                                    <div class="col-md-2">
                                        <label class="control-label">Balance</label>
                                        <input type="text" name="amount" id="amount" value="0"
                                            class="form-control" readonly>

                                    </div>
                                    <div class="col-md-2">
                                        <label class="control-label">Payment Date</label>
                                        <input type="date" name="date" placeholder="Start Date" id="date"
                                            value="{{ date('Y-m-d') }}" class="form-control ">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="control-label">Received In</label>
                                        <select class='form-control searchable select2' name='paid_by'>
                                            @foreach ($groups as $grp)
                                                <option value="{{ $grp->id }}" {{ $grp->id == 4 ? 'selected' : '' }}>
                                                    {{ $grp->name }}
                                                </option>;
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="control-label">Note</label>
                                        <input type="text" name="note" placeholder="Note" id="note"
                                            value="" class="form-control ">
                                    </div>
                                    <div class="col-md-2">
                                        <br>
                                        {!! Form::submit('Make Settlement', ['class' => 'btn btn-success']) !!}
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
