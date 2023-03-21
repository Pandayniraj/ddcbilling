<div class="modal-header">
    <h4 class="modal-title">{{ $modal_title }}</h4>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
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
            </tr>
        </thead>
        <tbody>
            @foreach ($deposits as $key => $value)
                @php
                    $balance = 0;
                    $invoice = App\Models\Invoice::where('bill_no', $value->reference_no)->first();
                    $payment = App\Models\InvoicePayment::where('reference_no', $value->reference_no)->first();
                    $balance = $value->prev_balance;
                @endphp
                <tr>
                    <td>

                        <input type="checkbox" checked value="{{ $value->id }}" class="check-this2" @if(!$fromDepo) disabled @endif>
                    </td>
                    <td>{{ $value->id }}</td>
                    <td>
                        @if ($invoice)
                            <a target="_blank"
                                href="/admin/entries/show/{{ \FinanceHelper::get_entry_type_label($invoice->entry->entrytype_id) }}/{{ $invoice->entry->id }}">{{ $invoice->entry->number }}</a>
                        @else
                            <a target="_blank"
                                href="/admin/entries/show/{{ \FinanceHelper::get_entry_type_label($value->entry->entrytype_id) }}/{{ $value->entry->id }}">{{ $value->entry->number }}</a>
                        @endif
                    </td>
                    <td>{{ $value->date }}</td>
                    <td>{{ $invoice->outlet ? $invoice->outlet->short_name . '/' . $invoice->fiscal_year . '/00' . $value->reference_no : 'DEP00' . $value->id }}
                    </td>
                    <td>{{ !$value->reference_no ? 'Deposit' : ($value->type == 'Deduct' ? 'Invoice' : 'Receipt') }}
                    </td>
                    <td>{{ $value->type == 'Deduct' ? -$value->amount : $value->amount }}</td>
                    <td class="balance">{{ $balance }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <script>
        calcBalance2();
        const checkElements2 = document.querySelectorAll('.check-this2');
        checkElements2.forEach(element => {
            let trElement = element.parentElement.parentElement;
            element.addEventListener('click', function(e) {
                let balance = Number(trElement.querySelector('.balance').textContent);
                calcBalance2();
            })
        })

        function calcBalance2() {
            let label = "";
            let balance = 0;
            let negatives = false;
            let positives = false;
            let fromDepo ="{{ $fromDepo }}"
            let checkedFields = "";
            const checkedElements = document.querySelectorAll('.check-this2:checked');
            checkedElements.forEach(element => {
                let trElement = element.parentElement.parentElement;
                let blc = Number(trElement.querySelector('.balance').textContent);
                balance += blc;
                if (blc < 0) negatives = true;
                if (blc > 0) positives = true;
                checkedFields += `<input type="hidden" name="idsu[]" value=${element.value}>`;
            })
            if (!fromDepo || (negatives && positives)) {
                showFields(balance, checkedFields);
            } else {
                hideFields();
            }
        }

        function showFields(balance, checkedFields) {
            document.querySelector('.settlement-fields2').style.display = "block";
            document.getElementById('amountu').value = balance;
            document.querySelector('.deposit-field2').innerHTML = checkedFields;
            document.querySelector('.btn-resettle-submit').removeAttribute('disabled');
        }

        function hideFields() {
            document.querySelector('.settlement-fields2').style.display = "none";
            document.getElementById('amountu').value = 0;
            document.querySelector('.deposit-field2').innerHTML = "";
            document.querySelector('.btn-resettle-submit').setAttribute('disabled', '');
        }

        let btnResettleSubmit = document.querySelector('.btn-resettle-submit');
        btnResettleSubmit.addEventListener('click', function(e){
            e.preventDefault();
            if(confirm('Are you sure you want to resettle !')){
                document.getElementById('resettleForm').submit();
            }
        })
    </script>
    <div class="box box-primary settlement-fields2">
        <div class='row'>
            <div class='col-md-12'>
                <div class="box">
                    <div class="box-body ">
                        <form method="post"
                            action="{{ route('admin.customerdeposits.resettlestore', $pdeposit->id) }}" id="resettleForm">
                            @csrf
                            <div class="row">
                                <div class="deposit-field2">

                                </div>
                                {{-- 
                                    Amount: Calculate
                                    Reference No: Calculate
                                    --}}
                                <div class="col-md-2">
                                    <label class="control-label">Balance</label>
                                    <input type="text" name="amountu" id="amountu" value="0"
                                        class="form-control" readonly>

                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Payment Date</label>
                                    <input type="date" name="dateu" placeholder="Start Date" id="dateu"
                                        value="{{ Carbon\Carbon::create($pmt->date)->format('Y-m-d') }}"
                                        class="form-control ">
                                </div>
                                <div class="col-md-3">
                                    <label class="control-label">Received In</label>
                                    <select class='form-control searchable select2' name='paid_byu'>
                                        @foreach ($groups as $grp)
                                            <option value="{{ $grp->id }}"
                                                {{ $grp->id == $pmt->paid_by ? 'selected' : '' }}>
                                                {{ $grp->name }}
                                            </option>;
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="control-label">Note</label>
                                    <input type="text" name="noteu" placeholder="Note" id="noteu"
                                        value="{{ $pmt->note }}" class="form-control">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer justify-content-between">
    <button type="button" class="btn  btn-default" data-dismiss="modal">{{ trans('general.button.cancel') }}</button>
    <a href="#" type="button" class="btn  btn-success btn-resettle-submit" disabled>Update Settlement</a>
</div>
