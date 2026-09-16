/**
 * Runs inside the sandboxed preview iframe: reports the content height to the backend page
 * (ContentPreviewHost.js) and toggles the fade-out overlay. The frame has no access to the parent
 * document.
 */
const MESSAGE_TYPE = 'flowd-look-content-preview-height';

if (window.parent !== window) {
    // the host resizes the iframe on every message, which can resize the body again; only report changes
    let reportedHeight = null;
    const bodyResizeObserver = new ResizeObserver((entries) => {
        for (const entry of entries) {
            const zoom = parseFloat(window.getComputedStyle(document.body).zoom) || 1;
            const height = Math.round(zoom * entry.contentRect.height * 100) / 100;
            if (height === reportedHeight) {
                continue;
            }
            reportedHeight = height;
            window.parent.postMessage({ type: MESSAGE_TYPE, height }, '*');
        }
    });
    bodyResizeObserver.observe(document.body);

    const documentResizeObserver = new ResizeObserver(() => {
        if (document.documentElement.scrollHeight > document.documentElement.clientHeight) {
            document.body.style.setProperty('--content-fade-overlay-display', 'block');
        } else {
            document.body.style.removeProperty('--content-fade-overlay-display');
        }
    });
    documentResizeObserver.observe(document.documentElement);
}
