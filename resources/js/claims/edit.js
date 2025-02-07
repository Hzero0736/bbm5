class ClaimForm {
    constructor(data) {
        this.detailIndex = data.detailCount;
        this.hargaBbm = data.hargaBbm;
        this.templateRow = data.templateRow;
        
        this.initEvents();
    }

    initEvents() {
        $('#add-detail').on('click', () => this.addNewRow());
        $('#detail-table').on('click', '.delete-row', (e) => this.deleteRow(e));
        $('#detail-table').on('input', '.liter', (e) => this.calculateTotal(e));
    }

    addNewRow() {
        const newRow = $(this.templateRow.replace(/INDEX/g, this.detailIndex));
        $('#detail-container').append(newRow);
        this.detailIndex++;
    }

    deleteRow(e) {
        if($('.detail-row').length > 1) {
            $(e.target).closest('.detail-row').remove();
        }
    }

    calculateTotal(e) {
        const input = $(e.target);
        const liter = parseFloat(input.val()) || 0;
        const total = this.hargaBbm * liter;
        
        input.closest('.detail-row')
             .find('.total-harga')
             .val(`Rp ${total.toLocaleString('id-ID')}`);
    }
}

// Initialize
$(document).ready(() => {
    new ClaimForm(CLAIM_DATA);
});
