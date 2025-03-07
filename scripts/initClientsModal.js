MicroModal.init();

function clearUrlAndClose() {
    let url = new URL(window.location.href);
    url.searchParams.delete('edit-product');
    window.history.pushState({}, '', url);
    MicroModal.close('edit-modal');
}

document.querySelectorAll('.open').forEach((modal)=>{
    MicroModal.show(
        modal.getAttribute('id'), {
            onClose: (modal) => {
                const url = new URL(document.location);
                const searchParams = url.searchParams;
                searchParams.delete('send-email');
                window.history.pushState({}, '', url.toString());
            }
        });
})

