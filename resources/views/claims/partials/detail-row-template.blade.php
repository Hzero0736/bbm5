<tr class="detail-row">
    <td>
        <input type="date" name="details[INDEX][tanggal]" class="form-control" required>
    </td>
    <td>
        <input type="number" name="details[INDEX][km]" class="form-control" required>
    </td>
    <td>
        <input type="number" step="0.01" name="details[INDEX][liter]" class="form-control liter" required>
    </td>
    <td>
        <select name="details[INDEX][bbm_id]" class="form-control" required>
            @foreach($bbms as $bbm)
            <option value="{{ $bbm->id }}">{{ $bbm->nama_bbm }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="text" class="form-control total-harga" readonly>
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-sm delete-row">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>