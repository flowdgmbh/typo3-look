function ready(fn) {
    if (document.readyState !== "loading"){
        fn();
    } else {
        document.addEventListener('DOMContentLoaded', fn);
    }
}

if (window.frameElement) {
    const bodyResizeObserver = new ResizeObserver( entries => {
        for (let entry of entries) {
            window.frameElement.style.height = (window.getComputedStyle(document.body).zoom * entry.contentRect.height) + 'px';
        }
    });
    bodyResizeObserver.observe(document.body);

    const documentResizeObserver = new ResizeObserver( entries => {
        for (let entry of entries) {
            if (window.document.documentElement.scrollHeight > window.document.documentElement.clientHeight) {
                window.document.body.style.setProperty('--content-fade-overlay-display', 'block');
            } else {
                window.document.body.style.removeProperty('--content-fade-overlay-display');
            }
        }
    });
    documentResizeObserver.observe(window.document.documentElement);
}

ready(() => {
    window.document.body.style.setProperty('--preview-scale', window.document.body.dataset.previewScale);
});
