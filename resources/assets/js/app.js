document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-laravel-backup-panel-toast]').forEach((element) => {
        window.bootstrap.Toast.getOrCreateInstance(element, { delay: 5000 }).show();
    });

    const modalElement = document.getElementById('laravel-backup-panel-delete-modal');
    const confirmationForm = document.getElementById('laravel-backup-panel-delete-confirmation');
    const message = document.querySelector('[data-laravel-backup-panel-delete-message]');

    if (!(modalElement instanceof HTMLElement) || !(confirmationForm instanceof HTMLFormElement) || !(message instanceof HTMLElement) || message.dataset.messageTemplate === undefined) {
        throw new Error('Laravel Backup Panel delete confirmation markup is invalid.');
    }

    const modal = window.bootstrap.Modal.getOrCreateInstance(modalElement);

    document.querySelectorAll('[data-laravel-backup-panel-delete-form]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
            const backupName = form.dataset.backupName;

            if (backupName === undefined) {
                throw new Error('Laravel Backup Panel delete form is missing a backup name.');
            }

            confirmationForm.action = form.action;
            message.textContent = message.dataset.messageTemplate.replace(':backup', backupName);
            modal.show();
        });
    });
});
