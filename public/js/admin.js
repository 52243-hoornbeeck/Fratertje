"use strict";
const approvedForms = new WeakSet();
const dialog = document.getElementById("confirm-dialog");
let pendingForm = null;
let pendingSubmitter = null;
document.addEventListener("submit", function (event) {
    const form = event.target;
    if (approvedForms.has(form)) { approvedForms.delete(form); return; }
    let message = form.dataset.confirm;
    const number = form.querySelector('[name="breeding_number"]');
    if (form.dataset.currentNumber && number && !number.value.trim()) {
        message = "Weet je zeker dat je het kweeknummer wilt archiveren? Het lid moet inactief zijn. De gegevens blijven bewaard.";
    }
    if (!message) return;
    event.preventDefault();
    pendingForm = form; pendingSubmitter = event.submitter;
    document.getElementById("confirm-message").textContent = message;
    dialog.showModal();
});
document.getElementById("confirm-cancel").addEventListener("click", () => { pendingForm = null; dialog.close(); });
dialog.addEventListener("cancel", () => { pendingForm = null; });
document.getElementById("confirm-accept").addEventListener("click", () => {
    const form = pendingForm, submitter = pendingSubmitter;
    pendingForm = null; dialog.close();
    if (form) { approvedForms.add(form); if (submitter) form.requestSubmit(submitter); else form.requestSubmit(); }
});
