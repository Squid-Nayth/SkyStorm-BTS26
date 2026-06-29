@extends('layouts.app')

@section('content')
<div style="max-width: 1000px; margin: 0 auto;">
    <div class="mb-4">
        <h2 class="mb-1">Administration des signalements</h2>
        <p class="text-muted mb-0">Le premier compte créé devient administrateur pour gérer cette page.</p>
    </div>

    @forelse($reports as $report)
        <div class="ss-card p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <div class="fw-semibold">Signalement #{{ $report->id }}</div>
                    <div class="small text-muted">
                        Signalé par {{ $report->user->name }} sur un post de {{ $report->post->user->name }}
                    </div>
                    <div class="small text-muted">
                        Statut actuel : <strong>{{ $report->status }}</strong>
                        @if($report->reviewed_at)
                            · traité le {{ $report->reviewed_at->format('d/m/Y H:i') }}
                        @endif
                    </div>
                </div>
                <span class="badge {{ $report->status === 'pending' ? 'text-bg-warning' : ($report->status === 'accepted' ? 'text-bg-danger' : 'text-bg-success') }}">
                    {{ $report->status }}
                </span>
            </div>

            <div class="mt-3">
                <div class="small fw-semibold">Raison</div>
                <p class="mb-2">{{ $report->reason }}</p>
                <div class="small fw-semibold">Post concerné</div>
                <p class="mb-0">{{ $report->post->content }}</p>
            </div>

            <form action="{{ route('admin.reports.update', $report) }}" method="POST" class="mt-3">
                @csrf
                @method('PATCH')
                <div class="row g-2">
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="accepted" {{ $report->status === 'accepted' ? 'selected' : '' }}>Accepter</option>
                            <option value="rejected" {{ $report->status === 'rejected' ? 'selected' : '' }}>Rejeter</option>
                        </select>
                    </div>
                    <div class="col-md-7">
                        <input type="text" name="admin_note" value="{{ $report->admin_note }}" class="form-control"
                               placeholder="Note de modération (optionnelle)">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Mettre à jour</button>
                    </div>
                </div>
            </form>
        </div>
    @empty
        <div class="ss-card p-4 text-center text-muted">Aucun signalement pour l'instant.</div>
    @endforelse
</div>
@endsection
