<div class="table-responsive">
    <table class="table table-hover" id="logTable">
        <thead>
            <tr>
                <th>
                    ID
                </th>
                <th>
                    Date
                </th>
                <th>
                    User
                </th>
                <th>
                    Action
                </th>
                <th>
                    Notes
                </th>
                <th>
                    Log level
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($logs as $log)
                <tr>
                    <td>
                        <p class="text-muted">{{ $log->id }}</p>
                    </td>
                    <td>
                        <p class="text-muted">{{ $log->created_at->format('d-m-Y H:i') }}</p>
                    </td>
                    <td>
                        <p class="text-muted">{{ $log->Profile->name }}</p>
                    </td>
                    <td>
                        <p class="text-muted">{{ $log->action }}</p>
                    </td>
                    <td>
                        <p class="text-muted">{{ $log->notes }}</p>
                    </td>
                    <td>
                        <p class="text-muted">{{ $log->log_level }}</p>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>