<tr id="loan-row-{{ $loan->id }}">
    @if (auth()->user()->isAdmin())
        <td><strong>{{ $loan->user->name }}</strong><span class="d-block muted small">{{ $loan->user->email }}</span></td>
    @endif
    <td>
        <strong>{{ $loan->book->title }}</strong>
        <span class="d-block muted small">{{ $loan->book->author }}</span>
    </td>
    <td>{{ $loan->loan_date->format('d M Y') }}</td>
    <td>{{ $loan->due_date->format('d M Y') }}</td>
    <td>
        <span class="status-badge {{ $loan->status === 'returned' ? 'status-returned' : 'status-borrowed' }}">
            {{ $loan->status === 'returned' ? 'Dikembalikan' : 'Dipinjam' }}
        </span>
    </td>
    <td>
        @if ($loan->proof_path)
            <a href="{{ asset('storage/'.$loan->proof_path) }}" target="_blank" rel="noopener">Lihat bukti</a>
        @else
            <span class="muted">—</span>
        @endif
    </td>
    <td>
        <div class="action-group">
            <button
                class="btn btn-sm btn-outline-secondary"
                type="button"
                data-bs-toggle="modal"
                data-bs-target="#loanModal"
                data-edit-loan
                data-update-url="{{ route('loans.update', $loan) }}"
                data-id="{{ $loan->id }}"
                data-user-id="{{ $loan->user_id }}"
                data-book-id="{{ $loan->book_id }}"
                data-loan-date="{{ $loan->loan_date->format('Y-m-d') }}"
                data-due-date="{{ $loan->due_date->format('Y-m-d') }}"
                data-status="{{ $loan->status }}"
            >Edit</button>
            <button class="btn btn-sm btn-outline-danger" type="button" data-delete-loan="{{ route('loans.destroy', $loan) }}" data-row="loan-row-{{ $loan->id }}">Hapus</button>
        </div>
    </td>
</tr>
