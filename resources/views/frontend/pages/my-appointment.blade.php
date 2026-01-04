@extends('frontend.layout.master')

@section('content')
<div class="container my-5">
    <h2 class="text-center mb-4">📅 My Appointments</h2>

    {{-- 🔹 Filter Links --}}
    <div class="text-center mb-3">
        <a href="javascript:void(0)" class="btn btn-outline-secondary filter-btn" data-status="All">All</a>
        <a href="javascript:void(0)" class="btn btn-outline-danger filter-btn" data-status="Rejected">Rejected</a>
        <a href="javascript:void(0)" class="btn btn-outline-success filter-btn" data-status="Completed">Completed</a>
        <a href="javascript:void(0)" class="btn btn-outline-warning filter-btn" data-status="Pending">Missed </a>
    </div>

    {{-- 🔹 Flash Messages --}}
    @foreach (['success', 'error', 'warning', 'info'] as $msg)
        @if(session($msg))
            <div class="alert alert-{{ $msg }} alert-dismissible fade show text-center" role="alert">
                {{ session($msg) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    @endforeach

    @if($appointments->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center" id="appointmentsTable">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Astrologer</th>
                        <th>Status</th>
                        <th>Call Type</th>
                        <th>Schedule Date</th>
                        <th>Schedule Time</th>
                        <th>Call Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $index => $appointment)
                        <tr data-status="{{ $appointment->callStatus }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img class="rounded-m" src="{{ Str::startsWith($appointment->profileImage, ['http://','https://']) ? $appointment->profileImage : '/' . $appointment->profileImage }}" onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Customer image" onclick="openImage('{{ $appointment->profileImage }}')" />
                                {{ $appointment->astrologerName }}
                            </td>
                            <td>
                                <span class="badge 
                                    @if($appointment->callStatus == 'Pending') bg-warning 
                                    @elseif($appointment->callStatus == 'Completed') bg-success 
                                    @elseif($appointment->callStatus == 'Rejected') bg-danger 
                                    @elseif($appointment->IsSchedule == 1) bg-info 
                                    @else bg-secondary @endif">
                                    {{ $appointment->IsSchedule == 1 ? 'Scheduled' : $appointment->callStatus }}
                                </span>
                            </td>
                            <td>{{ $appointment->call_type == 10 ? 'Audio Call' : 'Video Call' }}</td>
                            <td>{{ $appointment->schedule_date ?? '-' }}</td>
                            <td>{{ $appointment->schedule_time ?? '-' }}</td>
                            <td>{{ $appointment->callStatus ?? '-' }}</td>
                            <td>
                                <form action="{{ route('appointment.delete', $appointment->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="alert alert-info text-center">
            No appointments found.
        </div>
    @endif
</div>

{{-- 🔹 Filter Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const buttons = document.querySelectorAll('.filter-btn');
    const rows = document.querySelectorAll('#appointmentsTable tbody tr');

    buttons.forEach(btn => {
        btn.addEventListener('click', function() {
            const status = this.dataset.status;

            rows.forEach(row => {
                if (status === 'All' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endsection
