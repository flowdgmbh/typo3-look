/**
 * Runs in the backend page: receives the content height of the sandboxed preview iframes
 * (see iFramePreview.js) and resizes them. The iframes have an opaque origin, so they cannot
 * touch this document themselves.
 */
const MESSAGE_TYPE = 'flowd-look-content-preview-height';

window.addEventListener('message', (event) => {
    const data = event.data;
    if (!data || data.type !== MESSAGE_TYPE || typeof data.height !== 'number' || !Number.isFinite(data.height)) {
        return;
    }
    for (const iframe of document.querySelectorAll('iframe.look-content-preview')) {
        if (iframe.contentWindow === event.source) {
            iframe.style.height = Math.max(0, data.height) + 'px';
            return;
        }
    }
});
