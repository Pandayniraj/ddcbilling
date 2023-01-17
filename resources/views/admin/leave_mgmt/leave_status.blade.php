
    <div class="panel panel-custom">
        <!-- Default panel contents -->
        <div class="panel-heading bg-info">
            <div class="panel-title leave_stat" title="{{ $userIdStatus->first_name }} {{ $userIdStatus->last_name }} Leave Balance Stats">
                {{ $userIdStatus->first_name }} {{ $userIdStatus->last_name }} leave balance stats
            </div >
        </div>
        <table class="table table-striped">
            <tbody>


            <?php $leaves = 0; $totalLeaves = 0;

           ?>
            @foreach($categories->where('leave_flow','static')->whereIn('leave_category_id',$mystaticLeave) as $cv)
            <tr>
                <td>{{ $cv->leave_category }}</td>
                <?php
                    $leavesTaken = \TaskHelper::userLeave($userIdStatus->id, $cv->leave_category_id, date('Y'));
                    $leaves += $leavesTaken;
                    $totalLeaves += ($cv->leave_quota - $leavesTaken)
                ?>
                <td>{{  $cv->leave_quota - $leavesTaken  }}</td>
            </tr>
            @endforeach

            @foreach($categories->where('leave_flow','dynamic') as $cv)
            <tr>
                <td><span class="material-icons">
{{ $cv->icon }}
</span>{{ $cv->leave_category }}</td>
                 <td>{{ \TaskHelper::userLeave($userIdStatus->id, $cv->leave_category_id, date('Y')) }} days</td>
            </tr>
            @endforeach

        <tr class="bg-info">
                <td><span class="material-icons">update</span> Carry forward</td>
                <td> {{ TaskHelper::getCarryForwardLeave($userIdStatus->id) }} days</td>
            </tr>
            <?php  $timeoff = \TaskHelper::checkTimeOffMonthly($userIdStatus->id); ?>
            <tr>
                <td style="">
                    <span class="material-icons">alarm_on</span>
 Remaining Time Off <i data-toggle="tooltip"  data-original-title="120 minutes will be allowed monthly and deducted as you go along" class="fa fa-info-circle"></i>
                </td>
                <td style=""> {{ 120 - $timeoff }} Min. </td>
            </tr>




            </tbody>
        </table>
    </div>
