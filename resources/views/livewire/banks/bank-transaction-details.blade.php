<div>
    <div class="table-responsive table-responsive--lg" >
        <table class="table table--light style--two bg-white" >
            <thead >
                <tr>
                    <th>S.No.</th>
                    <th>Opening Balance</th>
                    <th>Closing Balance</th>
                    <th>Debit</th>
                    <th>Credit</th>
                    <th>Amount</th>
                    <th>Source</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $index => $transaction)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ showAmount($transaction->opening_balance) }}</td>
                        <td>{{ showAmount($transaction->closing_balance) }}</td>
                        <td>{{ showAmount($transaction->debit) }}</td>
                        <td>{{ showAmount($transaction->credit) }}</td>
                        <td>{{ showAmount($transaction->amount) }}</td>
                        <td>
                            <a href="#" wire:click="redirectDataModel({{ $transaction->module_id }},'{{ $transaction->data_model }}')">{{ $transaction->source }}</a>
                        </td>
                        <td>{{ showDateTime($transaction->created_at, 'd M, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>
