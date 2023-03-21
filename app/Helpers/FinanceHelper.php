<?php

namespace App\Helpers;

use App\Models\Client;
use App\Models\COAgroups;
use App\Models\COALedgers;
use App\Models\Entry;
use App\Models\Entryitem;
use App\Models\Fiscalyear;
use Illuminate\Support\Facades\DB;

class FinanceHelper
{
    public static function cur_fisc_yr()
    {
        $current_yr = Fiscalyear::where('current_year', '1')
            ->where('org_id', \Auth::user()->org_id)
            ->first();

        return $current_yr;
    }

    public static function get_last_entry_number($entryType)
    {

        $lastEntry = Entry::select('id')->orderBy('id', 'desc')->first()->id + 1;
        $entryType = \App\Models\Entrytype::find($entryType);
        return $entryType->code . '-' . $lastEntry;
    }


    public static function cur_leave_yr()
    {
        $leave_yr = \App\Models\Leaveyear::where('current_year', '1')
            ->where('org_id', \Auth::user()->org_id)
            ->first();

        return $leave_yr;
    }

    public static function get_ledger_id($name, $org_id = false)
    {
        if (!$org_id) {
            $org_id = \Auth::user()->org_id;
        }
        $name = trim($name);
        $ledger = \App\Models\LedgerSettings::where('org_id', $org_id)
            ->where(DB::raw("REPLACE(TRIM(ledger_name),'\r\n','')"), $name)
            ->first();
        if ($ledger) {
            return $ledger->ledger_id;
        } else {
            return null;
        }
    }

    public static function get_group_id($name, $org_id = false)
    {
        if (!$org_id) {
            $org_id = \Auth::user()->org_id;
        }
        $group = COAgroups::where([['name', $name], ['org_id', $org_id]])->first();
        if ($group) return $group->id;
        else return null;
    }

    public static function getNextCodeLedgers($id, $org_id = false)
    {
        if (!$org_id) {
            $org_id = \Auth::user()->org_id;
        }
        $group_data = COAgroups::find($id);
        $group_code = $group_data->code;
        $q = COALedgers::where('group_id', $id)->where('org_id', $org_id)->where('code', '!=', 'null')->get();
        if ($q) {
            $last = $q->last();
            $last = $last->code;
            $l_array = explode('-', $last);
            $new_index = end($l_array);
            $new_index += 1;
            $new_index = sprintf('%04d', $new_index);

            return $group_code . '-' . $new_index;
        } else {
            return $group_code . '-0001';
        }
    }

    public static function get_entry_type_id($label, $org_id = false)
    {
        if (!$org_id) {
            $org_id = \Auth::user()->org_id;
        }

        $entrytype = \App\Models\Entrytype::where(DB::raw("REPLACE(TRIM(label),'\r\n','')"), $label)->first();

        return $entrytype->id ?? null;
    }

    public static function get_entry_type_label($id, $org_id = false)
    {
        if (!$org_id) {
            $org_id = \Auth::user()->org_id;
        }

        $entrytype = \App\Models\Entrytype::where(DB::raw("REPLACE(TRIM(id),'\r\n','')"), $id)
            ->where('org_id', $org_id)
            ->first();

        return $entrytype->label ?? null;
    }

    public static function get_entry_type_name($id, $org_id = false)
    {
        if (!$org_id) {
            $org_id = \Auth::user()->org_id;
        }

        $entrytype = \App\Models\Entrytype::where(DB::raw("REPLACE(TRIM(id),'\r\n','')"), $id)
            ->where('org_id', $org_id)
            ->first();

        return $entrytype->name ?? null;
    }

    public static function ledgerGroupsOptionshtml($id = false, $parents_id = null)
    {
        $groups = \App\Models\COAgroups::orderBy('code', 'asc')
            ->where('parent_id', $parents_id)
            ->where('org_id', \Auth::user()->org_id)
            ->where(function ($query) use ($id) {
                if ($id) {
                    return $query->where('id', $id);
                }
            })
            ->get();

        foreach ($groups as $key => $child_group) {
            echo "<optgroup  label='{$child_group->name}'>";
            $ledgers = \App\Models\COALedgers::orderBy('code', 'asc')->where('group_id', $child_group->id)->get();
            foreach ($ledgers as $key => $value) {
                echo "<option value = '{$value->id}'>[{$value->code}] {$value->name} </option>";
            }
            echo '</optgroup>';
            self::ledgerGroupsOptionshtml(false, $child_group->id);
        }
    }

    private static function calculateledgerbalance($groupid, $fiscal_year, $item = [])
    {
        $ledgers = \App\Models\COALedgers::orderBy('code', 'asc')->where('group_id', $groupid)->get();

        foreach ($ledgers as $key => $value) {
            $entries = \App\Models\Entryitem::select('entryitems.*')
                ->where('ledger_id', $value->id)
                ->leftjoin('entries', 'entries.id', '=', 'entryitems.entry_id')
                ->where('entries.fiscal_year_id', $fiscal_year)
                ->groupBy('entryitems.id')
                ->get();

            $totalbalance = 0;

            if (count($entries) > 0) {
                foreach ($entries as $entry) {
                    $totalbalance = $totalbalance + $entry->amount;
                }
            }

            $item[] = ['name' => $value->name, 'y' => $totalbalance];
        }

        return $item;
    }

    public static function calculateLedgerGroupOpeningBalance(
        $parent_id,
        $fiscal_year,
        $item = [],
        $dept = 0
    )
    {
        $groups = \App\Models\COAgroups::orderBy('code', 'asc')
            ->where('parent_id', $parent_id)
            ->where('org_id', \Auth::user()->org_id)
            ->get();

        if ($dept == 0) {
            $item = self::calculateledgerbalance($parent_id, $fiscal_year, $item);
        }

        foreach ($groups as $key => $child_group) {
            $item = self::calculateledgerbalance($child_group->id, $fiscal_year, $item);

            $dept++;

            if ($dept > 100) { //stop infinite loop when wrong id is passed
                return $item;
            }

            self::calculateLedgerGroupOpeningBalance($child_group->id, $fiscal_year, $item, $dept);
        }

        return $item;
    }

    public static function getAccountingPrefix($name)
    {
        $value = \App\Models\AccountingPrefix::where('name', $name)->first()->value;

        return $value;
    }

    private static function getPaisa($number)

    {

        $no = round($number);
        $decimal = round($number - ($no = floor($number)), 2) * 100;
        $words = array(

            0 => '',
            1 => 'One',
            2 => 'Two',
            3 => 'Three',
            4 => 'Four',
            5 => 'Five',

            6 => 'Six',

            7 => 'Seven',

            8 => 'Eight',
            9 => 'Nine',

            10 => 'Ten',

            11 => 'Eleven',

            12 => 'Twelve',

            13 => 'Thirteen',

            14 => 'Fourteen',

            15 => 'Fifteen',

            16 => 'Sixteen',

            17 => 'Seventeen',

            18 => 'Eighteen',

            19 => 'Nineteen',

            20 => 'Twenty',

            30 => 'Thirty',
            40 => 'Forty',
            50 => 'Fifty',
            60 => 'Sixty',
            70 => 'Seventy',
            80 => 'Eighty',
            90 => 'Ninety');

        $paise = ($decimal) ? ' and ' . ($words[$decimal - $decimal % 10]) . " " . ($words[$decimal % 10]) . ' Paisa' : '';

        return $paise;

    }

    public static function numberRupeeFomatter($number)

    {

        $constnum = $number;

        $no = floor($number);

        $point = round($number - $no, 2) * 100;

        $hundred = null;

        $digits_1 = strlen($no);

        $i = 0;

        $str = array();

        $words = array('0' => '', '1' => 'one',

            '2' => 'two',
            '3' => 'three',
            '4' => 'four', '5' => 'five', '6' => 'six',
            '7' => 'seven', '8' => 'eight', '9' => 'nine',

            '10' => 'ten', '11' => 'eleven', '12' => 'twelve',

            '13' => 'thirteen', '14' => 'fourteen',

            '15' => 'fifteen', '16' => 'sixteen',

            '17' => 'seventeen',

            '18' => 'eighteen',

            '19' => 'nineteen',

            '20' => 'twenty',

            '30' => 'thirty',

            '40' => 'forty',

            '50' => 'fifty',
            '60' => 'sixty',
            '70' => 'seventy',
            '80' => 'eighty',
            '90' => 'ninety');

        $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');

        while ($i < $digits_1) {

            $divider = ($i == 2) ? 10 : 100;
            $number = floor($no % $divider);
            $no = floor($no / $divider);
            $i += ($divider == 10) ? 1 : 2;

            if ($number) {

                $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                $hundred = ($counter == 1 && $str[0]) ? '' : null;
                $str [] = ($number < 21) ? $words[$number] .

                    " " . $digits[$counter] . $plural . " " . $hundred

                    :

                    $words[floor($number / 10) * 10]
                    . " " . $words[$number % 10] . " "
                    . $digits[$counter] . $plural . " " . $hundred;

            } else $str[] = null;

        }

        $str = array_reverse($str);
        $result = implode('', $str);
        $points = self::getPaisa($constnum);

        return $result . ' Rupees' . $points;
    }

    public static function depositEntry($attributes)
    {
        $client = Client::find($attributes['client_id']);
        $attributes['entrytype_id'] = FinanceHelper::get_entry_type_id('cash'); //cash
        $attributes['tag_id'] = '1'; //Deposit
        $attributes['user_id'] = auth()->id();
        $attributes['org_id'] = auth()->user()->org_id;
        $attributes['number'] = FinanceHelper::get_last_entry_number($attributes['entrytype_id']);
        $attributes['date'] = \Carbon\Carbon::today();
        $attributes['dr_total'] = $attributes['amount'];
        $attributes['cr_total'] = $attributes['amount'];
        $attributes['source'] = "Customer Deposit";
        $attributes['fiscal_year_id'] = FinanceHelper::cur_fisc_yr()->id;
        $entry = Entry::create($attributes);
        Entryitem::where('entry_id', $entry->id)->delete();

        Entryitem::create([
            'entry_id' => $entry->id,
            'dc' => 'D',
            'ledger_id' => FinanceHelper::get_ledger_id('DEPOSIT_LEDGER'),
            'amount' => $attributes['amount'],
            'narration' => 'Deposit Being Made',
        ]);
        Entryitem::create([
            'entry_id' => $entry->id,
            'dc' => 'C',
            'ledger_id' => $client->ledger_id,
            'amount' => $attributes['amount'],
            'narration' => 'Deposit Amount',
        ]);
        return $entry->id;
    }
}
