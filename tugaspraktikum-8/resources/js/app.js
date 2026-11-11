import './bootstrap';

const loanForm = document.querySelector('[data-ajax-form]');
const loanModalElement = document.getElementById('loanModal');
const loanAlert = document.getElementById('loan-alert');

function showLoanAlert(message, success = false) {
    if (!loanAlert) return;
    loanAlert.textContent = message;
    loanAlert.classList.remove('d-none', 'alert-success', 'alert-danger');
    loanAlert.classList.add(success ? 'alert-success' : 'alert-danger');
}

function clearLoanErrors() {
    loanForm?.querySelectorAll('[data-error]').forEach((field) => {
        field.textContent = '';
        field.closest('.field')?.classList.remove('has-error');
    });
    loanForm?.querySelectorAll('.is-invalid').forEach((field) => field.classList.remove('is-invalid'));
}

function showValidationErrors(errors = {}) {
    clearLoanErrors();
    const messages = [];
    Object.entries(errors).forEach(([name, values]) => {
        const message = values[0];
        messages.push(message);
        const error = loanForm?.querySelector(`[data-error="${name}"]`);
        if (error) {
            error.textContent = message;
            error.closest('.field')?.classList.add('has-error');
            loanForm.querySelector(`[name="${name}"]`)?.classList.add('is-invalid');
        }
    });
    showLoanAlert(messages.join(' ') || 'Terjadi kesalahan. Silakan coba lagi.');
}

function setLoanFormMode(button) {
    if (!loanForm || !loanModalElement) return;
    clearLoanErrors();
    loanForm.reset();
    loanAlert?.classList.add('d-none');
    const isEditing = button.hasAttribute('data-edit-loan');
    const method = document.getElementById('loan-method');
    const statusField = document.getElementById('loan-status-field');
    const title = document.getElementById('loanModalLabel');
    const submit = document.getElementById('loan-submit');

    loanForm.action = isEditing ? button.dataset.updateUrl : loanForm.dataset.storeUrl;
    method.value = isEditing ? 'PUT' : 'POST';
    statusField?.classList.toggle('d-none', !isEditing);
    title.textContent = isEditing ? 'Edit peminjaman' : 'Pinjam buku';
    submit.textContent = isEditing ? 'Simpan perubahan' : 'Simpan peminjaman';

    if (isEditing) {
        document.getElementById('loan-book').value = button.dataset.bookId;
        document.getElementById('loan-date').value = button.dataset.loanDate;
        document.getElementById('due-date').value = button.dataset.dueDate;
        document.getElementById('loan-status').value = button.dataset.status;
        const user = document.getElementById('loan-user');
        if (user) user.value = button.dataset.userId;
    }
}

document.addEventListener('click', (event) => {
    const formButton = event.target.closest('[data-new-loan], [data-edit-loan]');
    if (formButton) setLoanFormMode(formButton);

    const deleteButton = event.target.closest('[data-delete-loan]');
    if (deleteButton) deleteLoan(deleteButton);
});

loanForm?.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearLoanErrors();
    const submit = document.getElementById('loan-submit');
    submit.disabled = true;
    try {
        const response = await fetch(loanForm.action, {
            method: 'POST',
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(loanForm),
        });
        const result = await response.json();
        if (!response.ok) {
            if (response.status === 422) showValidationErrors(result.errors);
            else showLoanAlert(result.message || 'Peminjaman gagal diproses.');
            return;
        }

        const tableBody = document.getElementById('loans-table-body');
        document.querySelector('.empty-row')?.remove();
        const currentRow = document.getElementById(`loan-row-${result.id}`);
        if (currentRow) currentRow.outerHTML = result.row_html;
        else tableBody.insertAdjacentHTML('afterbegin', result.row_html);
        updateBookStockOptions(result.stocks);
        bootstrap.Modal.getOrCreateInstance(loanModalElement).hide();
        showLoanAlert(result.message, true);
    } catch (error) {
        showLoanAlert('Tidak dapat menghubungi server. Periksa koneksi lalu coba lagi.');
        console.error(error);
    } finally {
        submit.disabled = false;
    }
});

async function deleteLoan(button) {
    if (!window.confirm('Hapus peminjaman ini?')) return;
    button.disabled = true;
    try {
        const response = await fetch(button.dataset.deleteLoan, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        });
        const result = await response.json();
        if (!response.ok) {
            showLoanAlert(result.message || 'Peminjaman gagal dihapus.');
            return;
        }
        document.getElementById(button.dataset.row)?.remove();
        if (!document.querySelector('#loans-table-body tr')) {
            const columns = window.loanConfig?.admin ? 7 : 6;
            document.getElementById('loans-table-body').innerHTML =
                `<tr class="empty-row"><td class="empty-state" colspan="${columns}">Belum ada peminjaman. Mulai dengan menambahkan peminjaman baru.</td></tr>`;
        }
        showLoanAlert(result.message, true);
    } catch (error) {
        showLoanAlert('Tidak dapat menghubungi server. Periksa koneksi lalu coba lagi.');
        console.error(error);
    } finally {
        button.disabled = false;
    }

    function updateBookStockOptions(stocks = {}) {
        const select = document.getElementById('loan-book');
        if (!select) return;
        Object.entries(stocks).forEach(([id, stock]) => {
            const option = select.querySelector(`option[value="${id}"]`);
            if (option) option.textContent = `${option.dataset.title} — ${stock} tersedia`;
        });
    }
}
