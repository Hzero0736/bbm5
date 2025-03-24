@extends('layouts.app')

@section('title', 'Persetujuan User')
@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1><i class="fas fa-user-check"></i> Persetujuan User</h1>
    </div>
</div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar User</h3>
        <div class="card-tools">
            <ul class="nav nav-pills ml-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#menunggu" data-toggle="tab">
                        <i class="fas fa-clock"></i> Menunggu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#disetujui" data-toggle="tab">
                        <i class="fas fa-check"></i> Disetujui
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#ditolak" data-toggle="tab">
                        <i class="fas fa-times"></i> Ditolak
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="card-body">
        <div class="tab-content">
            <div class="tab-pane active" id="menunggu">
                @include('admin.partials.user-table', ['users' => $pendingUsers, 'status' => 'menunggu'])
            </div>
            <div class="tab-pane" id="disetujui">
                <div class="mb-3">
                    <button class="btn btn-danger bulk-delete" data-status="disetujui">
                        <i class="fas fa-trash"></i> Hapus Yang Dipilih
                        <span class="selected-count"></span>
                    </button>
                </div>
                @include('admin.partials.user-table', ['users' => $approvedUsers, 'status' => 'disetujui'])
            </div>
            <div class="tab-pane" id="ditolak">
                <div class="mb-3">
                    <button class="btn btn-danger bulk-delete" data-status="ditolak">
                        <i class="fas fa-trash"></i> Hapus Yang Dipilih
                        <span class="selected-count"></span>
                    </button>
                </div>
                @include('admin.partials.user-table', ['users' => $rejectedUsers, 'status' => 'ditolak'])
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alasan Penolakan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>×</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reject-user-id">
                <div class="form-group">
                    <label>Alasan Penolakan</label>
                    <textarea class="form-control" id="reject-reason" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmReject">Tolak</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Delete single user handler
        $('.delete-btn').on('click', function() {
            let id = $(this).data('id');
            deleteUsers([id]);
        });

        // Select all checkbox per tab
        $('.tab-content').on('change', '#select-all', function() {
            let activeTab = $(this).closest('.tab-pane');
            let isChecked = $(this).prop('checked');
            activeTab.find('.user-checkbox').prop('checked', isChecked);
            updateSelectedCount(activeTab);
        });

        // Individual checkbox
        $('.tab-content').on('change', '.user-checkbox', function() {
            let activeTab = $(this).closest('.tab-pane');
            updateSelectedCount(activeTab);

            // Update select all checkbox
            let allChecked = activeTab.find('.user-checkbox:not(:checked)').length === 0;
            activeTab.find('#select-all').prop('checked', allChecked);
        });

        // Bulk delete handler
        $('.bulk-delete').on('click', function() {
            let activeTab = $(this).closest('.tab-pane');
            let selectedIds = activeTab.find('.user-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Pilih minimal satu user'
                });
                return;
            }

            deleteUsers(selectedIds);
        });

        // Tab change handler
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            $('.user-checkbox').prop('checked', false);
            $('#select-all').prop('checked', false);
            $('.selected-count').text('');
        });

        // Approve handler
        $('.approve-btn').on('click', function() {
            let id = $(this).data('id');
            approveUser(id);
        });

        // Reject button handler
        $('.btn-reject').on('click', function() {
            let userId = $(this).data('id');
            $('#reject-user-id').val(userId);
            $('#rejectModal').modal('show');
        });

        // Confirm reject handler
        $('#confirmReject').on('click', function() {
            let userId = $('#reject-user-id').val();
            let reason = $('#reject-reason').val();
            rejectUser(userId, reason);
        });

        // Helper Functions
        function updateSelectedCount(activeTab) {
            let count = activeTab.find('.user-checkbox:checked').length;
            activeTab.find('.selected-count').text(count > 0 ? `(${count} dipilih)` : '');
        }

        function deleteUsers(ids) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: `${ids.length} data user akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("users.destroy") }}',
                        type: 'POST',
                        data: {
                            ids: ids,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data'
                            });
                        }
                    });
                }
            });
        }

        function approveUser(id) {
            Swal.fire({
                title: 'Konfirmasi Persetujuan',
                text: "Apakah anda yakin menyetujui user ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/users/${id}/approve`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat menyetujui user'
                            });
                        }
                    });
                }
            });
        }

        function rejectUser(userId, reason) {
            if (!reason) {
                toastr.error('Alasan penolakan harus diisi');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Penolakan',
                text: "Apakah anda yakin ingin menolak user ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Tolak!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/users/${userId}/reject`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            rejection_reason: reason
                        },
                        success: function(response) {
                            $('#rejectModal').modal('hide');
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Terjadi kesalahan saat menolak user'
                            });
                        }
                    });
                }
            });
        }
    });
</script>
@endpush
@endsection